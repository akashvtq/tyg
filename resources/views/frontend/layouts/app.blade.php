<!DOCTYPE html>
{{-- Public (non-TYG) pages: legacy content rendered inside the TYG chrome — header, cart, search, footer,
     fonts and palette. Old component CSS still loads for layout; tyg-legacy.css re-skins it. --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ session('dir') ??  config('app.dir') }}" data-theme="light">

{{--Head Part Included Here--}}
@include('frontend.layouts.partials._head')
<body class="tyg-legacy">

<div class="tyg-cursor" aria-hidden="true"></div>
{{-- Hidden stand-ins for elements the old script.js expects (old cursor + back-to-top ring); TYG replaces both --}}
<div class="tyg-legacy-stub" aria-hidden="true">
	<div class="mouse-cursor cursor-outer"></div><div class="mouse-cursor cursor-inner"></div>
	<div class="scroll-up"><svg viewBox="0 0 10 10"><path d="M0 0L10 10"/></svg></div>
</div>

<div class="tyg-chrome">
	@include('frontend.tyg.partials.header')
	@include('frontend.tyg.partials.cart')
</div>

@if(setting('cookie_status'))
	@include('frontend.layouts.partials._cookies')
@endif

{{--Dynamic Body Part Included Here--}}
<main>
	@yield('content')
</main>

<div class="tyg-chrome">
	@include('frontend.tyg.partials.footer')
</div>

{{--Script Part Included Here--}}
@include('frontend.layouts.partials._script')

<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
<script>
	window.TYG = {
		currency: @json(setting('currency_symbol') ?: '$'),
		checkoutUrl: @json(route('user.order.services')),
		servicesUrl: @json(route('tyg.seo-services')),
	};
</script>
<script src="{{ asset('frontend/tyg/js/cart.js') }}?v={{ env('JS_VERSION', '1') }}"></script>
<script src="{{ asset('frontend/tyg/js/tyg.js') }}?v={{ env('JS_VERSION', '1') }}"></script>
</body>
</html>
