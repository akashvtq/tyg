@extends('frontend.tyg.layout')
@section('title', __('Home'))
@section('body_class', 'page-home')

@section('content')
@php($img = fn ($f) => asset('frontend/tyg/'.$f))

{{-- ============ HERO (Figma node 808-450) ============
     Centred headline on a soft pink pill, a hand-drawn red orbit and spark marks.
     Positions are Figma pixels on the 1440 × 800 frame (see .tyg-hv in tyg.css). --}}
<section class="tyg-hero tyg-hero--home">
        <div class="tyg-hero__visual" aria-hidden="true">
            <div class="tyg-hero__pill tyg-hv" style="--x:120;--y:200;--w:1261;--h:427" data-hero-blob></div>
            <svg class="tyg-hero__orbit tyg-hv" style="--x:40;--y:180;--w:1370;--h:450" viewBox="0 0 1370 450" preserveAspectRatio="none" data-hero-orbit>
                <ellipse cx="685" cy="236" rx="676" ry="150" transform="rotate(-10 685 236)"/>
            </svg>
            <svg class="tyg-hero__sparks tyg-hv" style="--x:1160;--y:155;--w:100;--h:125" viewBox="0 0 100 125" data-hero-sparks>
                <path d="M18 62 C 14 40, 18 18, 26 6"/><path d="M40 72 C 50 58, 62 48, 78 42"/><path d="M44 96 C 60 98, 76 104, 92 116"/>
            </svg>
        </div>

        <div class="tyg-hero__copy">
            <h1 class="tyg-hero__title">
                <span class="tyg-line"><span>Visibility &amp; Branding</span></span>
                <span class="tyg-line"><span>for Business</span></span>
                <span class="tyg-line tyg-hero__rotator" data-rotator>
                    <em class="is-on">SEO</em><em>Branding</em><em>Development</em><em>Content</em>
                </span>
            </h1>
            <a href="{{ url('/contact') }}" class="tyg-btn tyg-btn--red tyg-hero__cta" data-hero-fade data-magnetic>Let’s Grow Together</a>
        </div>

        <div class="tyg-trust tyg-hero__trust" data-hero-fade>
            <div class="tyg-trust__faces">
                @foreach(['t01', 't02', 't03', 't04', 't05'] as $a)
                    <img src="{{ $img('avatars/'.$a.'.png') }}" alt="">
                @endforeach
            </div>
            <p>Trusted by 500+ home<br>service owners</p>
        </div>
    @include('frontend.tyg.partials.sections', ['section' => 'marquee'])
</section>

{{-- ============ GET KNOWN / FOUND / BUILT ============ --}}
<section class="tyg-pillars">
    <img src="{{ $img('torn.png') }}" alt="" class="tyg-torn tyg-torn--l" aria-hidden="true"><img src="{{ $img('torn.png') }}" alt="" class="tyg-torn tyg-torn--r" aria-hidden="true">
    <div class="tyg-pillars__row" data-pillars>
        <a href="{{ route('tyg.branding-services') }}" class="tyg-pillar tyg-pillar--green">
            <small>Branding</small><strong>Get<br>Known.</strong>
        </a>
        <a href="{{ route('tyg.seo-services') }}" class="tyg-pillar tyg-pillar--cream">
            <small>SEO</small><strong>Get<br>Found.</strong>
        </a>
        <a href="{{ route('tyg.development-services') }}" class="tyg-pillar tyg-pillar--pink">
            <small>Development</small><strong>Get<br>Built.</strong>
        </a>
    </div>
</section>

{{-- ============ POWERING YOUR GROWTH + SERVICES SLIDER ============ --}}
<section class="tyg-power">
    <div class="tyg-container tyg-center tyg-power__head">
        <img src="{{ $img('burst.png') }}" alt="" class="tyg-power__burst" data-wiggle aria-hidden="true">
        <h2 class="tyg-mega" data-split>Powering<br>your<br><em>growth</em></h2>
        <img src="{{ $img('moon.png') }}" alt="" class="tyg-power__moon" data-float aria-hidden="true">
    </div>

    @php($slides = [
        ['SEO Services', 'Strategic SEO solutions designed to improve search visibility and organic growth.', ['Keyword Research', 'Technical SEO Audit', 'Complete Website SEO Audit', 'Competitor SEO Analysis', 'Topical Map Creation', 'SEO Content Strategy', 'SEO Article Methodology & Briefs', 'SEO Content Writing', 'On-Page SEO & Content Optimisation', 'Programmatic SEO Strategy'], route('tyg.seo-services'), 'svc-seo.png', 'green'],
        ['Branding', 'Identity systems that make ambitious brands instantly recognisable and remembered.', ['Brand Strategy & Positioning', 'Logo & Visual Identity', 'Brand Guidelines', 'Tone of Voice', 'Pitch Decks & Sales Collateral', 'Social Media Kits', 'Packaging Design', 'Ebook & Report Design'], route('tyg.branding-services'), 'svc-branding.png', 'pink'],
        ['Development', 'Fast, conversion-focused websites engineered to rank, scale and sell.', ['Website Design & UX', 'Custom Web Development', 'Landing Pages', 'E-Commerce Builds', 'Core Web Vitals Optimisation', 'CMS Integration', 'Programmatic Page Templates', 'Maintenance & Support'], route('tyg.development-services'), 'svc-development.png', 'cream'],
    ])
    <div class="tyg-container">
        <div class="tyg-slider" data-slider="services">
            <div class="tyg-slider__viewport">
                <div class="tyg-slider__track" data-track>
                    @foreach($slides as [$title, $intro, $items, $link, $image, $tone])
                        <article class="tyg-slide tyg-slide--{{ $tone }}" data-slide>
                            <div class="tyg-slide__img"><img src="{{ $img($image) }}" alt="{{ $title }}" loading="lazy"></div>
                            <div class="tyg-slide__body">
                                <h3>{{ $title }}</h3>
                                <svg class="tyg-squiggle" viewBox="0 0 160 10"><path d="M2 7 Q 40 1, 80 5 T 158 4"/></svg>
                                <p>{{ $intro }}</p>
                                <ul>@foreach($items as $item)<li>{{ $item }}</li>@endforeach</ul>
                                <a href="{{ $link }}" class="tyg-btn tyg-btn--red tyg-btn--sm">Learn More</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
            <div class="tyg-slider__ui">
                <div class="tyg-dots" data-dots></div>
                <div class="tyg-slider__arrows">
                    <button type="button" class="tyg-arrow" data-prev aria-label="Previous"><img src="{{ $img('arrow-left.png') }}" alt=""></button>
                    <button type="button" class="tyg-arrow" data-next aria-label="Next"><img src="{{ $img('arrow-right.png') }}" alt=""></button>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ PROCESS ============ --}}
