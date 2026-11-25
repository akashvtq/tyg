<?php

namespace Database\Seeders;

use App\Models\Services;
use Illuminate\Database\Seeder;

/**
 * Seeds the productized SEO services shown on the TYG "SEO Services" page.
 * Safe to re-run: rows are matched on slug and updated in place.
 *
 * php artisan db:seed --class=TygSeoServicesSeeder
 */
class TygSeoServicesSeeder extends Seeder
{
    public const SLUGS = [
        'keyword-research',
        'technical-seo-audit',
        'complete-website-seo-audit',
        'competitor-seo-analysis',
        'topical-map-creation',
        'seo-content-strategy',
        'seo-article-methodology-briefs',
        'seo-content-writing',
        'on-page-seo-content-optimisation',
        'programmatic-seo-strategy',
    ];

    public function run(): void
    {
        $services = [
            ['Keyword Research', 399, '5–7 business days', [
                'Up to 500 relevant keywords, grouped by topic',
                'Search intent, search volume and ranking difficulty',
                'Priority scores to show which keywords to target first',
                'Keyword research spreadsheet',
            ]],
            ['Technical SEO Audit', 699, '7–10 business days', [
                'Crawl of up to 500 indexable URLs',
                'Crawlability and indexing analysis',
                'Core Web Vitals review',
                'Checks for canonical tags, sitemaps, structured data and JavaScript rendering',
                'Prioritised report of technical issues',
            ]],
            ['Complete Website SEO Audit', 1199, '10–14 business days', [
                'Full technical SEO audit',
                'Review of on-page SEO and content quality',
                'Site architecture and internal linking analysis',
                'Backlink profile and conversion path review',
                'Executive summary and 90-day action plan',
            ]],
            ['Competitor SEO Analysis', 499, '5–7 business days', [
                'SEO analysis of 3 competitors',
                'Keyword and content gaps you can target',
                'Backlink profile comparison',
                'Review of competitor site structures and top-performing pages',
                'Report outlining SEO opportunities',
            ]],
            ['Topical Map Creation', 999, '10–15 business days', [
                'Topic hierarchy for 1 core topic',
                'Up to 40 validated keyword groups mapped by search intent',
                'Recommended structure for core and supporting pages',
                'Internal linking recommendations across related pages',
            ]],
            ['SEO Content Strategy', 699, '7–10 business days', [
                'Content gap assessment',
                'Prioritised plan for up to 30 pages or articles',
                'Recommended formats and funnel stage for each piece',
                'Publishing sequence and 3-month editorial calendar',
            ]],
            ['SEO Article Methodology & Briefs', 499, '5–7 business days', [
                '5 detailed, writer-ready article briefs',
                'Search intent, heading structure and key questions for each article',
                'Relevant entities and attributes to cover, such as products, features or concepts',
                'Section-by-section guidance and writing instructions',
            ]],
            ['SEO Content Writing', 799, '10–15 business days', [
                '4 original SEO articles, up to 1,500 words each',
                'Source research and content aligned with search intent',
                'Clear headings, SEO titles and meta descriptions',
                'Editorial quality review before delivery',
            ]],
            ['On-Page SEO & Content Optimisation', 499, '7–10 business days', [
                'Optimisation of 5 existing URLs',
                'Updates to page titles, meta descriptions and headings',
                'Content improvements to better match search intent',
                'Internal linking improvements',
                'Before-and-after change log',
            ]],
            ['Programmatic SEO Strategy', 1499, '10–15 business days', [
                'Strategy for 1 programmatic SEO use case',
                'Keyword research and identification of repeatable search patterns',
                'Assessment of whether your data can support the proposed pages',
                'Page architecture and specifications for 2 page templates',
                'Internal linking plan and implementation roadmap',
            ]],
        ];

        foreach ($services as $i => [$name, $price, $duration, $bullets]) {
            $list = '<ul>'.collect($bullets)->map(fn ($b) => '<li>'.e($b).'</li>')->implode('').'</ul>';

            $service = Services::firstOrNew(['slug' => self::SLUGS[$i]]);
            $service->name = ['en' => $name];
            $service->price = $price;
            $service->description = ['en' => $list];
            // Same tagify format the admin panel stores: [{"value":"..."}]
            $service->tags = json_encode([['value' => $duration]]);
            $service->status = true;
            $service->save();
        }
    }
}
