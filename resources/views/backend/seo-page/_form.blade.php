@php
	$isEdit = $page->exists;
	// After a failed submit, refill only what the browser sent: an unchecked box or a removed
	// FAQ row is absent from old input and must not fall back to the saved value.
	$hasOldInput = session()->hasOldInput();
	$faqRows = $hasOldInput ? (array) old('faq', []) : ($page->faq ?? []);
@endphp

@if ($errors->any())
	<div class="alert alert-danger" role="alert">
		<ul class="mb-0">
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif

<div class="row">
	{{-- Placement --}}
	<div class="col-md-6 mb-3">
		<label class="form-label" for="parent_id">{{ __('Parent') }}</label>
		<select class="form-select" name="parent_id" id="parent_id" @disabled($isEdit && $page->depth === 1)>
			@if(! $isEdit || $page->depth === 1)
				<option value="">{{ __('None (category)') }}</option>
			@endif
			@foreach($parents->groupBy('depth') as $depth => $options)
				<optgroup label="{{ $depth == 1 ? __('Categories') : __('Subcategories') }}">
					@foreach($options as $option)
						<option value="{{ $option->id }}" @selected((string) old('parent_id', $page->parent_id) === (string) $option->id)>/{{ $option->path }}</option>
					@endforeach
				</optgroup>
			@endforeach
		</select>
		<small class="text-muted d-none" id="category-route-notice">{{ __('A new category also needs its slug added to SeoPage::ROUTED_CATEGORIES (app/Models/SeoPage.php) before its URLs work.') }}</small>
	</div>
	<div class="col-md-3 mb-3">
		<label class="form-label" for="sort_order">{{ __('Sort order') }}</label>
		<input class="form-control" type="number" min="0" name="sort_order" id="sort_order" value="{{ old('sort_order', $page->sort_order ?? 0) }}">
	</div>
	<div class="col-md-3 mb-3">
		<label class="form-label" for="nav_group">{{ __('Group') }}</label>
		<input class="form-control" name="nav_group" id="nav_group" list="nav-group-options" maxlength="60" value="{{ old('nav_group', $page->nav_group) }}" placeholder="{{ __('e.g. Link Acquisition Methods') }}">
		<datalist id="nav-group-options"></datalist>
	</div>

	{{-- Names --}}
	<div class="col-md-12 mb-3">
		<label class="form-label" for="title">{{ __('Title (H1)') }}</label>
		<input class="form-control" name="title" id="title" maxlength="255" required value="{{ old('title', $page->title) }}">
	</div>
	<div class="col-md-6 mb-3">
		<label class="form-label" for="nav_label">{{ __('Nav label') }}</label>
		<input class="form-control" name="nav_label" id="nav_label" maxlength="60" required value="{{ old('nav_label', $page->nav_label) }}">
		<small class="text-muted">{{ __('Short name for the breadcrumb and menus (sheet column "Topical Map").') }}</small>
	</div>
	<div class="col-md-6 mb-3">
		<label class="form-label" for="slug">{{ __('Slug') }}</label>
		<input class="form-control" name="slug" id="slug" maxlength="60" required value="{{ old('slug', $page->slug) }}" data-original="{{ $page->slug }}">
		<div class="alert alert-warning mt-2 mb-0 py-2 d-none" id="slug-change-warning">
			{{ __('Changing the slug also changes the URLs of every page under this one. Old URLs will return 404.') }}
		</div>
	</div>
	<div class="col-md-12 mb-3">
		<label class="form-label" for="excerpt">{{ __('Excerpt') }}</label>
		<textarea class="form-control" name="excerpt" id="excerpt" rows="2" maxlength="1000">{{ old('excerpt', $page->excerpt) }}</textarea>
		<small class="text-muted">{{ __('Shown on the parent page\'s cards; also the fallback meta description.') }}</small>
	</div>

	{{-- Content --}}
	<div class="col-md-12 mb-3">
		<div class="d-flex justify-content-between align-items-center mb-2">
			<label class="form-label mb-0" for="content">{{ __('Content') }}</label>
			<div>
				<input type="file" id="docx-import-input" accept=".docx" class="d-none">
				<button type="button" class="btn btn-sm btn-outline-gray-600" id="docx-import-button">{{ __('Import .docx') }}</button>
			</div>
		</div>
		<div id="docx-import-report"></div>
		{{-- Not `.summernote`: seo-page-form.js initialises this editor with its own toolbar and image upload. --}}
		<textarea class="form-control seo-page-editor" name="content" id="content"
			data-image-upload-url="{{ route('admin.seo-pages.images.store') }}">{{ old('content', $page->content) }}</textarea>
		<small class="text-muted d-block mt-1">{{ __('Images: use the picture button, drag and drop or paste (JPG, PNG or WebP, max 2 MB). Each one is uploaded and needs alt text; click an image to edit its alt text.') }}</small>
		<small class="text-info d-none" id="content-image-status" role="status" aria-live="polite"></small>
	</div>

	{{-- FAQ --}}
	<div class="col-md-12 mb-3">
		<label class="form-label d-block">{{ __('FAQ') }}</label>
		<div id="faq-rows">
			@foreach($faqRows as $index => $item)
				@include('backend.seo-page._faq_row', ['index' => $index, 'item' => $item])
			@endforeach
		</div>
		<button type="button" class="btn btn-sm btn-outline-gray-600" id="faq-add">{{ __('Add question') }}</button>
		<template id="faq-row-template">
			@include('backend.seo-page._faq_row', ['index' => '__INDEX__', 'item' => ['question' => '', 'answer' => '']])
		</template>
	</div>

	{{-- Image --}}
	<div class="col-md-6 mb-3">
		<label class="form-label" for="featured_image">{{ __('Featured image') }}</label>
		<input class="form-control" type="file" name="featured_image" id="featured_image" accept=".jpg,.jpeg,.png,.webp" @unless($isEdit) required @endunless>
		<small class="text-muted">{{ __('JPG, PNG or WebP, max 2 MB. Saved under a file name built from the slug.') }}</small>
		@php($hasImage = $isEdit && $page->hasFeaturedImageFile())
		<img id="featured-image-preview" class="img-fluid rounded mt-2{{ $hasImage ? '' : ' d-none' }}" style="max-height: 180px"
			src="{{ $hasImage ? asset($page->featured_image) : '' }}" alt="">
	</div>
	<div class="col-md-6 mb-3">
		<label class="form-label" for="featured_image_alt">{{ __('Image alt text') }}</label>
		<input class="form-control" name="featured_image_alt" id="featured_image_alt" maxlength="125" required data-counter="125"
			value="{{ old('featured_image_alt', $page->featured_image_alt) }}">
		<small class="text-muted">{{ __('Describe what the image shows, e.g. "Dashboard showing referring domains in Ahrefs".') }}</small>
	</div>

	{{-- Author --}}
	<div class="col-md-6 mb-3">
		<label class="form-label" for="author_id">{{ __('Author') }}</label>
		<select class="form-select" name="author_id" id="author_id">
			<option value="">{{ __('None') }}</option>
			@foreach($admins as $admin)
				<option value="{{ $admin->id }}" @selected((string) old('author_id', $page->author_id) === (string) $admin->id)>{{ $admin->full_name }}</option>
			@endforeach
		</select>
	</div>

	{{-- SEO --}}
	<div class="col-md-12"><h2 class="h5 mt-3">{{ __('SEO') }}</h2></div>
	<div class="col-md-12 mb-3">
		<label class="form-label" for="meta_title">{{ __('Meta title (title tag)') }}</label>
		<input class="form-control" name="meta_title" id="meta_title" maxlength="255" data-counter="60" value="{{ old('meta_title', $page->meta_title) }}">
	</div>
	<div class="col-md-12 mb-3">
		<label class="form-label" for="meta_description">{{ __('Meta description') }}</label>
		<textarea class="form-control" name="meta_description" id="meta_description" rows="2" maxlength="170" data-counter="160">{{ old('meta_description', $page->meta_description) }}</textarea>
	</div>
	<div class="col-md-6 mb-3">
		<label class="form-label" for="canonical_url">{{ __('Canonical URL') }}</label>
		<input class="form-control" type="url" name="canonical_url" id="canonical_url" value="{{ old('canonical_url', $page->canonical_url) }}" placeholder="{{ __('Leave empty to use this page\'s URL') }}">
	</div>
	<div class="col-md-3 mb-3">
		<label class="form-label" for="primary_keyword">{{ __('Primary keyword') }}</label>
		<input class="form-control" name="primary_keyword" id="primary_keyword" value="{{ old('primary_keyword', $page->primary_keyword) }}">
	</div>
	<div class="col-md-3 mb-3">
		<label class="form-label" for="cluster">{{ __('Cluster') }}</label>
		<select class="form-select" name="cluster" id="cluster">
			<option value="">{{ __('None') }}</option>
			@foreach($clusters as $cluster)
				<option value="{{ $cluster }}" @selected(old('cluster', $page->cluster) === $cluster)>{{ ucfirst($cluster) }}</option>
			@endforeach
		</select>
	</div>

	{{-- Toggles --}}
	<div class="col-md-12 mb-3 d-flex flex-wrap gap-4">
		@foreach(['show_cta' => __('Show call to action'), 'is_active' => __('Active')] as $field => $label)
			<div class="form-check form-switch">
				<input class="form-check-input" type="checkbox" value="1" name="{{ $field }}" id="{{ $field }}" @checked($hasOldInput ? old($field) : $page->{$field})>
				<label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
			</div>
		@endforeach
	</div>
</div>