<section class="tyg-process tyg-dark">
    <div class="tyg-container">
        <h2 class="tyg-display tyg-display--md tyg-center" data-split>From being invisible to<br>become impossible to <em>ignore</em></h2>

        <div class="tyg-process__steps" data-process>
            <svg class="tyg-process__mpath" aria-hidden="true"><path d=""/></svg>
            <svg class="tyg-process__path" viewBox="0 0 1000 1900" preserveAspectRatio="none" aria-hidden="true">
                <path d="M520 0 C 470 260, 40 300, 120 520 C 190 700, 520 640, 620 760 C 780 950, 900 1050, 700 1180 C 500 1300, 280 1280, 260 1060 C 240 900, 600 1000, 760 1300 C 900 1560, 760 1760, 600 1860"/>
            </svg>
            @foreach([
                ['We understand your business', 'We get to know your goals, audience, and challenges.'],
                ['We build your strategy', 'A clear roadmap for SEO, content, and digital growth.'],
                ['We create content that connects', 'Content that attracts, educates, and builds trust.'],
                ['We get you found', 'We help your business reach the right audience.'],
                ['We turn attention into growth', 'We measure, optimize, and keep improving results.'],
            ] as $i => [$t, $d])
                <div class="tyg-step {{ $i % 2 ? 'is-right' : '' }}" data-step>
                    <span class="tyg-step__no">0{{ $i + 1 }}.</span>
                    <h4>{{ $t }}</h4>
                    <p>{{ $d }}</p>
                </div>
            @endforeach
        </div>

        <div class="tyg-gameplan tyg-center">
            <span class="tyg-spark tyg-spark--l" aria-hidden="true"><i></i><i></i><i></i></span>
            <h2 class="tyg-display" data-split>Your growth.<br>Our game plan.</h2>
            <span class="tyg-spark tyg-spark--r" aria-hidden="true"><i></i><i></i><i></i></span>
            <div><a href="{{ url('/contact') }}" class="tyg-btn tyg-btn--cream tyg-btn--xl" data-reveal data-magnetic>Let's grow together</a></div>
        </div>
    </div>
</section>

@include('frontend.tyg.partials.sections', ['section' => 'results'])

{{-- ============ TEAM ============ --}}
<section class="tyg-team tyg-dark">
    <div class="tyg-container tyg-team__grid">
        <div>
            <h2 class="tyg-display tyg-display--md" data-split>Meet the minds<br>behind the<br><em>growth.</em></h2>
            <p class="tyg-team__text" data-reveal>A content-first team turning smart strategy into meaningful business growth.</p>
            <a href="{{ url('/about') }}" class="tyg-btn tyg-btn--red tyg-btn--lg" data-reveal data-magnetic>Get to know us</a>
        </div>
        <div class="tyg-team__visual" data-team>
            <div class="tyg-team__blob"></div>
            <img src="{{ $img('team-1-crop.png') }}" alt="TYG co-founder" class="tyg-team__p1">
            <img src="{{ $img('team-2-crop.png') }}" alt="TYG co-founder" class="tyg-team__p2">
            <svg class="tyg-team__spark" viewBox="0 0 50 60" aria-hidden="true"><path d="M14 4 L8 20"/><path d="M22 26 L40 18"/><path d="M20 40 L38 46"/></svg>
            <span class="tyg-team__arc"></span>
        </div>
    </div>
</section>

@include('frontend.tyg.partials.sections', ['section' => 'faq'])

@include('frontend.tyg.partials.sections', ['section' => 'testimonials'])
@endsection
