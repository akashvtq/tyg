<section class="seo-page__children">
	<h2 class="seo-page__section-title">{{ __('In this section') }}</h2>
	@foreach($childGroups as $group)
		@if($group['group'])
			<h3 class="seo-page__group-title">{{ $group['group'] }}</h3>
		@endif
		<div class="row g-4 mb-4">
			@foreach($group['pages'] as $child)
				<div class="col-md-6">
					<a class="seo-page__card" href="{{ $child->url() }}">
						@if($child->hasFeaturedImageFile())
							<img src="{{ asset($child->featured_image) }}" alt="{{ $child->featured_image_alt }}" loading="lazy">
						@endif
						<span class="seo-page__card-title">{{ $child->nav_label }}</span>
						@if($child->excerpt)
							<span class="seo-page__card-text">{{ $child->excerpt }}</span>
						@endif
					</a>
				</div>
			@endforeach
		</div>
	@endforeach
</section>
