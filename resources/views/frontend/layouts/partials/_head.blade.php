<head>
	<meta charset="{{ setting('meta_charset') }}">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="title" content="@stack('meta_title'){{ $metaTitle ?? setting('meta_title') }}">
	<meta name="description" content="{{ $metaDescription ?? setting('meta_description') }}">
	<meta name="keywords" content="{{ setting('meta_keywords') }}" />
	<meta property="og:image" content="{{ $metaImage ?? asset(setting('meta_image')) }}" />
	@isset($metaImageAlt)
		<meta property="og:image:alt" content="{{ $metaImageAlt }}" />
	@endisset
	@isset($canonicalUrl)
		<link rel="canonical" href="{{ $canonicalUrl }}">
		<meta property="og:url" content="{{ $canonicalUrl }}" />
	@endisset
	@isset($ogType)
		<meta property="og:type" content="{{ $ogType }}" />
		<meta property="og:title" content="{{ $metaTitle ?? setting('meta_title') }}" />
		<meta property="og:description" content="{{ $metaDescription ?? setting('meta_description') }}" />
	@endisset

	@isset($fullTitle)
		<title>{{ $fullTitle }}</title>
	@else
		<title>@yield('title') - {{ setting('site_title')  }}</title>
	@endisset
	@stack('schema')
	<link rel="shortcut icon" href="{{ asset(setting('site_favicon')) }}" type="image/x-icon"/>
	<link rel="stylesheet" href="{{ asset('general/css/bootstrap.min.css') }}">

	<link rel="stylesheet" href="{{ asset('frontend/css/meanmenu.css') }}">
	<link rel="stylesheet" href="{{ asset('frontend/css/all.min.css') }}">
	<link rel="stylesheet" href="{{ asset('frontend/css/swiper-bundle.min.css') }}">
	<link rel="stylesheet" href="{{ asset('frontend/css/magnific-popup.css') }}">
	<link rel="stylesheet" href="{{ asset('frontend/css/animate.css') }}">
	<link rel="stylesheet" href="{{ asset('frontend/css/nice-select.css') }}">
	<link rel="stylesheet" href="{{ asset('general/css/simple-notify.min.css?v2.01') }}" />
	<link rel="stylesheet" href="{{ asset('frontend/css/style.css')."?".env("JS_VERSION","2.0") }}">
	<link rel="stylesheet" href="{{ asset('frontend/css/custom.css?v2.01') }}">
	<link rel="stylesheet" href="{{ asset('frontend/css/home.css')."?".env("JS_VERSION","2.0") }}">
	<link rel="stylesheet" href="{{ asset('frontend/css/video-js.css') }}">
	@if(setting('site_preloader') !== 'none')
		<link rel="stylesheet" href="{{ asset('frontend/css/preloader.css?v2.01') }}">
	@endif

	{{--Custom CSS--}}
	@php($css = \App\Models\SiteCustom::value('css'))
	@if($css->is_active)
		<style>
			{{ $css->value }}
		</style>
	@endif


	{{--site color set dynamically--}}
	@include('frontend.layouts.partials._site_color')
	{{--end site color --}}

	{{-- TYG theme: fonts, design tokens + chrome, then the skin that re-themes legacy components --}}
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800;900&family=Poppins:wght@300;400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('frontend/tyg/css/tyg.css') }}?v={{ env('JS_VERSION', '1') }}">
	<link rel="stylesheet" href="{{ asset('frontend/tyg/css/tyg-legacy.css') }}?v={{ env('JS_VERSION', '1') }}">

	@yield('style')
	@stack('style')
</head>
