@extends('frontend.tyg.layout')
@section('title', __('SEO Services'))
@section('meta_description', 'Strategic SEO solutions to rank higher, attract quality traffic and grow your business. Productized SEO services with fixed scope and pricing.')
@section('body_class', 'page-seo')

@section('content')
@php($img = fn ($f) => asset('frontend/tyg/'.$f))

{{-- ============ HERO ============ --}}
<section class="tyg-hero tyg-hero--seo">
        <div class="tyg-hero__copy">
            <h1 class="tyg-hero__title">
                <span class="tyg-line"><span>Boost your</span></span>
                <span class="tyg-line"><span>website’s</span></span>
                <span class="tyg-line"><span><em>Visibility</em></span></span>
            </h1>
            <p class="tyg-hero__sub" data-hero-fade>Strategic SEO solution to rank higher, attract quality traffic and grow your business.</p>
            <a href="#packages" class="tyg-btn tyg-btn--red" data-hero-fade data-magnetic>
                Get Started With SEO
                <svg viewBox="0 0 16 16" width="12" height="12"><path d="M2 8h11M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
            </a>
        </div>
        {{-- Positions are Figma pixels on the 1440 × 800 hero frame (see .tyg-hv in tyg.css) --}}
        <div class="tyg-hero__visual">
            <div class="tyg-hero__blob tyg-hv" style="--x:650;--y:196;--w:640" data-hero-blob></div>
            <img src="{{ $img('seo-hero.png') }}" alt="Rising bar chart with magnifying glass" class="tyg-hero__chart tyg-hv" style="--x:389;--y:122;--w:953" data-hero-laptop>
            <div class="tyg-searchpill tyg-hv" style="--x:814;--y:170;--w:226" data-chip><em data-typer="SEO|Rankings|Traffic|Leads">SEO</em>
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
            </div>
            <div class="tyg-chip tyg-hv" style="--x:628;--y:320;--mx:395;--my:330" data-chip>
                <svg viewBox="0 0 32 32"><path d="M6 26c2-6 6-10 14-14l6-6-2 8c-4 8-8 12-14 14z M10 18l4 4"/></svg>
                <span>Drive<br>Targeted<br>Traffic</span>
            </div>
            <div class="tyg-chip tyg-hv" style="--x:1188;--y:115;--mx:1060;--my:96" data-chip>
                <svg viewBox="0 0 32 32"><path d="M4 28V20M10 28V16M16 28V12M22 28V6M20 8l4-4 4 4"/></svg>
                <span>Rank Higher<br>on Google</span>
            </div>
            <div class="tyg-chip tyg-hv" style="--x:1243;--y:435;--mx:1100;--my:480" data-chip>
                <svg viewBox="0 0 32 32"><circle cx="16" cy="10" r="4"/><circle cx="7" cy="13" r="3"/><circle cx="25" cy="13" r="3"/><path d="M8 26c0-5 4-8 8-8s8 3 8 8M2 26c0-4 2-7 5-7M30 26c0-4-2-7-5-7"/></svg>
                <span>Grow Your<br>Business</span>
            </div>
        </div>
    @include('frontend.tyg.partials.sections', ['section' => 'marquee'])
</section>

{{-- ============ WHY SEO MATTERS ============ --}}
<section class="tyg-why">
    <img src="{{ $img('torn.png') }}" alt="" class="tyg-torn tyg-torn--l tyg-torn--l-wide" aria-hidden="true"><img src="{{ $img('torn.png') }}" alt="" class="tyg-torn tyg-torn--r" aria-hidden="true">
    <div class="tyg-container tyg-center">
        <h2 class="tyg-display tyg-display--md" data-split>Why SEO <em>matters ?</em></h2>
        <p class="tyg-lead tyg-lead--wide" data-reveal>SEO helps your business get discovered by the right audience at the exact micro-moment of need. It brings consistent, high-quality traffic, builds institutional credibility, and drives long-term revenue growth — completely insulated from per-click media inflation.</p>
    </div>
    <div class="tyg-fan tyg-why__cards" data-fan data-fan-rot="8" data-fan-y="28">
        <div class="tyg-fan__card tyg-why__card tyg-why__card--green" data-fan-card>
            <h4>Get Qualified <span>Leads</span></h4>
            <p>Attract sophisticated decision-makers actively researching solutions, drastically shortening pipeline cycles.</p>
        </div>
        <div class="tyg-fan__card tyg-why__card tyg-why__card--cream" data-fan-card>
            <h4>Increase Organic <span>Visibility</span></h4>
            <p>Dominate high-intent search queries where buyer decisions happen and secure permanent digital real estate.</p>
        </div>
        <div class="tyg-fan__card tyg-why__card tyg-why__card--pink" data-fan-card>
            <h4>Grow <span>Revenue</span></h4>
            <p>Build an owned, compounding discovery pipeline that retains multi-year momentum and drives bottom-line profitability.</p>
        </div>
    </div>
</section>

@include('frontend.tyg.partials.packages', ['services' => $seoServices, 'heading' => 'Everything your website<br>needs to <em>get found</em>'])

@include('frontend.tyg.partials.process', [
    'heading' => 'Our SEO<br><em>process</em>',
    'intro' => 'A clear and proven approach to deliver measurable results.',
    'imageAlt' => 'Strategist planning an SEO roadmap',
    'steps' => [
        ['Understand your business', 'Deep dive into your revenue drivers, audience segments, competitive moats, and category nuances.', 'Discovery'],
        ['Build strategy', 'Translate research into a prioritised roadmap of keywords, topics, technical fixes and content that will move the needle.', 'Roadmap'],
        ['Execute & optimise', 'Ship the fixes, briefs and content — then refine pages based on real ranking and engagement data.', 'Delivery'],
        ['Track results', 'Transparent reporting on rankings, traffic and conversions, with clear next steps every cycle.', 'Reporting'],
    ],
])

@include('frontend.tyg.partials.sections', ['section' => 'results'])

{{-- ============ CTA ============ --}}
<section class="tyg-cta tyg-dark">
    <div class="tyg-container tyg-center">
        <h2 class="tyg-display tyg-display--md" data-split>Ready to get found ?<br><span class="tyg-red">Let’s grow together</span></h2>
        <p class="tyg-cta__text" data-reveal>Stop letting competitors capture the highest-intent customers in your industry.<br>Partner with TYG to engineer an unassailable organic search footprint.</p>
        <div class="tyg-cta__btns" data-reveal>
            <a href="#packages" class="tyg-btn tyg-btn--red tyg-btn--lg" data-magnetic>Start Your SEO Project</a>
            <a href="{{ url('/contact') }}" class="tyg-btn tyg-btn--ghost tyg-btn--lg" data-magnetic>Talk to Our Experts</a>
        </div>
        <p class="tyg-cta__small" data-reveal>No Long-Term Contracts Required <i>•</i> 24h Kickoff Guarantee <i>•</i> Custom Enterprise Scopes</p>
    </div>
</section>

@include('frontend.tyg.partials.sections', ['section' => 'explore', 'except' => 'seo'])
@include('frontend.tyg.partials.sections', ['section' => 'faq'])
@endsection
