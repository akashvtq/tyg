<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - There You Grow</title>
    <meta name="description" content="@yield('meta_description', $metaDescription ?? setting('meta_description'))">
    <link rel="shortcut icon" href="{{ asset(setting('site_favicon')) }}" type="image/x-icon"/>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Poppins:wght@300;400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/tyg/css/tyg.css') }}?v={{ env('JS_VERSION', '1') }}">
    @stack('style')
</head>
<body class="tyg @yield('body_class')">

<div class="tyg-cursor" aria-hidden="true"></div>

@include('frontend.tyg.partials.header')
@include('frontend.tyg.partials.cart')

<main id="smooth">
    @yield('content')
</main>

@include('frontend.tyg.partials.footer')

<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js"></script>
<script>
    window.TYG = {
        currency: @json(setting('currency_symbol') ?: '$'),
        checkoutUrl: @json(route('user.order.services')),
        servicesUrl: @json(route('tyg.seo-services')),
    };
</script>
<script src="{{ asset('frontend/tyg/js/cart.js') }}?v={{ env('JS_VERSION', '1') }}"></script>
<script src="{{ asset('frontend/tyg/js/tyg.js') }}?v={{ env('JS_VERSION', '1') }}"></script>
@stack('script')
</body>
</html>
