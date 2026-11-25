<?php

use App\Models\Page;
use App\Models\SeoPage;

it('stops a CMS page from taking a routed SEO category slug', function (string $slug) {
    $this->actingAs(seoAdmin(['page-list', 'page-manage']), 'admin')
        ->from(route('admin.page.site.create'))
        ->post(route('admin.page.site.store'), [
            'page_title' => 'Zz Test Page',
            'page_slug' => $slug,
            'component' => ['1'],
        ])
        ->assertRedirect(route('admin.page.site.create'))
        ->assertSessionHas('notifyevs.message', 'This slug is used by the SEO pages section; choose another.');

    expect(Page::where('title', 'Zz Test Page')->exists())->toBeFalse();
})->with(fn () => [...SeoPage::ROUTED_CATEGORIES, strtoupper(SeoPage::ROUTED_CATEGORIES[0])]);

it('stops a CMS page from being renamed to a routed SEO category slug', function () {
    $page = Page::create(['title' => 'Zz Test Page', 'slug' => 'zz-test-page', 'component_id' => '["1"]']);

    $this->actingAs(seoAdmin(['page-list', 'page-manage']), 'admin')
        ->put(route('admin.page.site.update', $page->id), [
            'page_title' => 'Zz Test Page',
            'page_slug' => SeoPage::ROUTED_CATEGORIES[0],
            'component' => ['1'],
        ])
        ->assertSessionHas('notifyevs.message', 'This slug is used by the SEO pages section; choose another.');

    expect($page->fresh()->slug)->toBe('zz-test-page');
});

it('still creates a CMS page with an ordinary slug', function () {
    $this->actingAs(seoAdmin(['page-list', 'page-manage']), 'admin')
        ->post(route('admin.page.site.store'), [
            'page_title' => 'Zz Test Page',
            'page_slug' => 'zz-test-page',
            'component' => ['1'],
        ])
        ->assertRedirect(route('admin.page.site.index'));

    expect(Page::where('slug', 'zz-test-page')->exists())->toBeTrue();
});
