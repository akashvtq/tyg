<?php

use App\Services\SeoPage\SeoPageImageService;
use Illuminate\Http\UploadedFile;

afterEach(function () {
    foreach (glob(base_path('assets/'.SeoPageImageService::DIRECTORY.'/zz-test-*')) as $file) {
        unlink($file);
    }
});

it('stores the image under a file name built from the slug', function () {
    $slug = seoTestSlug('tools');
    $path = app(SeoPageImageService::class)->store(UploadedFile::fake()->image('Photo 1.PNG', 1200, 630), $slug);

    expect($path)->toBe('general/images/seo-pages/'.$slug.'.png')
        ->and(is_file(base_path('assets/'.$path)))->toBeTrue();
});

it('appends -2, -3 when the name is taken', function () {
    $service = app(SeoPageImageService::class);

    $slug = seoTestSlug('tools');

    $first = $service->store(UploadedFile::fake()->image('a.jpg'), $slug);
    $second = $service->store(UploadedFile::fake()->image('b.jpg'), $slug);
    $third = $service->store(UploadedFile::fake()->image('c.jpg'), $slug);

    expect([$first, $second, $third])->toBe([
        "general/images/seo-pages/{$slug}.jpg",
        "general/images/seo-pages/{$slug}-2.jpg",
        "general/images/seo-pages/{$slug}-3.jpg",
    ]);
});

it('deletes a stored image and ignores missing ones', function () {
    $service = app(SeoPageImageService::class);
    $path = $service->store(UploadedFile::fake()->image('a.jpg'), seoTestSlug('delete'));

    $service->delete($path);
    $service->delete('general/images/seo-pages/zz-test-never-existed.jpg');
    $service->delete(null);

    expect(is_file(base_path('assets/'.$path)))->toBeFalse();
});

it('names the file from the detected image type, not the uploaded file name', function () {
    // UploadedFile::fake() reports a name-derived mime type, so build a real UploadedFile
    // around the fake image's actual (genuine PNG) content to simulate a spoofed client name.
    $fake = UploadedFile::fake()->image('temp.png');
    $spoofed = new UploadedFile($fake->getPathname(), 'evil.html', null, null, true);

    $slug = seoTestSlug('spoofed');
    $path = app(SeoPageImageService::class)->store($spoofed, $slug);

    expect($path)->toBe('general/images/seo-pages/'.$slug.'.png')
        ->and(is_file(base_path('assets/'.$path)))->toBeTrue();
});

it('rejects a non-image upload and stores nothing', function () {
    $directory = base_path('assets/'.SeoPageImageService::DIRECTORY);

    app(SeoPageImageService::class)->store(UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'), 'zz-test-rejected');
})->throws(InvalidArgumentException::class, 'Unsupported image type.');

it('leaves no file behind when the upload is rejected', function () {
    $slug = seoTestSlug('rejected');

    try {
        app(SeoPageImageService::class)->store(UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'), $slug);
    } catch (InvalidArgumentException) {
        // expected
    }

    expect(glob(base_path('assets/'.SeoPageImageService::DIRECTORY.'/'.$slug.'*')))->toBe([]);
});
