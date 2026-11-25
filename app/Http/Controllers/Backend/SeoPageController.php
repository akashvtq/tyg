<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeoPageRequest;
use App\Models\Admin;
use App\Models\SeoPage;
use App\Services\SeoPage\SeoPageImageService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class SeoPageController extends Controller
{
    public function __construct(private readonly SeoPageImageService $images)
    {
        $this->permissionAuthorization([
            'index' => 'seo-page-list',
            'create' => 'seo-page-manage',
            'store' => 'seo-page-manage',
            'edit' => 'seo-page-manage',
            'update' => 'seo-page-manage',
            'destroy' => 'seo-page-manage',
            'storeImage' => 'seo-page-manage',
        ]);
    }

    public function index(Request $request): View
    {
        $status = $this->stringInput($request, 'status');
        $status = in_array($status, ['active', 'draft'], true) ? $status : null;
        $cluster = $this->stringInput($request, 'cluster');
        $cluster = in_array($cluster, SeoPage::CLUSTERS, true) ? $cluster : null;
        $search = $this->stringInput($request, 'search');
        $filtered = $status !== null || $cluster !== null || $search !== null;

        $query = SeoPage::query();
        if ($status !== null) {
            $query->where('is_active', $status === 'active');
        }
        if ($cluster !== null) {
            $query->where('cluster', $cluster);
        }
        if ($search !== null) {
            // Backslash is MySQL's default LIKE escape character.
            $term = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search).'%';
            $query->where(fn ($q) => $q->where('title', 'like', $term)
                ->orWhere('nav_label', 'like', $term)
                ->orWhere('primary_keyword', 'like', $term));
        }

        $pages = $query->orderBy('sort_order')->orderBy('nav_label')->get([
            'id', 'parent_id', 'depth', 'title', 'nav_label', 'nav_group', 'cluster', 'path',
            'is_active', 'featured_image', 'sort_order', 'updated_at',
        ]);

        return view('backend.seo-page.index', [
            'rows' => $filtered ? $pages->sortBy('path')->values() : $this->treeOrder($pages),
            'filtered' => $filtered,
            'clusters' => SeoPage::CLUSTERS,
        ]);
    }

    public function create(Request $request): View
    {
        $page = new SeoPage([
            'parent_id' => $request->integer('parent_id') ?: null,
            'author_id' => auth('admin')->id(),
            'show_toc' => true,
            'show_cta' => true,
            'sort_order' => 0,
        ]);

        return view('backend.seo-page.create', $this->formData($page));
    }

    public function store(SeoPageRequest $request): RedirectResponse
    {
        $data = $request->pageData();
        $data['author_id'] ??= auth('admin')->id();
        $data['featured_image'] = $this->images->store(
            $request->file('featured_image'),
            $data['slug'],
            $this->imageSubdirectory($data)
        );

        try {
            DB::transaction(fn () => SeoPage::create($data));
        } catch (Throwable $e) {
            $this->images->delete($data['featured_image']);

            throw $e;
        }

        notifyEvs('success', __('SEO page created successfully'));

        return redirect()->route('admin.seo-pages.index');
    }

    public function edit(SeoPage $seoPage): View
    {
        return view('backend.seo-page.edit', $this->formData($seoPage));
    }

    public function update(SeoPageRequest $request, SeoPage $seoPage): RedirectResponse
    {
        $data = $request->pageData();

        $oldImage = $seoPage->featured_image;
        $newImage = $request->hasFile('featured_image')
            ? $this->images->store(
                $request->file('featured_image'),
                $data['slug'],
                $this->imageSubdirectory($data)
            )
            : null;

        if ($newImage !== null) {
            $data['featured_image'] = $newImage;
        }

        try {
            // A path change cascades to every descendant; all rows commit together or not at all.
            DB::transaction(fn () => $seoPage->update($data));
        } catch (Throwable $e) {
            $this->images->delete($newImage);

            throw $e;
        }

        if ($newImage !== null) {
            try {
                $this->images->delete($oldImage);
            } catch (Throwable $e) {
                // The page is saved; a leftover old file must not fail the request.
                report($e);
            }
        }

        notifyEvs('success', __('SEO page updated successfully'));

        return redirect()->route('admin.seo-pages.index');
    }

    public function destroy(SeoPage $seoPage): JsonResponse
    {
        if ($seoPage->children()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => __('Delete or move this page\'s child pages first.'),
            ]);
        }

        $image = $seoPage->featured_image;

        try {
            $seoPage->delete();
        } catch (QueryException $e) {
            // e.g. a child page was added after the check above (the foreign key restricts deletes).
            report($e);

            return response()->json([
                'status' => 'error',
                'message' => __('This page could not be deleted. Delete or move its child pages first.'),
            ]);
        }

        $this->images->delete($image);

        return response()->json(['status' => 'success', 'message' => __('SEO page deleted successfully')]);
    }

    /**
     * Uploads an image for the content editor and returns its root-relative URL (e.g.
    * `/assets/general/images/seo-pages/seo/category/topic/topic-body.png`), so no host or scheme is saved in the content.
     * The public page rewrites these sources to this install's asset URL (ContentImageUrlService).
     * Named `{slug}-body[-n].{ext}` after the page's slug.
     */
    public function storeImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'slug' => ['nullable', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('seo_pages', 'id')->where(
                    fn ($query) => $query->where('depth', '<', SeoPage::MAX_DEPTH)
                ),
            ],
        ]);

        $slug = trim(Str::limit(Str::slug((string) $request->input('slug')), 60, ''), '-') ?: 'image';
        $parentPath = $request->filled('parent_id')
            ? SeoPage::findOrFail($request->integer('parent_id'))->path.'/'
            : '';
        $path = $this->images->store($request->file('image'), $slug.'-body', $parentPath.$slug);

        return response()->json(['url' => parse_url(asset($path), PHP_URL_PATH)]);
    }

    /**
     * Pages in tree order: each category, then its subcategories, each followed by its topics.
     */
    private function treeOrder(Collection $pages, ?int $parentId = null): Collection
    {
        return $pages->where('parent_id', $parentId)
            ->flatMap(fn (SeoPage $page) => collect([$page])->concat($this->treeOrder($pages, $page->id)))
            ->values();
    }

    /**
     * A trimmed, non-empty string query value, or null (arrays and blanks are ignored).
     */
    private function stringInput(Request $request, string $key): ?string
    {
        $value = $request->query($key);

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    private function imageSubdirectory(array $data): string
    {
        $parentPath = ! empty($data['parent_id'])
            ? SeoPage::findOrFail((int) $data['parent_id'])->path.'/'
            : '';

        return $parentPath.$data['slug'];
    }

    private function formData(SeoPage $page): array
    {
        if ($page->exists) {
            $parents = $page->depth === 1
                ? collect()
                : SeoPage::where('depth', $page->depth - 1)->orderBy('path')->get(['id', 'nav_label', 'path', 'depth']);
        } else {
            $parents = SeoPage::where('depth', '<', SeoPage::MAX_DEPTH)->orderBy('path')->get(['id', 'nav_label', 'path', 'depth']);
        }

        $groupsByParent = SeoPage::whereNotNull('nav_group')->get(['parent_id', 'nav_group'])
            ->groupBy(fn (SeoPage $p) => (string) ($p->parent_id ?? 'root'))
            ->map(fn (Collection $items) => $items->pluck('nav_group')->unique()->values());

        return [
            'page' => $page,
            'parents' => $parents,
            'admins' => Admin::orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'groupsByParent' => $groupsByParent,
            'hasChildren' => $page->exists && $page->children()->exists(),
            'clusters' => SeoPage::CLUSTERS,
        ];
    }
}
