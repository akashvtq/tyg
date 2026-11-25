@extends('frontend.layouts.app')

@push('style')
	<link rel="stylesheet" href="{{ asset('frontend/css/seo-page.css') }}">
@endpush

@push('schema')
	@foreach($schemas as $schema)
		<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
	@endforeach
@endpush

@section('content')
	<section class="seo-page">
		<div class="container">
			@include('frontend.seo-page._breadcrumb')

			<div class="row g-5">
				<!-- <div class="col-lg-3 p-md-0">
					@if($showToc)
						@include('frontend.seo-page._toc')
					@endif
				</div> -->
				<div class="col-lg-3 p-md-0">
					@include('frontend.seo-page._sidebar')
				</div>
				<div class="col-lg-9">
					<article>
						<h1 class="seo-page__title">{{ $page->title }}</h1>

						<span class="seo-page__author-date mb-2">
							{{ __('Last updated') }} <time datetime="{{ $page->updated_at->toIso8601String() }}">{{ $page->updated_at->format('M d, Y') }}</time>
						</span>

						@if($hasImage)
							<figure class="seo-page__image">
								<img src="{{ asset($page->featured_image) }}" alt="{{ $page->featured_image_alt }}"
									@if($imageSize) width="{{ $imageSize[0] }}" height="{{ $imageSize[1] }}" @endif
									fetchpriority="high">
							</figure>
						@endif

						<div class="seo-page__content">{!! $toc['html'] !!}</div>

						@if($childGroups !== [])
							@include('frontend.seo-page._children')
						@endif

						@if(! empty($page->faq))
							@include('frontend.seo-page._faq')
						@endif

						<aside class="seo-page__author-bio" aria-label="Author bio">
							<div class="seo-page__author-body">
								<img class="seo-page__author-image" src="{{ asset($page->author->avatar) }}" alt="{{ $page->author->full_name }}" width="96" height="96" loading="lazy">
								<div class="seo-page__author-copy">
									<div class="seo-page__author-meta">
										<h2 class="seo-page__author-name">{{ $page->author->full_name }}</h2>
											<a href="https://www.linkedin.com/in/saurav-agarwal-there-you-grow/" target="_blank" rel="noreferrer" class="text-white mb-1"><i class="fa-brands fa-linkedin-in fa-fade"></i></a>
										
									</div>
									<p>Saurav Agarwal is a SaaS organic growth strategist who spends his days figuring out how to make SaaS brands more discoverable (without begging Google for attention). His playground includes SaaS SEO, semantic SEO, link building, and technical SEO. His latest obsession? SaaS automation. Because why do something manually when you can spend three days automating it?</p>
								</div>
							</div>
						</aside>

						@if($page->show_cta)
							@include('frontend.seo-page._cta')
						@endif
					</article>
				</div>

				
			</div>
		</div>
	</section>
@endsection
