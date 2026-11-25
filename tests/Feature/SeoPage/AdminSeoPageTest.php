<?php

use App\Http\Requests\SeoPageRequest;
use App\Models\Page;
use App\Models\SeoPage;
use App\Services\SeoPage\SeoPageImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

afterEach(function () {
    $directory = base_path('assets/'.SeoPageImageService::DIRECTORY);
    if (! is_dir($directory)) {
        return;
    }

    foreach (\Illuminate\Support\Facades\File::allFiles($directory) as $file) {
        if (str_starts_with($file->getFilename(), 'zz-test-')) {
            unlink($file->getPathname());
        }
    }
});

function seoPayload(array $overrides = []): array
{
    return array_merge([
        'parent_id' => '',
        'title' => 'Zz Test Page: A Complete Guide',
        'nav_label' => 'Zz Test Page',
        'slug' => seoTestSlug(),
        'nav_group' => '',
        'excerpt' => 'A short summary.',
        'content' => '<h2>One</h2><p>Text</p><h2>Two</h2><p>More</p>',
        'featured_image' => UploadedFile::fake()->image('cover.jpg', 1200, 630),
        'featured_image_alt' => 'Dashboard showing referring domains',
        'meta_title' => '',
        'meta_description' => '',
        'canonical_url' => '',
        'primary_keyword' => 'test keyword',
        'cluster' => 'inner',
        'show_toc' => '1',
        'show_cta' => '1',
        'is_active' => '1',
        'sort_order' => '0',
    ], $overrides);
}

beforeEach(function () {
    [$this->category, $this->subcategory, $this->topic] = seoTree();
});

it('requires the seo-page permissions', function () {
    $this->actingAs(seoAdmin([]), 'admin')
        ->get(route('admin.seo-pages.index'))
        ->assertForbidden();

    $this->actingAs(seoAdmin(['seo-page-list']), 'admin')
        ->get(route('admin.seo-pages.create'))
        ->assertForbidden();
});

it('forbids store, update and destroy to an admin who can only list', function () {
    $admin = seoAdmin(['seo-page-list']);
    $payload = seoPayload(['parent_id' => $this->category->id]);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.seo-pages.store'), $payload)
        ->assertForbidden();

    $this->actingAs($admin, 'admin')
        ->put(route('admin.seo-pages.update', $this->topic), seoPayload([
            'parent_id' => $this->subcategory->id, 'slug' => 'zz-test-renamed', 'featured_image' => null,
        ]))
        ->assertForbidden();

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.seo-pages.destroy', $this->topic))
        ->assertForbidden();

    expect(SeoPage::where('slug', $payload['slug'])->exists())->toBeFalse()
        ->and($this->topic->fresh()->slug)->toBe('tools');
});

it('reserves the first static segment of every route, the admin prefix and page slugs, but not routed categories', function () {
    Page::create(['title' => 'Zz Test Page Slug', 'slug' => 'zz-test-page-slug', 'component_id' => '[]']);

    $reserved = SeoPageRequest::reservedCategorySlugs();

    expect($reserved)
        ->toContain('logout', 'sanctum', setting('admin_prefix'), 'zz-test-page-slug', ...SeoPage::RESERVED_CATEGORY_SLUGS)
        ->not->toContain('seo', '')
        ->and(collect($reserved)->contains(fn ($slug) => str_starts_with($slug, '{')))->toBeFalse();
});

it('lets the seo category itself be updated', function () {
    $this->actingAs(seoAdmin(), 'admin')
        ->put(route('admin.seo-pages.update', $this->category), seoPayload([
            'slug' => 'seo', 'nav_label' => 'SEO', 'featured_image' => null,
        ]))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.seo-pages.index'));
});

it('treats LIKE wildcards in the search literally and ignores malformed filters', function () {
    SeoPage::factory()->create(['parent_id' => $this->subcategory->id, 'slug' => 'zz-test-pct', 'nav_label' => 'Zz 100% Guide']);
    $admin = seoAdmin();

    $this->actingAs($admin, 'admin')
        ->get(route('admin.seo-pages.index', ['search' => '100%']))
        ->assertOk()
        ->assertSee('Zz 100% Guide')
        ->assertDontSee('Link Building Tools');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.seo-pages.index', ['search' => '%']))
        ->assertOk()
        ->assertSee('Zz 100% Guide')
        ->assertDontSee('Link Building Tools');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.seo-pages.index', ['search' => '_']))
        ->assertOk()
        ->assertDontSee('Link Building Tools');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.seo-pages.index', ['search' => ['x'], 'status' => ['active'], 'cluster' => ['inner']]))
        ->assertOk()
        ->assertSeeInOrder(['SEO', 'SEO Link Building', 'Link Building Tools']);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.seo-pages.index', ['status' => 'bogus']))
        ->assertOk()
        ->assertSee('Link Building Tools');
});

