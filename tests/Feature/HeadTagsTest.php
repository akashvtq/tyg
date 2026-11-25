<?php

it('keeps the existing head output on pages that set no SEO variables', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('<meta property="og:image" content="'.asset(setting('meta_image')).'" />', false)
        ->assertSee(' - '.setting('site_title').'</title>', false)
        ->assertDontSee('rel="canonical"', false)
        ->assertDontSee('og:type', false);
});

it('prints canonical, Open Graph tags, schema and the exact title when a view sets them', function () {
    $html = view('frontend.layouts.partials._head', [
        'fullTitle' => 'Exact Title From The Sheet',
        'metaTitle' => 'Exact Title From The Sheet',
        'metaDescription' => 'A description.',
        'canonicalUrl' => 'https://buildsaaslinks.com/seo',
        'metaImage' => 'https://buildsaaslinks.com/assets/x.webp',
        'metaImageAlt' => 'An image',
        'ogType' => 'article',
    ])->render();

    expect($html)
        ->toContain('<title>Exact Title From The Sheet</title>')
        ->toContain('<link rel="canonical" href="https://buildsaaslinks.com/seo">')
        ->toContain('<meta property="og:url" content="https://buildsaaslinks.com/seo" />')
        ->toContain('<meta property="og:type" content="article" />')
        ->toContain('<meta property="og:title" content="Exact Title From The Sheet" />')
        ->toContain('<meta property="og:description" content="A description." />')
        ->toContain('<meta property="og:image" content="https://buildsaaslinks.com/assets/x.webp" />')
        ->toContain('<meta property="og:image:alt" content="An image" />');
});
