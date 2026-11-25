<?php

namespace App\Http\Requests;

use App\Models\Page;
use App\Models\SeoPage;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mews\Purifier\Facades\Purifier;

class SeoPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Access is enforced by the `permission:seo-page-manage` middleware on the controller.
        return true;
    }

    protected function prepareForValidation(): void
    {
        $faq = collect($this->input('faq', []))
            ->filter(fn ($item) => is_array($item) && (filled($item['question'] ?? null) || filled($item['answer'] ?? null)))
            ->values()
            ->all();

        $this->merge([
            'parent_id' => $this->input('parent_id') ?: null,
            'slug' => Str::slug((string) ($this->input('slug') ?: $this->input('nav_label'))),
            'nav_group' => $this->input('nav_group') ?: null,
            'cluster' => $this->input('cluster') ?: null,
            'faq' => $faq,
            'show_toc' => $this->boolean('show_toc'),
            'show_cta' => $this->boolean('show_cta'),
            'is_active' => $this->boolean('is_active'),
            'sort_order' => filled($this->input('sort_order')) ? $this->input('sort_order') : 0,
        ]);
    }

    public function rules(): array
    {
        $page = $this->currentPage();
        $parentId = $this->input('parent_id');

        $slugRules = [
            'required', 'string', 'max:60',
            Rule::unique('seo_pages', 'slug')
                ->where(fn ($query) => $parentId ? $query->where('parent_id', $parentId) : $query->whereNull('parent_id'))
                ->ignore($page?->id),
        ];

        if (! $parentId) {
            $slugRules[] = Rule::notIn(self::reservedCategorySlugs());
        }

        return [
            'parent_id' => [
                'nullable', 'integer',
                Rule::exists('seo_pages', 'id')->where(fn ($query) => $query->where('depth', '<', SeoPage::MAX_DEPTH)),
            ],
            'title' => ['required', 'string', 'max:255'],
            'nav_label' => ['required', 'string', 'max:60'],
            'nav_group' => ['nullable', 'string', 'max:60'],
            'slug' => $slugRules,
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'faq' => ['array'],
            'faq.*.question' => ['required', 'string', 'max:500'],
            'faq.*.answer' => ['required', 'string', 'max:5000'],
            'featured_image' => [$page ? 'nullable' : 'required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'featured_image_alt' => ['required', 'string', 'max:125', 'not_regex:/\.(jpe?g|png|webp|gif|svg)$/i'],
            'author_id' => ['nullable', 'integer', 'exists:admins,id'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'primary_keyword' => ['nullable', 'string', 'max:255'],
            'cluster' => ['nullable', Rule::in(SeoPage::CLUSTERS)],
            'show_toc' => ['boolean'],
            'show_cta' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'parent_id.exists' => __('Pages can only be nested three levels deep.'),
            'slug.not_in' => __('This slug is already used by another part of the site.'),
            'featured_image_alt.not_regex' => __('The alt text must describe the image, not be a file name.'),
            'faq.*.question.required' => __('Every FAQ answer needs a question.'),
            'faq.*.answer.required' => __('Every FAQ question needs an answer.'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $content = $this->input('content');
            if (is_string($content) && $content !== '' && ! $validator->errors()->has('content')) {
                $clean = Purifier::clean($content, 'seo_content');
                $hasText = trim(html_entity_decode(strip_tags($clean), ENT_QUOTES | ENT_HTML5, 'UTF-8'), " \t\n\r\0\x0B\u{A0}") !== '';

                if (! $hasText && ! preg_match('/<(img|table)\b/i', $clean)) {
                    $validator->errors()->add('content', __('The content is empty.'));
                } elseif ($missing = self::imagesWithoutAlt($clean)) {
                    $validator->errors()->add('content', __('Every image in the content needs alt text (missing for: :images).', [
                        'images' => implode(', ', array_unique($missing)),
                    ]));
                }
            }

            $page = $this->currentPage();
            if (! $page) {
                return;
            }

            // Renaming a live category would take every page under it offline.
            if ($page->parent_id === null && $page->slug !== $this->input('slug')
                && in_array($page->slug, SeoPage::ROUTED_CATEGORIES, true) && $page->children()->exists()) {
                $validator->errors()->add('slug', __('This category is live; add the new slug to SeoPage::ROUTED_CATEGORIES first, or its pages will go offline.'));
            }

            // Depth is fixed: a page may only move to a parent at its current parent's level.
            $newParentDepth = $this->input('parent_id') ? (int) SeoPage::whereKey($this->input('parent_id'))->value('depth') : null;
            $currentParentDepth = $page->parent_id ? $page->depth - 1 : null;

            if ($newParentDepth !== $currentParentDepth) {
                $validator->errors()->add('parent_id', __('A page can only move to a parent at the same level as its current parent.'));
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        notifyEvs('error', $validator->errors()->first());

        parent::failedValidation($validator);
    }

    /**
     * Validated data ready to save: purified content and FAQ, without the uploaded file.
     */
    public function pageData(): array
    {
        $data = collect($this->validated())->except('featured_image')->all();

        $data['sort_order'] = (int) $data['sort_order'];
        $data['content'] = Purifier::clean($data['content'], 'seo_content');
        $data['faq'] = collect($data['faq'] ?? [])
            ->map(fn (array $item) => [
                'question' => trim(strip_tags($item['question'])),
                'answer' => Purifier::clean($item['answer'], 'seo_content'),
            ])
            ->values()
            ->all() ?: null;

        return $data;
    }

    /** An alt text that is only a file name, e.g. "IMG_0042.JPG" (a sentence mentioning one is fine). */
    public const FILE_NAME_ALT = '/^[\w.-]+\.(jpe?g|png|webp|gif|svg)$/i';

    /**
     * The file names (src base names) of images in purified HTML whose alt is missing, blank, a bare
     * file name, or the src's own file name. HTML Purifier fills a missing alt with the src's base
     * name, so an alt equal to that counts as missing too.
     *
     * @return array<int, string>
     */
    public static function imagesWithoutAlt(string $html): array
    {
        if (! preg_match('/<img\b/i', $html)) {
            return [];
        }

        $dom = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><div>'.$html.'</div>', LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $missing = [];
        foreach ($dom->getElementsByTagName('img') as $img) {
            $alt = trim(str_replace("\u{A0}", ' ', $img->getAttribute('alt')));
            $srcName = basename((string) parse_url($img->getAttribute('src'), PHP_URL_PATH));

            if ($alt === '' || preg_match(self::FILE_NAME_ALT, $alt) || ($srcName !== '' && $alt === $srcName)) {
                // Only URL-safe characters: the message is also shown through notifyEvs.
                $name = substr(preg_replace('/[^\w.%-]/', '', $srcName), 0, 80);
                $missing[] = $name !== '' ? $name : 'image without a file name';
            }
        }

        return $missing;
    }

    /**
     * First URL segments a category slug may not take: the first static segment of every
     * registered route, the admin prefix, the hand-kept SeoPage::RESERVED_CATEGORY_SLUGS
     * floor and every CMS page slug. The categories the SEO route itself serves are allowed.
     *
     * @return array<int, string>
     */
    public static function reservedCategorySlugs(): array
    {
        $routeSegments = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => explode('/', ltrim($route->uri(), '/'))[0])
            ->reject(fn (string $segment) => $segment === '' || str_starts_with($segment, '{'));

        return $routeSegments
            ->push((string) setting('admin_prefix'))
            ->merge(SeoPage::RESERVED_CATEGORY_SLUGS)
            ->merge(Page::pluck('slug'))
            ->map(fn ($slug) => strtolower((string) $slug))
            ->filter()
            ->unique()
            ->diff(SeoPage::ROUTED_CATEGORIES)
            ->values()
            ->all();
    }

    private function currentPage(): ?SeoPage
    {
        $page = $this->route('seo_page');

        return $page instanceof SeoPage ? $page : null;
    }
}
