@extends('frontend.layouts.app')
@section('title')
@yield('title')
@endsection
@section('content')

@if(isset($isPageBreadcrumb) && $isPageBreadcrumb)
@include('frontend.page.partial._breadcrumb')
@endif

@yield('page_content')
@endsection
{{-- user auth script part included here--}}
@include('frontend.layouts.user.partials._script')