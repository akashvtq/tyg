@extends('backend.layouts.app')
@section('title')
	{{ __('SEO Pages') }}
@endsection
@section('content')
	@php
		// Query values can arrive as arrays (?search[]=x); echo only strings.
		$currentFilter = fn (string $key) => is_string(request()->query($key)) ? request()->query($key) : '';
	@endphp
	<div class="py-4">
		<div class="d-flex justify-content-between w-100 flex-wrap">
			<div class="mb-3 mb-lg-0">
				<h1 class="h4">{{ __('SEO Pages') }}</h1>
			</div>
			@can('seo-page-manage')
				<div class="btn-toolbar mb-md-0 mb-2">
					<a href="{{ route('admin.seo-pages.create') }}" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
						<x-svg i="plus"/>
						{{ __('Add New') }}
					</a>
				</div>
			@endcan
		</div>
	</div>

	<form method="GET" action="{{ route('admin.seo-pages.index') }}" class="card card-body border-0 shadow mb-4">
		<div class="row g-3 align-items-end">
			<div class="col-md-4">
				<label class="form-label" for="search">{{ __('Search') }}</label>
				<input class="form-control" type="search" name="search" id="search" value="{{ $currentFilter('search') }}" placeholder="{{ __('Title, nav label or keyword') }}">
			</div>
			<div class="col-md-3">
				<label class="form-label" for="status">{{ __('Status') }}</label>
				<select class="form-select" name="status" id="status">
					<option value="">{{ __('All') }}</option>
					<option value="active" @selected($currentFilter('status') === 'active')>{{ __('Active') }}</option>
					<option value="draft" @selected($currentFilter('status') === 'draft')>{{ __('Draft') }}</option>
				</select>
			</div>
			<div class="col-md-3">
				<label class="form-label" for="cluster">{{ __('Cluster') }}</label>
				<select class="form-select" name="cluster" id="cluster">
					<option value="">{{ __('All') }}</option>
					@foreach($clusters as $cluster)
						<option value="{{ $cluster }}" @selected($currentFilter('cluster') === $cluster)>{{ ucfirst($cluster) }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-2 d-flex gap-2">
				<button class="btn btn-gray-800" type="submit">{{ __('Filter') }}</button>
				@if($filtered)
					<a class="btn btn-outline-gray-600" href="{{ route('admin.seo-pages.index') }}">{{ __('Reset') }}</a>
				@endif
			</div>
		</div>
	</form>

	<div class="card border-0 shadow mb-4">
		<div class="table-responsive">
			<table class="table table-centered table-nowrap mb-0 rounded align-items-center">
				<thead>
				<tr>
					<th class="border-bottom">{{ __('Nav label') }}</th>
					<th class="border-bottom">{{ __('Level') }}</th>
					<th class="border-bottom">{{ __('Group') }}</th>
					<th class="border-bottom">{{ __('Cluster') }}</th>
					<th class="border-bottom">{{ __('Path') }}</th>
					<th class="border-bottom">{{ __('Status') }}</th>
					<th class="border-bottom">{{ __('Last updated') }}</th>
					@can('seo-page-manage')
						<th class="border-bottom">{{ __('Action') }}</th>
					@endcan
				</tr>
				</thead>
				<tbody>
				@foreach($rows as $row)
					<tr>
						<td style="padding-left: {{ $filtered ? 16 : 16 + ($row->depth - 1) * 24 }}px">
							<span class="fw-bold d-block">{{ $row->nav_label }}</span>
							<small class="text-muted text-wrap d-block" style="max-width: 360px">{{ $row->title }}</small>
							@unless($row->hasFeaturedImageFile())
								<span class="badge bg-warning text-dark">{{ __('Missing image') }}</span>
							@endunless
						</td>
						<td><span class="badge bg-gray-600">{{ [1 => __('Category'), 2 => __('Subcategory'), 3 => __('Topic')][$row->depth] }}</span></td>
						<td>{{ $row->nav_group }}</td>
						<td>{{ $row->cluster ? ucfirst($row->cluster) : '' }}</td>
						<td><a href="{{ $row->url() }}" target="_blank">/{{ $row->path }}</a></td>
						<td><span class="fw-bold text-{{ $row->is_active ? 'success' : 'danger' }}">{{ $row->is_active ? __('Active') : __('Draft') }}</span></td>
						<td>{{ $row->updated_at->format('d M, Y') }}</td>
						@can('seo-page-manage')
							<td>
								<a href="{{ route('admin.seo-pages.edit', $row) }}" class="edit" title="{{ __('Edit') }}"><x-svg i="edit"/></a>
								@if($row->depth < \App\Models\SeoPage::MAX_DEPTH)
									<a href="{{ route('admin.seo-pages.create', ['parent_id' => $row->id]) }}" class="edit" title="{{ __('Add child') }}"><x-svg i="plus"/></a>
								@endif
								<button type="button" data-id="{{ $row->id }}" class="btn btn-link p-0 delete" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}"><x-svg i="delete"/></button>
							</td>
						@endcan
					</tr>
				@endforeach
				</tbody>
			</table>
			@if($rows->isEmpty())
				<h4 class="text-center text-muted py-3">{{ __('No Data Available') }}</h4>
			@endif
		</div>
	</div>
@endsection
@section('script')
	<script>
        $(document).ready(function () {
            'use strict';
            $('.delete').on('click', function () {
                let url = "{{ route('admin.seo-pages.destroy', ['seo_page' => ':id']) }}".replace(':id', $(this).data('id'));
                deleteItem(url);
            });
        });
	</script>
@endsection
