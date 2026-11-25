<?php

namespace App\Support;

/**
 * Content for the TYG service landing pages that share resources/views/frontend/tyg/service.blade.php
 * (Figma: "Branding Services" node 845-528, "Development Services" node 913-541).
 * Packages themselves live in the services table (see TygServicePackagesSeeder) and are matched by slug.
 */
class TygServicePages
{
    public const BRANDING_SLUGS = [
        'brand-strategy-positioning',
        'brand-naming-tagline',
        'logo-design',
        'visual-brand-identity',
        'brand-bible-guidelines',
        'brand-voice-messaging',
        'packaging-design',
        'social-media-brand-kit',
        'social-media-content-strategy',
        'campaign-ad-creative-design',
    ];

    public const DEVELOPMENT_SLUGS = [
        'landing-page-design-development',
        'business-website-development',
        'saas-website-development',
        'shopify-store-development',
        'wordpress-website-development',
        'website-ux-ui-audit',
        'website-speed-optimisation',
        'website-maintenance',
        'api-third-party-integrations',
        'custom-web-application-development',
    ];

    public static function get(string $key): array
    {
        return match ($key) {
            'branding' => self::branding(),
            'development' => self::development(),
        };
    }

    private static function branding(): array
    {
        return [
            'key' => 'branding',
            'slugs' => self::BRANDING_SLUGS,
            'title' => 'Branding Services',
            'meta' => 'Brand strategy, naming, logo and visual identity services that make your business instantly recognisable. Productized branding with fixed scope and pricing.',
            'hero' => [
                'lines' => ['Build a brand', 'people'],
                'em' => 'Remember',
                'sub' => 'Strategic branding that builds trust, sets you apart and turns attention into loyal customers.',
                'cta' => 'Get Started With Branding',
                'image' => 'svc-branding.png',
                'alt' => 'Brand guidelines book, business cards and logo mark',
                'pill' => 'Branding|Identity|Logo|Voice',
                'chips' => [
                    ['Build<br>Trust', '<path d="M16 4l10 4v7c0 7-4.5 11-10 13C10.5 26 6 22 6 15V8z"/><path d="M11 16l3.5 3.5L21 12"/>'],
                    ['Stand Out<br>Instantly', '<path d="M16 3l3.6 7.4 8.1 1.2-5.9 5.7 1.4 8.1L16 21.6l-7.2 3.8 1.4-8.1-5.9-5.7 8.1-1.2z"/>'],
                    ['Grow Your<br>Business', '<path d="M4 28V20M10 28V16M16 28V12M22 28V6M20 8l4-4 4 4"/>'],
                ],
            ],
            'why' => [
                'heading' => 'Why branding <em>matters ?</em>',
                'lead' => 'Strong branding helps your business stand out, build trust, create recognition and attract the right customers.',
                'cards' => [
                    ['green', 'Build', 'Trust', 'Create a professional identity that builds customer confidence and lasting relationships.'],
                    ['cream', 'Stand', 'Design', 'Differentiate your business with a unique identity, consistent visuals and messaging.'],
                    ['pink', 'Drive', 'Growth', 'Build brand recognition, attract customers and strengthen your market presence.'],
                ],
            ],
            'packages' => 'Our branding<br>services <em>get known</em>',
            'process' => [
                'heading' => 'Our branding<br><em>process</em>',
                'intro' => 'A simple approach to building a strong and memorable brand.',
                'image_alt' => 'Brand strategist planning an identity system',
                'steps' => [
                    ['Understand your business', 'Discover your brand goals, audience and market.', 'Discovery'],
                    ['Build strategy', 'Define positioning, personality and the message that makes you different.', 'Positioning'],
                    ['Design identity', 'Craft the logo, colours, type and visual system that bring the strategy to life.', 'Identity'],
                    ['Execute & refine', 'Roll the brand out across every touchpoint and refine it with real feedback.', 'Rollout'],
                ],
            ],
            'cta' => [
                'heading' => 'Ready to get known ?',
                'text' => 'Stop blending in with competitors in your industry.<br>Partner with TYG to build a brand your customers recognise, trust and remember.',
                'button' => 'Start Your Branding Project',
            ],
        ];
    }

    private static function development(): array
    {
        return [
            'key' => 'development',
            'slugs' => self::DEVELOPMENT_SLUGS,
            'title' => 'Development Services',
            'meta' => 'Fast, conversion-focused websites, stores and web apps engineered to rank, scale and sell. Productized web development with fixed scope and pricing.',
            'hero' => [
                'lines' => ['Websites built', 'to perform &'],
                'em' => 'Convert',
                'sub' => 'Fast, secure and scalable websites, stores and web apps engineered to grow your business.',
                'cta' => 'Get Started With Development',
                'image' => 'svc-development.png',
                'alt' => 'Responsive website shown on a laptop and phone with code editor',
                'pill' => 'Websites|Stores|Web Apps|APIs',
                'chips' => [
                    ['Lightning<br>Fast Sites', '<path d="M18 3L6 18h9l-2 11 12-15h-9z"/>'],
                    ['Built To<br>Convert', '<circle cx="16" cy="16" r="11"/><circle cx="16" cy="16" r="6"/><circle cx="16" cy="16" r="1.5"/>'],
                    ['Scale With<br>Confidence', '<path d="M4 28V20M10 28V16M16 28V12M22 28V6M20 8l4-4 4 4"/>'],
                ],
            ],
            'why' => [
                'heading' => 'Why choose our <em>development services?</em>',
                'lead' => 'Get innovative, scalable and reliable solutions to turn your ideas into powerful digital products.',
                'cards' => [
                    ['green', 'Cutting-Edge', 'Technology', 'Leverage the latest technologies to build fast, secure and scalable web solutions.'],
                    ['cream', 'Quality &', 'Performance', 'Deliver high-quality development with a focus on performance, security and reliability.'],
                    ['pink', 'Dedicated', 'Support', 'Get ongoing support and maintenance to keep your digital assets running smoothly.'],
                ],
            ],
            'packages' => 'Our development<br>services <em>get built</em>',
            'process' => [
                'heading' => 'Our development<br><em>process</em>',
                'intro' => 'A clear, collaborative approach from first idea to a fast, launched product.',
                'image_alt' => 'Product lead reviewing a website build plan',
                'steps' => [
                    ['Understand your business', 'Map your goals, users and the features your site or product needs.', 'Discovery'],
                    ['Plan & design', 'Turn requirements into sitemaps, wireframes and a polished UI design.', 'Design'],
                    ['Build & test', 'Develop, integrate and test across devices for speed, security and SEO.', 'Build'],
                    ['Launch & support', 'Go live smoothly, then monitor, maintain and improve after launch.', 'Launch'],
                ],
            ],
            'cta' => [
                'heading' => 'Ready to get built ?',
                'text' => 'Stop losing customers to slow, outdated websites.<br>Partner with TYG to launch a fast, scalable site engineered to convert.',
                'button' => 'Start Your Development Project',
            ],
        ];
    }
}
