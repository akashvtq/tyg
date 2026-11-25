<?php

use App\Models\SeoPage;
use Illuminate\Support\Facades\Cache;

it('computes depth and path from the parent', function () {
    [$category, $subcategory, $topic] = seoTree();

    expect($category->depth)->toBe(1)
        ->and($category->path)->toBe('seo')
        ->and($subcategory->depth)->toBe(2)
        ->and($subcategory->path)->toBe('seo/link-building')
        ->and($topic->depth)->toBe(3)
        ->and($topic->path)->toBe('seo/link-building/tools')
        ->and($topic->url())->toBe(url('seo/link-building/tools'));
});

it('slugifies the slug on save', function () {
    [$category] = seoTree();
    $page = SeoPage::factory()->create(['parent_id' => $category->id, 'slug' => 'Guest Posting!']);

    expect($page->slug)->toBe('guest-posting')->and($page->path)->toBe('seo/guest-posting');
});

it('recomputes descendant paths when a slug changes', function () {
    [, $subcategory, $topic] = seoTree();

    $subcategory->update(['slug' => 'backlinks']);

    expect($topic->fresh()->path)->toBe('seo/backlinks/tools');
});

it('sets published_at only the first time a page is activated', function () {
    [$category] = seoTree();
    $page = SeoPage::factory()->create(['parent_id' => $category->id, 'is_active' => false]);
    expect($page->published_at)->toBeNull();

    $page->update(['is_active' => true]);
    $firstPublished = $page->fresh()->published_at;
    expect($firstPublished)->not->toBeNull();

    $this->travel(2)->days();
    $page->update(['is_active' => false]);
    $page->update(['is_active' => true]);
    expect($page->fresh()->published_at->equalTo($firstPublished))->toBeTrue();
});

it('is publicly visible only when it and all its ancestors are active', function () {
    [, $subcategory, $topic] = seoTree();
    expect($topic->isPubliclyVisible())->toBeTrue();

    $subcategory->update(['is_active' => false]);

    expect($topic->fresh()->isPubliclyVisible())->toBeFalse();
});

it('returns ancestors from the root down', function () {
    [$category, $subcategory, $topic] = seoTree();

    expect($topic->ancestors()->pluck('id')->all())->toBe([$category->id, $subcategory->id])
        ->and($category->ancestors())->toBeEmpty();
});

it('falls back for meta title, description and canonical', function () {
    [$category] = seoTree();
    $page = SeoPage::factory()->create([
        'parent_id' => $category->id,
        'title' => 'The H1',
        'meta_title' => null,
        'meta_description' => null,
        'excerpt' => null,
        'canonical_url' => null,
        'content' => '<p>'.str_repeat('word ', 60).'</p>',
    ]);

    expect($page->resolvedMetaTitle())->toBe('The H1')
        ->and(mb_strlen($page->resolvedMetaDescription()))->toBeLessThanOrEqual(160)
        ->and($page->resolvedMetaDescription())->toStartWith('word word')
        ->and($page->resolvedCanonical())->toBe($page->url());

    $page->update(['excerpt' => 'Short summary.']);
    expect($page->resolvedMetaDescription())->toBe('Short summary.');

    $page->update(['meta_title' => 'Custom title', 'meta_description' => 'Custom description', 'canonical_url' => 'https://example.com/x']);
    expect($page->resolvedMetaTitle())->toBe('Custom title')
        ->and($page->resolvedMetaDescription())->toBe('Custom description')
        ->and($page->resolvedCanonical())->toBe('https://example.com/x');
});

it('decodes entities and spaces out tags when building the meta description fallback', function () {
    [$category] = seoTree();
    $page = SeoPage::factory()->create([
        'parent_id' => $category->id,
        'meta_description' => null,
        'excerpt' => null,
        'content' => '<h2>Intro</h2><p>Tom &amp; Jerry&nbsp;rock</p>',
    ]);

    expect($page->resolvedMetaDescription())->toBe('Intro Tom & Jerry rock');
});

