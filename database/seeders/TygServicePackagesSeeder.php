<?php

namespace Database\Seeders;

use App\Models\Services;
use App\Support\TygServicePages;
use Illuminate\Database\Seeder;

/**
 * Seeds the productized Branding and Development packages shown on the TYG service pages
 * (Figma "Branding Services" node 845-528 and "Development Services" node 913-541).
 * Safe to re-run: rows are matched on slug and updated in place.
 *
 * php artisan db:seed --class=TygServicePackagesSeeder
 */
class TygServicePackagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->seed(TygServicePages::BRANDING_SLUGS, [
            ['Brand Strategy & Positioning', 999, '10–14 business days', [
                'Brand discovery questionnaire',
                '3-competitor analysis',
                'Target audience profile',
                'Differentiation & positioning statement',
                'Value proposition and 15–20-page strategy deck',
            ]],
            ['Brand Naming & Tagline', 499, '7–10 business days', [
                'Brand naming brief',
                '15 name options',
                'Shortlist of 5 with rationale',
                '5 tagline options',
                'Preliminary domain & trademark screening',
            ]],
            ['Logo Design', 399, '7–10 business days', [
                '3 initial logo concepts',
                '1 selected final direction',
                'Primary/secondary versions',
                'Monochrome variations',
                'Colour palette and editable files',
            ]],
            ['Visual Brand Identity', 999, '12–18 business days', [
                'Logo system & colour palette',
                'Typography & supporting elements',
                'Image direction',
                '3 application mockups',
                'Editable assets',
            ]],
            ['Brand Bible / Brand Guidelines', 699, '7–10 business days', [
                '20–25-page brand guidelines',
                'Logo usage & colours',
                'Fonts & visual rules',
                'Imagery & messaging principles',
                'Examples',
            ]],
            ['Brand Voice & Messaging', 599, '7–10 business days', [
                'Brand voice definition',
                'Tone guidelines & key messages',
                'Value proposition refinement',
                'Do’s / Don’ts & copy examples',
                '10–15-page messaging guide',
            ]],
            ['Packaging Design', 499, '10–15 business days', [
                'Packaging design for 1 SKU',
                '2 initial concepts',
                'Final production artwork',
                'Print-ready files',
                '2 mockups',
            ]],
            ['Social Media Brand Kit', 349, '5–7 business days', [
                'Instagram profile visual direction',
                '6 editable post templates',
                '3 story templates',
                '5 highlight covers',
                'Profile image & mini usage guide',
            ]],
            ['Social Media Content Strategy', 499, '7–10 business days', [
                'Audience analysis',
                'Competitor review of 3 brands',
                '4 content pillars',
                'Platform recommendations',
                '30-day content calendar & 15 ideas',
            ]],
            ['Campaign & Ad Creative Design', 399, '7–10 business days', [
                '1 campaign creative direction',
                '2 initial concepts',
                '5 static ad designs',
                '2 size adaptations per design',
                'Editable source files',
            ]],
        ]);

        $this->seed(TygServicePages::DEVELOPMENT_SLUGS, [
            ['Landing Page Design & Development', 99, '7–10 business days', [
                'Custom landing page',
                'Responsive design',
                'Lead form, basic SEO',
                'Analytics setup',
            ]],
            ['Business Website Development', 1499, '15–20 business days', [
                'Up to 5 pages',
                'Responsive design',
                'Contact form, CMS',
                'Basic SEO',
            ]],
            // Figma shows placeholder bullets (copied from Logo Design) on this card — replace with real scope when confirmed.
            ['SaaS Website Development', 399, '7–10 business days', [
                'Product-led marketing site',
                'Pricing & feature pages',
                'Sign-up / demo flow integration',
                'Responsive design, basic SEO',
            ]],
            ['Shopify Store Development', 1499, '15–20 business days', [
                'Custom store setup',
                'Payment integration',
                'Theme customization',
                'Basic SEO',
            ]],
            ['WordPress Website Development', 1199, '12–18 business days', [
                'Up to 6 pages',
                'Custom theme',
                'Essential plugins',
                'Basic SEO',
            ]],
            ['Website UX/UI Audit', 399, '4–5 business days', [
                'Evaluate UX & UI',
                'Actionable insights',
                'Improvement roadmap',
                'Performance tips',
            ]],
            ['Website Speed Optimisation', 499, '3–7 business days', [
                'Performance audit',
                'Speed improvements',
                'Optimisation report',
                'Better user experience',
            ]],
            ['Website Maintenance', 199, 'per month', [
                'Updates & backups',
                'Security monitoring',
                'Bug fixes',
                'Performance checks',
            ]],
            ['API & Third-Party Integrations', 499, '5–7 business days', [
                'Connect APIs',
                'Integrate third-party tools',
                'Custom workflows',
                'Testing & support',
            ]],
            ['Custom Web Application Development', 3999, '20–40 business days', [
                'Tailored web applications',
                'Scalable architecture',
                'Advanced features',
                'Ongoing support',
            ]],
        ]);
    }

    private function seed(array $slugs, array $services): void
    {
        foreach ($services as $i => [$name, $price, $duration, $bullets]) {
            $list = '<ul>'.collect($bullets)->map(fn ($b) => '<li>'.e($b).'</li>')->implode('').'</ul>';

            $service = Services::firstOrNew(['slug' => $slugs[$i]]);
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
