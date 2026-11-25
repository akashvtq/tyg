<?php

use App\Services\SeoPage\SeoPageImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

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

it('forbids body image uploads to an admin who can only list', function () {
    $slug = seoTestSlug('forbidden');

    $this->actingAs(seoAdmin(['seo-page-list']), 'admin')
        ->postJson(route('admin.seo-pages.images.store'), [
            'image' => UploadedFile::fake()->image('photo.png'),
            'slug' => $slug,
        ])
        ->assertForbidden();

    expect(seoBodyImageMatches($slug))->toBe([]);
});

it('rejects a body image upload that is not a valid image', function (callable $file) {
    $slug = seoTestSlug('bad');

    $this->actingAs(seoAdmin(), 'admin')
        ->postJson(route('admin.seo-pages.images.store'), ['image' => $file(), 'slug' => $slug])
        ->assertStatus(422)
        ->assertJsonValidationErrors('image');

    expect(seoBodyImageMatches($slug))->toBe([]);
})->with([
    'missing' => [fn () => null],
    'not an image' => [fn () => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')],
    'gif' => [fn () => UploadedFile::fake()->image('anim.gif')],
    'too large' => [fn () => UploadedFile::fake()->image('big.png')->size(2049)],
]);

/** The root-relative URL the endpoint returns, e.g. /assets/... (or /build-saas-links/assets/... in a subfolder). */
function seoBodyImageUrl(string $name): string
{
    return parse_url(asset(SeoPageImageService::DIRECTORY.'/'.$name), PHP_URL_PATH);
}

function seoBodyImageMatches(string $prefix): array
{
    return array_filter(\Illuminate\Support\Facades\File::allFiles(base_path('assets/'.SeoPageImageService::DIRECTORY)),
        fn ($file) => str_starts_with($file->getFilename(), $prefix));
}

it('stores a body image under the page slug and returns its root-relative URL', function () {
    $slug = seoTestSlug('tools');

    $response = $this->actingAs(seoAdmin(), 'admin')
        ->postJson(route('admin.seo-pages.images.store'), [
            'image' => UploadedFile::fake()->image('Screenshot 1.PNG', 800, 400),
            'slug' => $slug,
        ])
        ->assertOk();

    $path = SeoPageImageService::DIRECTORY.'/'.$slug.'/'.$slug.'-body.png';

    $response->assertExactJson(['url' => seoBodyImageUrl($slug.'/'.$slug.'-body.png')]);
    expect(is_file(base_path('assets/'.$path)))->toBeTrue()
        ->and($response->json('url'))->toStartWith('/')->toEndWith('/assets/'.$path)
        ->not->toContain('://');
});

it('stores body images under the selected category and subcategory slug path', function () {
    [, $subcategory] = seoTree();
    $slug = seoTestSlug('analytics');

    $response = $this->actingAs(seoAdmin(), 'admin')
        ->postJson(route('admin.seo-pages.images.store'), [
            'image' => UploadedFile::fake()->image('Screenshot.png'),
            'parent_id' => $subcategory->id,
            'slug' => $slug,
        ])
        ->assertOk();

    $name = $slug.'-body.png';
    $relativePath = SeoPageImageService::DIRECTORY.'/seo/link-building/'.$slug.'/'.$name;

    $response->assertExactJson(['url' => seoBodyImageUrl('seo/link-building/'.$slug.'/'.$name)]);
    expect(is_file(base_path('assets/'.$relativePath)))->toBeTrue();
});

it('numbers repeated body images and slugifies the slug it is given', function () {
    $admin = seoAdmin();
    $suffix = Str::lower(Str::random(8));
    $slug = 'zz-test-tools-'.$suffix;

    $first = $this->actingAs($admin, 'admin')
        ->postJson(route('admin.seo-pages.images.store'), ['image' => UploadedFile::fake()->image('a.jpg'), 'slug' => 'Zz Test ../Tools '.strtoupper($suffix)])
        ->assertOk()->json('url');
    $second = $this->actingAs($admin, 'admin')
        ->postJson(route('admin.seo-pages.images.store'), ['image' => UploadedFile::fake()->image('b.jpg'), 'slug' => $slug])
        ->assertOk()->json('url');

    expect($first)->toBe(seoBodyImageUrl($slug.'/'.$slug.'-body.jpg'))
        ->and($second)->toBe(seoBodyImageUrl($slug.'/'.$slug.'-body-2.jpg'));
});

it('falls back to "image" when no usable slug is sent', function () {
    $url = $this->actingAs(seoAdmin(), 'admin')
        ->postJson(route('admin.seo-pages.images.store'), ['image' => UploadedFile::fake()->image('a.png')])
        ->assertOk()->json('url');

    $name = basename($url);
    // Not a zz-test-* name, so remove it here.
    $file = base_path('assets/'.SeoPageImageService::DIRECTORY.'/'.$name);
    expect(is_file($file))->toBeTrue();
    unlink($file);

    expect($name)->toMatch('/^image-body(-\d+)?\.png$/');
});