it('adds an SEO Pages item to the admin menu', function () {
    expect(json_encode(config('admin_menus')))->toContain('"route":"admin.seo-pages.index"');
});

it('lists pages in tree order', function () {
    $this->actingAs(seoAdmin(), 'admin')
        ->get(route('admin.seo-pages.index'))
        ->assertOk()
        ->assertSeeInOrder(['SEO', 'SEO Link Building', 'Link Building Tools']);
});

it('creates a topic with a slug-named image, purified content and the current admin as author', function () {
    $admin = seoAdmin();
    $slug = seoTestSlug();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.seo-pages.store'), seoPayload([
            'parent_id' => $this->subcategory->id,
            'slug' => $slug,
            'content' => '<h2>One</h2><script>alert(1)</script><table><tr><td>1</td></tr></table>',
            'faq' => [
                ['question' => 'First?', 'answer' => '<p>Yes.</p>'],
                ['question' => '', 'answer' => ''],
            ],
        ]))
        ->assertRedirect(route('admin.seo-pages.index'))
        ->assertSessionHasNoErrors();

    $page = SeoPage::where('path', 'seo/link-building/'.$slug)->firstOrFail();

    expect($page->depth)->toBe(3)
        ->and($page->featured_image)->toBe('general/images/seo-pages/'.$slug.'.jpg')
        ->and(is_file(base_path('assets/'.$page->featured_image)))->toBeTrue()
        ->and($page->content)->toContain('<table>')->not->toContain('<script')
        ->and($page->faq)->toEqual([['question' => 'First?', 'answer' => '<p>Yes.</p>']])
        ->and($page->author_id)->toBe($admin->id)
        ->and($page->cluster)->toBe('inner')
        ->and($page->is_active)->toBeTrue()
        ->and($page->published_at)->not->toBeNull();
});

it('accepts a 92-character meta title', function () {
    $this->actingAs(seoAdmin(), 'admin')
        ->post(route('admin.seo-pages.store'), seoPayload([
            'parent_id' => $this->category->id,
            'meta_title' => str_repeat('x', 92),
        ]))
        ->assertSessionHasNoErrors();
});

it('rejects invalid input', function (array $overrides, string $field) {
    $overrides = collect($overrides)->map(fn ($value) => match ($value) {
        '@topic' => $this->topic->id,
        '@subcategory' => $this->subcategory->id,
        '@page-slug' => Page::create(['title' => 'Zz Test Page Slug', 'slug' => 'zz-test-page-slug', 'component_id' => '[]'])->slug,
        default => $value,
    })->all();

    $this->actingAs(seoAdmin(), 'admin')
        ->post(route('admin.seo-pages.store'), seoPayload($overrides))
        ->assertSessionHasErrors($field);
})->with([
    'missing image' => [['featured_image' => null], 'featured_image'],
    'missing alt text' => [['featured_image_alt' => ''], 'featured_image_alt'],
    'alt text is a file name' => [['featured_image_alt' => 'image1.jpg'], 'featured_image_alt'],
    'alt text too long' => [['featured_image_alt' => str_repeat('a', 126)], 'featured_image_alt'],
    'missing nav label' => [['nav_label' => '', 'slug' => 'zz-test-x'], 'nav_label'],
    'unknown cluster' => [['cluster' => 'middle'], 'cluster'],
    'fourth level' => [['parent_id' => '@topic'], 'parent_id'],
    'duplicate sibling slug' => [['parent_id' => '@subcategory', 'slug' => 'tools'], 'slug'],
    'reserved category slug' => [['slug' => 'software'], 'slug'],
    'route segment as category slug' => [['slug' => 'logout'], 'slug'],
    'existing page slug as category' => [['slug' => '@page-slug'], 'slug'],
    'non-numeric sort order' => [['sort_order' => 'abc'], 'sort_order'],
    'missing content' => [['content' => ''], 'content'],
    'content empty after purification' => [['content' => '<p><br></p>'], 'content'],
    'content only disallowed markup' => [['content' => '<script>alert(1)</script><p> </p>'], 'content'],
    'faq answer without question' => [['faq' => [['question' => '', 'answer' => 'Orphan']]], 'faq.0.question'],
    'content image without alt' => [['content' => '<p>Text</p><img src="https://example.com/chart">'], 'content'],
    'content image with empty alt' => [['content' => '<p>Text</p><img src="https://example.com/a.png" alt="">'], 'content'],
    'content image with blank alt' => [['content' => '<p>Text</p><img src="https://example.com/a.png" alt="  ">'], 'content'],
    'content image alt is a file name' => [['content' => '<p>Text</p><img src="https://example.com/a.png" alt="IMG_0042.JPG">'], 'content'],
    'content image alt is the src file name' => [['content' => '<p>Text</p><img src="https://example.com/growth-chart" alt="growth-chart">'], 'content'],
]);

