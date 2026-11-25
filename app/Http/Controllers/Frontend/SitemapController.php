<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Page;
use App\Models\SeoPage;
use App\Models\Services;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([
            ['loc' => url('/'), 'lastmod' => now()->toAtomString()],
        ]);

        $urls = $urls->merge(Page::query()
            ->whereNotNull('slug')
            ->where('slug', '!=', '/')
            ->get()
            ->map(fn (Page $page) => [
                'loc' => url($page->slug),
                'lastmod' => ($page->updated_at ?? now())->toAtomString(),
            ]));

        $urls = $urls->merge(SeoPage::visibleTree()->map(fn (SeoPage $page) => [
            'loc' => $page->url(),
            'lastmod' => ($page->updated_at ?? now())->toAtomString(),
        ]));

        $urls = $urls->merge(Blog::query()->where('is_active', true)->get()->map(fn (Blog $blog) => [
            'loc' => route('blog.details', $blog->slug),
            'lastmod' => ($blog->updated_at ?? $blog->created_at ?? now())->toAtomString(),
        ]));

        $urls = $urls->merge(Services::query()->where('status', 1)->get()->map(fn (Services $service) => [
            'loc' => route('service.details', $service->slug),
            'lastmod' => ($service->updated_at ?? $service->created_at ?? now())->toAtomString(),
        ]));

        $urls = $urls
            ->map(fn (array $item) => ['loc' => $item['loc'], 'lastmod' => $item['lastmod']])
            ->unique('loc')
            ->values();

        return response()
            ->view('frontend.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
