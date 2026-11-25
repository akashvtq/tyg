<?php

use App\Services\SeoPage\SchemaBuilderService;

function articleInput(array $overrides = []): array
{
    return array_merge([
        'headline' => '27 Link Building Tools',
        'description' => 'Compare link building tools.',
        'url' => 'https://buildsaaslinks.com/seo/link-building/tools',
        'image' => 'https://buildsaaslinks.com/assets/general/images/seo-pages/link-building-tools.webp',
        'datePublished' => '2026-09-25T10:00:00+00:00',
        'dateModified' => '2026-09-26T10:00:00+00:00',
        'authorName' => 'Saurav Kumar',
        'publisherName' => 'Build Saas Links',
        'publisherLogo' => 'https://buildsaaslinks.com/assets/general/images/logo.png',
    ], $overrides);
}

it('builds a BreadcrumbList with 1-based positions', function () {
    $schema = (new SchemaBuilderService)->breadcrumbList([
        ['name' => 'Home', 'url' => 'https://buildsaaslinks.com'],
        ['name' => 'SEO', 'url' => 'https://buildsaaslinks.com/seo'],
    ]);

    expect($schema['@context'])->toBe('https://schema.org')
        ->and($schema['@type'])->toBe('BreadcrumbList')
        ->and($schema['itemListElement'][1])->toBe([
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'SEO',
            'item' => 'https://buildsaaslinks.com/seo',
        ]);
});

it('builds an Article with author, image and publisher', function () {
    $schema = (new SchemaBuilderService)->article(articleInput());

    expect($schema['@type'])->toBe('Article')
        ->and($schema['headline'])->toBe('27 Link Building Tools')
        ->and($schema['mainEntityOfPage'])->toBe(['@type' => 'WebPage', '@id' => 'https://buildsaaslinks.com/seo/link-building/tools'])
        ->and($schema['image'])->toBe(['https://buildsaaslinks.com/assets/general/images/seo-pages/link-building-tools.webp'])
        ->and($schema['author'])->toBe(['@type' => 'Person', 'name' => 'Saurav Kumar'])
        ->and($schema['publisher']['@type'])->toBe('Organization')
        ->and($schema['publisher']['logo']['url'])->toBe('https://buildsaaslinks.com/assets/general/images/logo.png')
        ->and($schema['datePublished'])->toBe('2026-09-25T10:00:00+00:00')
        ->and($schema['dateModified'])->toBe('2026-09-26T10:00:00+00:00');
});

it('omits author and image when they are missing', function () {
    $schema = (new SchemaBuilderService)->article(articleInput(['authorName' => null, 'image' => null]));

    expect($schema)->not->toHaveKey('author')->not->toHaveKey('image');
});

it('builds a FAQPage, or nothing when there are no questions', function () {
    $builder = new SchemaBuilderService;
    $schema = $builder->faqPage([['question' => 'Is it safe?', 'answer' => '<p>Yes.</p>']]);

    expect($schema['@type'])->toBe('FAQPage')
        ->and($schema['mainEntity'][0])->toBe([
            '@type' => 'Question',
            'name' => 'Is it safe?',
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => '<p>Yes.</p>'],
        ])
        ->and($builder->faqPage([]))->toBeNull();
});