it('says which content images need alt text', function () {
    $this->actingAs(seoAdmin(), 'admin')
        ->post(route('admin.seo-pages.store'), seoPayload([
            'parent_id' => $this->category->id,
            'content' => '<p>Text</p><img src="https://example.com/a.png" alt="A chart"><img src="https://example.com/x/b-body.png">'
                .'<img src="/assets/general/images/seo-pages/c-body.webp" alt="c-body.webp">',
        ]))
        ->assertSessionHasErrors(['content' => 'Every image in the content needs alt text (missing for: b-body.png, c-body.webp).']);
});

it('accepts alt text that mentions a file name inside a sentence', function () {
    $slug = seoTestSlug();

    $this->actingAs(seoAdmin(), 'admin')
        ->post(route('admin.seo-pages.store'), seoPayload([
            'parent_id' => $this->category->id,
            'slug' => $slug,
            'content' => '<p>Text</p><img src="https://example.com/a.png" alt="Growth shown in chart.png">',
        ]))
        ->assertRedirect(route('admin.seo-pages.index'))
        ->assertSessionHasNoErrors();

    expect(SeoPage::where('slug', $slug)->exists())->toBeTrue();
});

it('saves content images that have descriptive alt text', function () {
    $slug = seoTestSlug();

    $this->actingAs(seoAdmin(), 'admin')
        ->post(route('admin.seo-pages.store'), seoPayload([
            'parent_id' => $this->category->id,
            'slug' => $slug,
            'content' => '<h2>One</h2><p>Text</p><p><img src="https://example.com/a.png" alt="Chart of referring domains over time"></p>',
        ]))
        ->assertSessionHasNoErrors();

    expect(SeoPage::where('slug', $slug)->value('content'))
        ->toContain('<img src="https://example.com/a.png" alt="Chart of referring domains over time"');
});

it('moves a topic to another subcategory but not to a different level', function () {
    $other = SeoPage::factory()->create(['parent_id' => $this->category->id, 'slug' => 'zz-test-other']);
    $admin = seoAdmin();

    $this->actingAs($admin, 'admin')
        ->put(route('admin.seo-pages.update', $this->topic), seoPayload([
            'parent_id' => $this->category->id,
            'slug' => 'tools',
            'featured_image' => null,
        ]))
        ->assertSessionHasErrors('parent_id');

    $this->actingAs($admin, 'admin')
        ->put(route('admin.seo-pages.update', $this->topic), seoPayload([
            'parent_id' => $other->id,
            'slug' => 'tools',
            'featured_image' => null,
        ]))
        ->assertSessionHasNoErrors();

    expect($this->topic->fresh()->path)->toBe('seo/zz-test-other/tools');
});

it('keeps the current image when none is uploaded and replaces it when one is', function () {
    $admin = seoAdmin();
    $original = $this->topic->featured_image;

    $this->actingAs($admin, 'admin')
        ->put(route('admin.seo-pages.update', $this->topic), seoPayload([
            'parent_id' => $this->subcategory->id, 'slug' => 'tools', 'featured_image' => null,
        ]))
        ->assertSessionHasNoErrors();
    expect($this->topic->fresh()->featured_image)->toBe($original);

    $slug = seoTestSlug('tools');
    $this->actingAs($admin, 'admin')
        ->put(route('admin.seo-pages.update', $this->topic), seoPayload([
            'parent_id' => $this->subcategory->id, 'slug' => $slug,
        ]))
        ->assertSessionHasNoErrors();
    $imagePath = 'general/images/seo-pages/seo/link-building/'.$slug.'/'.$slug.'.jpg';
    expect($this->topic->fresh()->featured_image)->toBe($imagePath)
        ->and(is_file(base_path('assets/'.$imagePath)))->toBeTrue();
});

it('refuses to delete a page that has children', function () {
    $this->actingAs(seoAdmin(), 'admin')
        ->delete(route('admin.seo-pages.destroy', $this->subcategory))
        ->assertOk()
        ->assertJson(['status' => 'error']);

    expect(SeoPage::find($this->subcategory->id))->not->toBeNull();
});

