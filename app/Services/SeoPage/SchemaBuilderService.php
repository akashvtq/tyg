<?php

namespace App\Services\SeoPage;

class SchemaBuilderService
{
    /**
     * @param  list<array{name: string, url: string}>  $crumbs
     */
    public function breadcrumbList(array $crumbs): array
    {
        $items = [];
        foreach (array_values($crumbs) as $index => $crumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * @param  array{headline: string, description: string, url: string, image: ?string, datePublished: string,
     *               dateModified: string, authorName: ?string, publisherName: string, publisherLogo: string}  $article
     */
    public function article(array $article): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article['headline'],
            'description' => $article['description'],
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $article['url']],
            'datePublished' => $article['datePublished'],
            'dateModified' => $article['dateModified'],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $article['publisherName'],
                'logo' => ['@type' => 'ImageObject', 'url' => $article['publisherLogo']],
            ],
        ];

        if (! empty($article['image'])) {
            $schema['image'] = [$article['image']];
        }

        if (! empty($article['authorName'])) {
            $schema['author'] = ['@type' => 'Person', 'name' => $article['authorName']];
        }

        return $schema;
    }

    /**
     * @param  list<array{question: string, answer: string}>  $faq
     */
    public function faqPage(array $faq): ?array
    {
        if ($faq === []) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn (array $item) => [
                '@type' => 'Question',
                'name' => $item['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['answer']],
            ], array_values($faq)),
        ];
    }
}
