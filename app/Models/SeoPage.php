<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;

class SeoPage extends Model
{
    use HasFactory;

    public const MAX_DEPTH = 3;

    public const CLUSTERS = ['core', 'inner', 'outer'];

    public const TREE_CACHE_KEY = 'seo_pages_tree';

    /**
     * Category slugs the public route serves (routes/web.php) and the sitemap lists.
     * A new category needs its slug added here before its pages go live.
     */
    public const ROUTED_CATEGORIES = ['seo'];

    /**
     * First URL segments a category slug may not use (existing routes).
     * Existing `pages` slugs are checked separately in SeoPageRequest.
     */
    public const RESERVED_CATEGORY_SLUGS = [
        'admin', 'api', 'blog', 'service', 'software', 'login', 'register', 'user',
        'details', 'locale-set', 'contact', 'programmatic-seo-services', 'assets', 'build',
    ];

    protected $fillable = [
        'parent_id', 'title', 'nav_label', 'nav_group', 'slug', 'excerpt', 'content', 'faq',
        'featured_image', 'featured_image_alt', 'author_id', 'meta_title', 'meta_description',
        'canonical_url', 'primary_keyword', 'cluster', 'show_toc', 'show_cta', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'depth' => 'integer',
        'faq' => 'array',
        'show_toc' => 'boolean',
        'show_cta' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (SeoPage $page) {
            $page->slug = Str::slug($page->slug);

            $parent = $page->parent_id ? static::query()->findOrFail($page->parent_id) : null;
            $depth = $parent ? $parent->depth + 1 : 1;

            if ($depth > self::MAX_DEPTH) {
                throw new LogicException('SEO pages can only be nested '.self::MAX_DEPTH.' levels deep.');
            }

            if ($page->exists && $page->getOriginal('depth') !== null && (int) $page->getOriginal('depth') !== $depth) {
                throw new LogicException('An SEO page cannot change level; move it to a parent at the same level as its current parent.');
            }

            if ($page->isDirty('parent_id')) {
                $page->unsetRelation('parent');
            }

            $page->depth = $depth;
            $page->path = $parent ? $parent->path.'/'.$page->slug : $page->slug;

            if ($page->is_active && $page->published_at === null) {
                $page->published_at = now();
            }
        });

        static::saved(function (SeoPage $page) {
            // Saving each child re-runs its `saving` hook, which rebuilds its path
            // from ours; grandchildren follow through the same `saved` hook.
            if ($page->wasChanged('path')) {
                $page->children()->get()->each->save();
            }

            DB::afterCommit(fn () => static::clearTreeCache());
        });

        static::deleted(fn () => DB::afterCommit(fn () => static::clearTreeCache()));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('nav_label');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function url(): string
    {
        return url($this->path);
    }

    /**
     * Ancestors ordered from the category down (empty for a category).
     */
    public function ancestors(): Collection
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    public function isPubliclyVisible(): bool
    {
        return $this->is_active && $this->ancestors()->every(fn (SeoPage $ancestor) => $ancestor->is_active);
    }

    public function resolvedMetaTitle(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function resolvedMetaDescription(): string
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }

        if ($this->excerpt) {
            return $this->limitDescriptionTo160($this->excerpt);
        }

        return $this->limitDescriptionTo160($this->plainTextFromContent());
    }

    /**
     * Tags whose opening or closing form should get a space inserted before it, so
     * block-level elements don't glue adjacent words together once stripped. Inline
     * tags (a, strong, em, span, ...) are left alone so punctuation stays attached.
     */
    private const BLOCK_TAGS = 'p|div|h[1-6]|li|ul|ol|br|tr|td|th|blockquote|section|article|table';

    /**
     * Strips tags from the content, inserting a space before each block-level tag first
     * so those elements don't glue adjacent words together, then decodes HTML entities.
     */
    private function plainTextFromContent(): string
    {
        $spaced = preg_replace('/<(?=\/?(?:'.self::BLOCK_TAGS.')\b)/i', ' <', (string) $this->content);
        $decoded = html_entity_decode(strip_tags($spaced), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $decoded));
    }

    /**
     * Collapses whitespace and limits to 160 characters (multibyte-safe), cutting back
     * to the last space so no word is split. No ellipsis is appended.
     */
    private function limitDescriptionTo160(string $text): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        if (mb_strlen($text) <= 160) {
            return $text;
        }

        $truncated = mb_substr($text, 0, 160);
        $lastSpace = mb_strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = mb_substr($truncated, 0, $lastSpace);
        }

        return rtrim($truncated);
    }

    public function resolvedCanonical(): string
    {
        return $this->canonical_url ?: $this->url();
    }

    public function hasFeaturedImageFile(): bool
    {
        return $this->featured_image !== null && is_file(base_path('assets/'.$this->featured_image));
    }

    /**
     * @return array{0: int, 1: int}|null Width and height, or null if the file is missing.
     */
    public function featuredImageSize(): ?array
    {
        if (! $this->hasFeaturedImageFile()) {
            return null;
        }

        $size = @getimagesize(base_path('assets/'.$this->featured_image));

        return $size ? [$size[0], $size[1]] : null;
    }

    /**
     * Every publicly visible page (active, with all ancestors active), cached.
     * Used for the sidebar and the sitemap.
     */
    public static function visibleTree(): Collection
    {
        return Cache::rememberForever(self::TREE_CACHE_KEY, function () {
            $active = static::active()
                ->orderBy('depth')->orderBy('sort_order')->orderBy('nav_label')
                ->get(['id', 'parent_id', 'depth', 'title', 'nav_label', 'nav_group', 'slug', 'path', 'sort_order', 'updated_at'])
                ->keyBy('id');

            return $active->filter(function (SeoPage $page) use ($active) {
                $parentId = $page->parent_id;

                while ($parentId !== null) {
                    if (! $active->has($parentId)) {
                        return false;
                    }
                    $parentId = $active[$parentId]->parent_id;
                }

                return true;
            })->values();
        });
    }

    public static function clearTreeCache(): void
    {
        Cache::forget(self::TREE_CACHE_KEY);
    }
}