it('deletes a page without children and its image', function () {
    $admin = seoAdmin();
    $payload = seoPayload(['parent_id' => $this->subcategory->id]);
    $this->actingAs($admin, 'admin')->post(route('admin.seo-pages.store'), $payload);
    $page = SeoPage::where('slug', $payload['slug'])->firstOrFail();

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.seo-pages.destroy', $page))
        ->assertOk()
        ->assertJson(['status' => 'success']);

    expect(SeoPage::find($page->id))->toBeNull()
        ->and(is_file(base_path('assets/'.$page->featured_image)))->toBeFalse();
});

it('rolls back the update and keeps the old image when cascading to child pages fails', function () {
    $directory = base_path('assets/'.SeoPageImageService::DIRECTORY);
    $oldImage = SeoPageImageService::DIRECTORY.'/'.seoTestSlug('old').'.jpg';
    $renamed = seoTestSlug('renamed');
    file_put_contents(base_path('assets/'.$oldImage), 'old');
    $this->subcategory->forceFill(['featured_image' => $oldImage])->saveQuietly();

    // Corrupt the child's stored depth (bypassing the model) so the path cascade
    // trips the model's LogicException after the subcategory row has been written.
    DB::table('seo_pages')->where('id', $this->topic->id)->update(['depth' => 2]);

    $this->actingAs(seoAdmin(), 'admin')
        ->put(route('admin.seo-pages.update', $this->subcategory), seoPayload([
            'parent_id' => $this->category->id,
            'slug' => $renamed,
        ]))
        ->assertStatus(500);

    $subcategory = $this->subcategory->fresh();

    expect($subcategory->path)->toBe('seo/link-building')
        ->and($subcategory->featured_image)->toBe($oldImage)
        ->and(is_file(base_path('assets/'.$oldImage)))->toBeTrue()
        ->and(glob($directory.'/seo/'.$renamed.'/'.$renamed.'*'))->toBe([])
        ->and($this->topic->fresh()->path)->toBe('seo/link-building/tools');
});

it('returns a JSON error and keeps the image when the delete query fails', function () {
    $image = SeoPageImageService::DIRECTORY.'/'.seoTestSlug('delete').'.jpg';
    file_put_contents(base_path('assets/'.$image), 'img');
    $this->topic->forceFill(['featured_image' => $image])->saveQuietly();

    // A child appears between the children check and the delete; the foreign key refuses the delete.
    SeoPage::deleting(function (SeoPage $page) {
        DB::table('seo_pages')->insert([
            'parent_id' => $page->id, 'depth' => 3, 'title' => 'Zz Late Child', 'nav_label' => 'Zz Late Child',
            'slug' => 'zz-test-late', 'path' => $page->path.'/zz-test-late', 'content' => '<p>x</p>',
            'featured_image' => 'general/images/seo-pages/missing.jpg', 'featured_image_alt' => 'Late child',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    });

    $this->actingAs(seoAdmin(), 'admin')
        ->delete(route('admin.seo-pages.destroy', $this->topic))
        ->assertOk()
        ->assertJson(['status' => 'error']);

    expect(SeoPage::find($this->topic->id))->not->toBeNull()
        ->and(is_file(base_path('assets/'.$image)))->toBeTrue();
});

it('accepts content that is only an image or a table', function (string $content) {
    $this->actingAs(seoAdmin(), 'admin')
        ->post(route('admin.seo-pages.store'), seoPayload(['parent_id' => $this->category->id, 'content' => $content]))
        ->assertSessionHasNoErrors();
})->with([
    'image' => ['<p><img src="https://example.com/chart.png" alt="Chart"></p>'],
    'table' => ['<table><tbody><tr><td></td></tr></tbody></table>'],
]);

it('refuses to rename a routed category that has children', function () {
    $this->actingAs(seoAdmin(), 'admin')
        ->put(route('admin.seo-pages.update', $this->category), seoPayload([
            'slug' => 'zz-test-renamed', 'nav_label' => 'SEO', 'featured_image' => null,
        ]))
        ->assertSessionHasErrors(['slug' => 'This category is live; add the new slug to SeoPage::ROUTED_CATEGORIES first, or its pages will go offline.']);

    expect($this->category->fresh()->slug)->toBe('seo');
});

it('lets a routed category without children be renamed', function () {
    DB::table('seo_pages')->where('parent_id', '!=', null)->orderByDesc('depth')->delete();

    $this->actingAs(seoAdmin(), 'admin')
        ->put(route('admin.seo-pages.update', $this->category), seoPayload([
            'slug' => 'zz-test-renamed', 'nav_label' => 'SEO', 'featured_image' => null,
        ]))
        ->assertSessionHasNoErrors();

    expect($this->category->fresh()->slug)->toBe('zz-test-renamed');
});
