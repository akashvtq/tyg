<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use App\Services\SeoPage\ContentImageUrlService;
use App\Services\SeoPage\NavGroupService;
use App\Services\SeoPage\SchemaBuilderService;
use App\Services\SeoPage\TableOfContentsService;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SeoPageController extends Controller
{
    public function __construct(
        private readonly TableOfContentsService $toc,
        private readonly SchemaBuilderService $schema,
        private readonly NavGroupService $nav,
        private readonly ContentImageUrlService $imageUrls,
    ) {
    }

    public function index(): View
    {
        $title = __('SEO Guides & Resources');
        $tree = SeoPage::visibleTree();

        // Level 1 Root Categories (e.g. /seo)
        $rootCategories = $tree->whereNull('parent_id')->values();

        // Level 2 Subcategories (e.g. /seo/link-building, /seo/seo-strategy, etc.)
        $subcategories = $tree->where('depth', 2)->values();

        // Full model records with author & relations
        $allActivePages = SeoPage::active()
            ->with(['author', 'children' => fn ($q) => $q->active()->orderBy('sort_order')])
            ->get()
            ->keyBy('id');

        // Build structure: for each subcategory, attach its topics & sub-groups
        $sections = $subcategories->map(function (SeoPage $treeSubcat) use ($allActivePages, $tree) {
            $fullSubcat = $allActivePages->get($treeSubcat->id);
            if (!$fullSubcat) {
                return null;
            }

            // Topics under this subcategory (depth 3)
            $topics = $tree->where('parent_id', $fullSubcat->id)
                ->map(fn ($t) => $allActivePages->get($t->id))
                ->filter()
                ->values();

            // Group topics by nav_group if available
            $groupedTopics = $this->nav->group($topics);

            // Extract all unique nav_group tabs for pill filtering
            $groups = $topics->pluck('nav_group')->filter()->unique()->values()->all();

            return [
                'category' => $fullSubcat,
                'topics' => $topics,
                'groups' => $groups,
                'groupedTopics' => $groupedTopics,
            ];
        })->filter()->values();

        return view('frontend.seo-page.index', [
            'title' => $title,
            'rootCategories' => $rootCategories,
            'subcategories' => $subcategories,
            'sections' => $sections,
            'isPageBreadcrumb' => false,
        ]);
    }

    public function show(string $category, ?string $subcategory = null, ?string $topic = null): View
    {
        $path = implode('/', array_filter([$category, $subcategory, $topic]));

        $page = SeoPage::active()->with('author')->where('path', $path)->first();
        abort_unless($page && $page->isPubliclyVisible(), 404);

        $trail = $page->ancestors()->concat([$page])->values();
        $root = $trail->first();
        $branch = $page->depth === 1 ? null : $trail->get(1);
        $tree = SeoPage::visibleTree();

        // Keyed on the content itself: updated_at has one-second resolution, so two
        // saves in the same second would otherwise keep serving the older body.
        $toc = Cache::remember(
            'seo_page_toc_v1_'.md5((string) $page->content),
            now()->addDays(30),
            fn () => $this->toc->build($page->content)
        );
        // After the cache: the image URLs depend on this install's asset URL, not on the content.
        $toc['html'] = $this->imageUrls->rewrite($toc['html']);

        $children = $page->depth < SeoPage::MAX_DEPTH ? $page->children()->active()->get() : collect();
        $canonical = $page->resolvedCanonical();
        $hasImage = $page->hasFeaturedImageFile();

        return view('frontend.seo-page.show', [
            'page' => $page,
            'trail' => $trail,
            'root' => $root,
            'branch' => $branch,
            'categoryGroups' => $this->nav->group($tree->whereNull('parent_id')),
            'pageGroups' => $tree->whereNotNull('parent_id')
                ->groupBy('parent_id')
                ->map(fn (Collection $pages) => $this->nav->group($pages))
                ->all(),
            'childGroups' => $this->nav->group($children),
            'toc' => $toc,
            'showToc' => $page->show_toc && count($toc['items']) >= 2,
            'hasImage' => $hasImage,
            'imageSize' => $page->featuredImageSize(),
            'schemas' => $this->schemas($page, $trail, $canonical, $hasImage),
            'fullTitle' => $page->resolvedMetaTitle(),
            'metaTitle' => $page->resolvedMetaTitle(),
            'metaDescription' => $page->resolvedMetaDescription(),
            'canonicalUrl' => $canonical,
            'metaImage' => $hasImage ? asset($page->featured_image) : null,
            'metaImageAlt' => $hasImage ? $page->featured_image_alt : null,
            'ogType' => 'article',
        ]);
    }

    public function sitemap(): Response
    {
        return response()
            ->view('frontend.seo-page.sitemap', [
                'pages' => SeoPage::visibleTree()->filter(
                    fn (SeoPage $page) => in_array(explode('/', $page->path, 2)[0], SeoPage::ROUTED_CATEGORIES, true)
                ),
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function schemas(SeoPage $page, Collection $trail, string $canonical, bool $hasImage): array
    {
        $crumbs = collect([['name' => __('Home'), 'url' => route('home')]])
            ->concat($trail->map(fn (SeoPage $crumb) => ['name' => $crumb->nav_label, 'url' => $crumb->url()]))
            ->all();

        $schemas = [
            $this->schema->breadcrumbList($crumbs),
            $this->schema->article([
                'headline' => $page->title,
                'description' => $page->resolvedMetaDescription(),
                'url' => $canonical,
                'image' => $hasImage ? asset($page->featured_image) : null,
                'datePublished' => ($page->published_at ?? $page->created_at)->toIso8601String(),
                'dateModified' => $page->updated_at->toIso8601String(),
                'authorName' => $page->author?->full_name,
                'publisherName' => (string) setting('site_title'),
                'publisherLogo' => asset(setting('site_logo')),
            ]),
        ];

        if ($faq = $this->schema->faqPage($page->faq ?? [])) {
            $schemas[] = $faq;
        }

        return $schemas;
    }
}