it('keeps punctuation attached after inline tags in the meta description fallback', function () {
    [$category] = seoTree();
    $page = SeoPage::factory()->create([
        'parent_id' => $category->id,
        'meta_description' => null,
        'excerpt' => null,
        'content' => '<p>Read <a href="x">our guide</a>. It is <strong>fast</strong>!</p>',
    ]);

    expect($page->resolvedMetaDescription())->toBe('Read our guide. It is fast!');
});

it('cuts the meta description fallback at a word boundary within 160 characters', function () {
    [$category] = seoTree();
    $page = SeoPage::factory()->create([
        'parent_id' => $category->id,
        'meta_description' => null,
        'excerpt' => null,
        'content' => '<p>'.str_repeat('wordy ', 40).'</p>',
    ]);

    $description = $page->resolvedMetaDescription();

    expect(mb_strlen($description))->toBeLessThanOrEqual(160)
        ->and($description)->toEndWith('wordy')
        ->and($description)->not->toContain('  ');
});

it('limits the excerpt fallback to 160 characters', function () {
    [$category] = seoTree();
    $page = SeoPage::factory()->create([
        'parent_id' => $category->id,
        'meta_description' => null,
        'excerpt' => str_repeat('excerpt words here ', 10),
        'content' => '<p>content</p>',
    ]);

    expect(mb_strlen($page->resolvedMetaDescription()))->toBeLessThanOrEqual(160);
});

it('refuses to create a page deeper than the maximum depth', function () {
    [, , $topic] = seoTree();

    expect(fn () => SeoPage::factory()->create(['parent_id' => $topic->id]))
        ->toThrow(LogicException::class, 'SEO pages can only be nested '.SeoPage::MAX_DEPTH.' levels deep.');
});

it('refuses to move a page to a different level', function () {
    [$category, , $topic] = seoTree();

    expect(fn () => $topic->update(['parent_id' => $category->id]))
        ->toThrow(LogicException::class, 'An SEO page cannot change level; move it to a parent at the same level as its current parent.');
});

it('allows moving a page to a new parent at the same level and updates its path', function () {
    [$category, , $topic] = seoTree();
    $otherSubcategory = SeoPage::factory()->create(['parent_id' => $category->id, 'slug' => 'content-marketing']);

    $topic->update(['parent_id' => $otherSubcategory->id]);

    expect($topic->fresh()->path)->toBe('seo/content-marketing/tools');
});

it('cascades a slug rename down two levels', function () {
    [$category, $subcategory, $topic] = seoTree();

    $category->update(['slug' => 'search-engine-optimization']);

    expect($subcategory->fresh()->path)->toBe('search-engine-optimization/link-building')
        ->and($topic->fresh()->path)->toBe('search-engine-optimization/link-building/tools');
});

it('lists only publicly visible pages in the cached tree and refreshes it on save', function () {
    [$category, $subcategory, $topic] = seoTree();
    expect(SeoPage::visibleTree()->pluck('id')->all())->toBe([$category->id, $subcategory->id, $topic->id]);

    $subcategory->update(['is_active' => false]);

    expect(Cache::has(SeoPage::TREE_CACHE_KEY))->toBeFalse()
        ->and(SeoPage::visibleTree()->pluck('id')->all())->toBe([$category->id]);
});

it('clears the cached tree when a page is deleted', function () {
    [$category, $subcategory, $topic] = seoTree();
    SeoPage::visibleTree();
    expect(Cache::has(SeoPage::TREE_CACHE_KEY))->toBeTrue();

    $topic->delete();

    expect(Cache::has(SeoPage::TREE_CACHE_KEY))->toBeFalse()
        ->and(SeoPage::visibleTree()->pluck('id')->all())->toBe([$category->id, $subcategory->id]);
});
