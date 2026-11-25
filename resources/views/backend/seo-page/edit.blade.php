@extends('backend.layouts.app')
@section('title')
	{{ __('Edit SEO Page') }}
@endsection
@section('content')
	<div class="py-4">
		<div class="d-flex justify-content-between w-100 flex-wrap">
			<h1 class="h4">{{ __('Edit SEO Page') }}: {{ $page->nav_label }}</h1>
			<div class="d-flex gap-2">
				<a href="{{ $page->url() }}" target="_blank" class="btn btn-sm btn-outline-gray-600 d-inline-flex align-items-center">
					<x-svg i="eye"/>
					{{ __('View') }}
				</a>
				<a href="{{ route('admin.seo-pages.index') }}" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
					<x-svg i="back"/>
					{{ __('Back') }}
				</a>
			</div>
		</div>
	</div>
	<div class="card card-body border-0 shadow mb-4">
		<form method="POST" action="{{ route('admin.seo-pages.update', $page) }}" enctype="multipart/form-data" id="seo-page-form"
			data-edit="1" data-has-children="{{ $hasChildren ? 1 : 0 }}" data-groups='@json($groupsByParent)'>
			@csrf
			@method('PUT')
			@include('backend.seo-page._form')
			<button class="btn btn-gray-800 mt-3" type="submit">{{ __('Save Changes') }}</button>
		</form>
	</div>
@endsection
@push('script')
	<script src="{{ asset('backend/js/mammoth.browser.min.js') }}"></script>
	<script src="{{ asset('backend/js/seo-page-docx-clean.js') }}"></script>
	<script src="{{ asset('backend/js/seo-page-docx-import.js') }}"></script>
	<script src="{{ asset('backend/js/seo-page-form.js') }}"></script>
@endpush
