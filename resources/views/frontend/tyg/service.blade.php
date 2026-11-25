{{-- Shared TYG service landing page (Branding / Development). Content: App\Support\TygServicePages --}}
@extends('frontend.tyg.layout')
@section('title', $page['title'])
@section('meta_description', $page['meta'])
@section('body_class', 'page-service page-'.$page['key'])

@section('content')
@php($img = fn ($f) => asset('frontend/tyg/'.$f))
@php($hero = $page['hero'])

{{-- ============ HERO (same frame + positions as the SEO hero) ============ --}}
<section class="tyg-hero tyg-hero--seo tyg-hero--svc">
        <div class="tyg-hero__copy">
            <h1 class="tyg-hero__title">
                @foreach($hero['lines'] as $line)
                    <span class="tyg-line"><span>{{ $line }}</span></span>
                @endforeach
                <span class="tyg-line"><span><em>{{ $hero['em'] }}</em></span></span>
            </h1>
            <p class="tyg-hero__sub" data-hero-fade>{{ $hero['sub'] }}</p>
            <a href="#packages" class="tyg-btn tyg-btn--red" data-hero-fade data-magnetic>
                {{ $hero['cta'] }}
                <svg viewBox="0 0 16 16" width="12" height="12"><path d="M2 8h11M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
            </a>
        </div>
        <div class="tyg-hero__visual">
            <div class="tyg-hero__blob tyg-hv" style="--x:690;--y:130;--w:600" data-hero-blob></div>
            <img src="{{ $img($hero['image']) }}" alt="{{ $hero['alt'] }}" class="tyg-hero__chart tyg-svc-hero__img tyg-hv" style="--x:720;--y:150;--w:560" data-hero-laptop>
            <div class="tyg-searchpill tyg-hv" style="--x:640;--y:150;--w:230" data-chip>
                <em data-typer="{{ $hero['pill'] }}">{{ explode('|', $hero['pill'])[0] }}</em>
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
            </div>
            @foreach($hero['chips'] as $i => [$label, $icon])
                @php($pos = [['628', '420', '610', '470'], ['1200', '120', '1190', '110'], ['1220', '470', '1200', '520']][$i])
                <div class="tyg-chip tyg-hv" style="--x:{{ $pos[0] }};--y:{{ $pos[1] }};--mx:{{ $pos[2] }};--my:{{ $pos[3] }}" data-chip>
                    <svg viewBox="0 0 32 32">{!! $icon !!}</svg>
                    <span>{!! $label !!}</span>
                </div>
            @endforeach
        </div>
    @include('frontend.tyg.partials.sections', ['section' => 'marquee'])
</section>

{{-- ============ WHY IT MATTERS ============ --}}
<section class="tyg-why">
    <img src="{{ $img('torn.png') }}" alt="" class="tyg-torn tyg-torn--l tyg-torn--l-wide" aria-hidden="true"><img src="{{ $img('torn.png') }}" alt="" class="tyg-torn tyg-torn--r" aria-hidden="true">
    <div class="tyg-container tyg-center">
        <h2 class="tyg-display tyg-display--md" data-split>{!! $page['why']['heading'] !!}</h2>
        <p class="tyg-lead tyg-lead--wide" data-reveal>{{ $page['why']['lead'] }}</p>
    </div>
    <div class="tyg-fan tyg-why__cards" data-fan data-fan-rot="8" data-fan-y="28">
        @foreach($page['why']['cards'] as [$tone, $word, $accent, $text])
            <div class="tyg-fan__card tyg-why__card tyg-why__card--{{ $tone }}" data-fan-card>
                <h4>{{ $word }} <span>{{ $accent }}</span></h4>
                <p>{{ $text }}</p>
            </div>
        @endforeach
    </div>
</section>

@include('frontend.tyg.partials.packages', ['services' => $services, 'heading' => $page['packages']])

@include('frontend.tyg.partials.process', [
    'heading' => $page['process']['heading'],
    'intro' => $page['process']['intro'],
    'steps' => $page['process']['steps'],
    'imageAlt' => $page['process']['image_alt'],
])

@include('frontend.tyg.partials.sections', ['section' => 'results'])

{{-- ============ CTA ============ --}}
<section class="tyg-cta tyg-dark">
    <div class="tyg-container tyg-center">
        <h2 class="tyg-display tyg-display--md" data-split>{{ $page['cta']['heading'] }}<br><span class="tyg-red">Let’s grow together</span></h2>
        <p class="tyg-cta__text" data-reveal>{!! $page['cta']['text'] !!}</p>
        <div class="tyg-cta__btns" data-reveal>
            <a href="#packages" class="tyg-btn tyg-btn--red tyg-btn--lg" data-magnetic>{{ $page['cta']['button'] }}</a>
            <a href="{{ url('/contact') }}" class="tyg-btn tyg-btn--ghost tyg-btn--lg" data-magnetic>Talk to Our Experts</a>
        </div>
        <p class="tyg-cta__small" data-reveal>No Long-Term Contracts Required <i>•</i> 24h Kickoff Guarantee <i>•</i> Custom Enterprise Scopes</p>
    </div>
</section>

@include('frontend.tyg.partials.sections', ['section' => 'explore', 'except' => $page['key']])
@include('frontend.tyg.partials.sections', ['section' => 'faq'])
@endsection
