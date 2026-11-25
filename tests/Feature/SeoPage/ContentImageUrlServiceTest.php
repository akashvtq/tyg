<?php

use App\Services\SeoPage\ContentImageUrlService;

function seoRewrite(string $html): string
{
    return app(ContentImageUrlService::class)->rewrite($html);
}

it('rewrites seo-pages image sources from any host or prefix to this site', function (string $src) {
    expect(seoRewrite('<p><img src="'.$src.'" alt="Chart"></p>'))
        ->toBe('<p><img src="'.asset('general/images/seo-pages/a-body.png').'" alt="Chart"></p>');
})->with([
    'other host' => 'http://staging.example/assets/general/images/seo-pages/a-body.png',
    'https with subfolder' => 'https://example.com/build-saas-links/assets/general/images/seo-pages/a-body.png',
    'root-relative with subfolder' => '/build-saas-links/assets/general/images/seo-pages/a-body.png',
    'root-relative' => '/assets/general/images/seo-pages/a-body.png',
    'relative' => 'assets/general/images/seo-pages/a-body.png',
    'with a query string' => '/assets/general/images/seo-pages/a-body.png?v=2',
]);

it('rewrites every matching image and keeps the other attributes and markup', function () {
    $html = '<h2>A</h2><p>x <img class="wide" src="/x/assets/general/images/seo-pages/one.jpg" alt="One" width="10">'
        ."<IMG alt='Two' SRC='http://old/assets/general/images/seo-pages/two.webp'></p>";

    expect(seoRewrite($html))->toBe('<h2>A</h2><p>x <img class="wide" src="'.asset('general/images/seo-pages/one.jpg').'" alt="One" width="10">'
        ."<IMG alt='Two' SRC='".asset('general/images/seo-pages/two.webp')."'></p>");
});

it('leaves other sources untouched', function (string $html) {
    expect(seoRewrite($html))->toBe($html);
})->with([
    'another folder' => '<img src="https://cdn.example.com/other/c.png" alt="Other">',
    'blog images' => '<img src="/assets/general/images/blog/c.png" alt="Blog">',
    'a nested path under seo-pages' => '<img src="/assets/general/images/seo-pages/sub/c.png" alt="Nested">',
    'a traversal attempt' => '<img src="/assets/general/images/seo-pages/..%2F..%2Fx.png" alt="x">',
    'the folder in a link, not an image' => '<a href="/assets/general/images/seo-pages/a.png">a</a>',
    'the folder in alt text' => '<img src="https://cdn.example.com/c.png" alt="/assets/general/images/seo-pages/a.png">',
    'no images' => '<h2>Plain</h2><p>Text</p>',
]);
