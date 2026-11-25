@extends('backend.layouts.app')
@section('title')
	{{ __('Create SEO Page') }}
@endsection
@section('content')
	<div class="py-4">
		<div class="d-flex justify-content-between w-100 flex-wrap">
			<h1 class="h4">{{ __('Create SEO Page') }}</h1>
			<a href="{{ route('admin.seo-pages.index') }}" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
				<x-svg i="back"/>
				{{ __('Back') }}
			</a>
		</div>
	</div>
	<div class="card card-body border-0 shadow mb-4">
		<form method="POST" action="{{ route('admin.seo-pages.store') }}" enctype="multipart/form-data" id="seo-page-form"
			data-edit="0" data-has-children="0" data-groups='@json($groupsByParent)'>
			@csrf
			@include('backend.seo-page._form')
			<button class="btn btn-gray-800 mt-3" type="submit">{{ __('Create Page') }}</button>
		</form>
	</div>
@endsection
@push('script')
	<script src="{{ asset('backend/js/mammoth.browser.min.js') }}"></script>
	<script src="{{ asset('backend/js/seo-page-docx-clean.js') }}"></script>
	<script src="{{ asset('backend/js/seo-page-docx-import.js') }}"></script>
	<script src="{{ asset('backend/js/seo-page-form.js') }}"></script>
@endpush
