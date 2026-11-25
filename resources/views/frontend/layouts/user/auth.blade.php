<!doctype html>
<html lang="en">

{{-- TYG theme for login / register / password pages only (the user dashboard keeps its own styles) --}}
@push('style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Poppins:wght@300;400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/tyg/css/tyg-auth.css') }}?v={{ env('JS_VERSION', '1') }}">
@endpush

{{-- user auth head part included here --}}
@include('frontend.layouts.user.partials._head')

<body class="tyg-auth d-flex flex-column">
<span class="tyg-auth__blob tyg-auth__blob--green" aria-hidden="true"></span>
<span class="tyg-auth__blob tyg-auth__blob--pink" aria-hidden="true"></span>
<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="tyg-auth__logo" aria-label="{{ title(setting('site_title')) }} home">TYG</a>
        </div>
        {{-- user auth content part display here --}}
        @yield('user-auth-content')
        <p class="tyg-auth__back"><a href="{{ route('home') }}">← {{ __('Back to website') }}</a></p>
    </div>
</div>
{{-- user auth script part included here--}}
@include('frontend.layouts.user.partials._script')
</body>
</html>
