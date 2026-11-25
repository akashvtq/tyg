@if ($seoPages->isNotEmpty())
	<section class="blog-area pt-120 pb-120">
		<div class="container">
			<div class="section-header text-center mb-60">
				<h5 class="wow fadeInUp" data-wow-delay="00ms" data-wow-duration="1500ms">
					{{ __('SEO Resources') }}
				</h5>
				<h2 class="wow fadeInUp" data-wow-delay="200ms" data-wow-duration="1500ms">
					{{ __('Explore Our SEO Pages') }}
				</h2>
			</div>
			<div class="row g-4">
				@foreach ($seoPages as $seoPage)
					<div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="{{ $loop->iteration }}00ms"
					     data-wow-duration="1500ms">
						<div class="blog__item" style="background-color: white;border: 1px solid #fff;border-radius: 12px;overflow: hidden;">
							<a href="{{ $seoPage->url() }}" class="blog__image d-block image">
								@if ($seoPage->hasFeaturedImageFile())
									<img class="component-blog-cover" src="{{ asset($seoPage->featured_image) }}"
									     alt="{{ $seoPage->featured_image_alt }}" loading="lazy">
								@endif
								<div class="blog-tag">
									<h3 class="text-white">{{ $seoPage->created_at->format('d') }}</h3>
									<span class="text-white">{{ $seoPage->created_at->format('M') }}</span>
								</div>
							</a>
							<div class="blog__content" >
								<ul class="blog-info pb-10 bor-bottom mb-10">
									<li>
										<x-svg i="user"/>
										<span class="text-black">{{ __('By') }} {{ $seoPage->author->first_name . ' ' . $seoPage->author->last_name }}</span>
									</li>
								</ul>
								<h3><a href="{{ $seoPage->url() }}" class="primary-hover">{{ $seoPage->title }}</a></h3>
								<a class="mt-15 read-more-btn" style="color: black;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='black'" href="{{ $seoPage->url() }}">
									{{ __('Read More') }} <i class="fa-regular fa-arrow-right-long"></i>
								</a>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</section>
@endif
