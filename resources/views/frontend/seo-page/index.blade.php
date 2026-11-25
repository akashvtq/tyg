@extends('frontend.layouts.app')

@push('style')
<style>
/* SEO Guide Hub Styles */
.guide-hero {
	padding: 80px 0 60px;
	background: radial-gradient(circle at 80% 20%, rgba(132, 99, 221, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
	position: relative;
}
.guide-badge {
	display: inline-block;
	font-size: 12px;
	font-weight: 700;
	letter-spacing: 0.08em;
	text-transform: uppercase;
	padding: 6px 14px;
	border-radius: 50px;
	background: rgba(132, 99, 221, 0.1);
	color: #8e271f;
	margin-bottom: 16px;
}
.guide-hero__title {
	font-size: clamp(32px, 4.5vw, 52px);
	font-weight: 800;
	line-height: 1.15;
	margin-bottom: 20px;
	letter-spacing: -0.02em;
}
.guide-hero__desc {
	font-size: 17px;
	line-height: 1.65;
	max-width: 580px;
}
.guide-hero__graphic {
	position: relative;
	background: #ffffff;
	border-radius: 20px;
	box-shadow: 0 20px 45px -10px rgba(132, 99, 221, 0.12), 0 0 0 1px #E6E3F0;
	padding: 30px;
	min-height: 220px;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
}
.guide-graphic-svg {
	width: 100%;
	max-width: 380px;
	height: auto;
}

/* Browse by SEO Topic Overview Cards */
.guide-categories-overview {
	padding: 60px 0 40px;
	background: #F6F5FA;
}
.guide-section-head {
	margin-bottom: 36px;
}
.guide-section-head h2 {
	font-size: 28px;
	font-weight: 700;
	color: #111111;
	margin-bottom: 6px;
	letter-spacing: -0.01em;
}
.guide-section-head p {
	font-size: 15px;
	color: #555555;
	margin: 0;
}
.guide-topic-card {
	display: flex;
	flex-direction: column;
	height: 100%;
	background: #ffffff;
	border-radius: 16px;
	padding: 24px;
	border: 1px solid #E6E3F0;
	text-decoration: none;
	color: inherit;
	transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	position: relative;
}
.guide-topic-card:hover {
	transform: translateY(-4px);
	box-shadow: 0 16px 32px -8px rgba(17, 17, 17, 0.08);
	border-color: #CFCAE2;
	color: inherit;
}
.guide-topic-card__icon {
	width: 48px;
	height: 48px;
	border-radius: 12px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 20px;
	margin-bottom: 16px;
	background: #F1EDFB;
	color: #b9342b;
}
.guide-topic-card:nth-child(6n+1) .guide-topic-card__icon { background: #e0f2fe; color: #0284c7; }
.guide-topic-card:nth-child(6n+2) .guide-topic-card__icon { background: #dcfce7; color: #16a34a; }
.guide-topic-card:nth-child(6n+3) .guide-topic-card__icon { background: #f3e8ff; color: #9333ea; }
.guide-topic-card:nth-child(6n+4) .guide-topic-card__icon { background: #ffedd5; color: #ea580c; }
.guide-topic-card:nth-child(6n+5) .guide-topic-card__icon { background: #e0e7ff; color: #8e271f; }
.guide-topic-card:nth-child(6n+6) .guide-topic-card__icon { background: #fef3c7; color: #d97706; }

.guide-topic-card__title {
	font-size: 18px;
	font-weight: 700;
	color: #111111;
	margin-bottom: 8px;
}
.guide-topic-card__desc {
	font-size: 14px;
	color: #555555;
	line-height: 1.55;
	flex-grow: 1;
	margin-bottom: 16px;
}
.guide-topic-card__arrow {
	width: 32px;
	height: 32px;
	border-radius: 50%;
	background: #F6F5FA;
	display: flex;
	align-items: center;
	justify-content: center;
	color: #444444;
	font-size: 13px;
	transition: all 0.2s ease;
}
.guide-topic-card:hover .guide-topic-card__arrow {
	background: #b9342b;
	color: #ffffff;
}

/* Category Sections & Articles */
.guide-category-section {
	padding: 60px 0;
	border-bottom: 1px solid #F6F5FA;
}
.guide-category-section:last-of-type {
	border-bottom: none;
}
.guide-category-header {
	display: flex;
	align-items: flex-start;
	justify-content: space-between;
	gap: 20px;
	margin-bottom: 24px;
	flex-wrap: wrap;
}
.guide-category-header__left {
	display: flex;
	align-items: center;
	gap: 16px;
}
.guide-category-header__icon {
	width: 52px;
	height: 52px;
	border-radius: 14px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 24px;
	background: #F1EDFB;
	color: #b9342b;
	flex-shrink: 0;
}
.guide-category-header h2 {
	font-size: 24px;
	font-weight: 700;
	margin: 0 0 4px;
}
.guide-category-header p {
	font-size: 15px;
	color: #555555;
	margin: 0;
}
.guide-viewall-link {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	font-size: 14px;
	font-weight: 700;
	color: #b9342b;
	text-decoration: none;
	transition: gap 0.2s ease;
}
.guide-viewall-link:hover {
	gap: 12px;
	color: #8e271f;
}

/* Sub-category Pill Filter Buttons */
.guide-subnav-pills {
	display: flex;
	gap: 8px;
	margin-bottom: 28px;
	overflow-x: auto;
	scrollbar-width: none;
	padding-bottom: 4px;
}
.guide-subnav-pills::-webkit-scrollbar {
	display: none;
}
.guide-pill-btn {
	padding: 6px 16px;
	border-radius: 50px;
	font-size: 13px;
	font-weight: 600;
	color: #444444;
	background: #F6F5FA;
	border: 1px solid #E6E3F0;
	cursor: pointer;
	white-space: nowrap;
	transition: all 0.2s ease;
}
.guide-pill-btn:hover {
	background: #F6F5FA;
	color: #111111;
}
.guide-pill-btn.active {
	background: #b9342b;
	color: #ffffff;
	border-color: #b9342b;
}

/* Blog item styles for consistency */
.blog__item {
	background: #ffffff;
	border-radius: 12px;
	border: 1px solid #E6E3F0;
	overflow: hidden;
	height: 100%;
	display: flex;
	flex-direction: column;
	transition: all 0.3s ease;
}
.blog__item:hover {
	transform: translateY(-4px);
	box-shadow: 0 16px 32px -8px rgba(17, 17, 17, 0.08);
	border-color: #CFCAE2;
}
.blog__item .blog__content {
	flex-grow: 1;
	display: flex;
	flex-direction: column;
}
.blog__item .blog__content p {
	flex-grow: 1;
}

/* Graphic Mockup inside Hero */
.guide-mockup-tag {
	font-size: 11px;
	font-weight: 700;
	padding: 4px 10px;
	border-radius: 20px;
	display: inline-flex;
	align-items: center;
	gap: 5px;
	box-shadow: 0 4px 10px rgba(0,0,0,0.05);
	background: #ffffff;
	border: 1px solid #E6E3F0;
	position: absolute;
}
.tag-strategy { top: 15px; left: 35%; color: #0284c7; }
.tag-links { top: 35px; right: 12%; color: #b9342b; }
.tag-types { top: 90px; right: 6%; color: #9333ea; }
.tag-analysis { bottom: 70px; right: 15%; color: #ea580c; }
.tag-tools { bottom: 20px; right: 35%; color: #16a34a; }
.tag-search { top: 35px; left: 10%; color: #555555; }
.read-more-btn{ color: black !important; }
.read-more-btn:hover { color: var(--primary-color) !important; }

@media (max-width: 991px) {
	.guide-hero { padding: 40px 0; }
	.guide-hero__graphic { margin-top: 30px; min-height: 180px; }
}
</style>
@endpush

@section('content')
	<!-- 1. Hero Section -->
	<section class="guide-hero">
		<div class="container">
			<div class="row align-items-center g-5">
				<div class="col-lg-7">
					<span class="guide-badge">{{ __('SEO GUIDE') }}</span>
					<h1 class="guide-hero__title">{{ __('SEO Guides and Resources') }}</h1>
					<p class="guide-hero__desc">
						{{ __('Explore clear, practical guides to help you understand, plan and execute SEO for your business. Browse by topic, from strategy and link building to types of SEO, analysis and tools.') }}
					</p>
				</div>
				<div class="col-lg-5">
					<img src="{{ asset('frontend/SEO-growth.png') }}" alt="SEO-growth">
				</div>
			</div>
		</div>
	</section>

	<!-- 2. Browse by SEO Topic Cards Section -->
	@if($subcategories->isNotEmpty())
	<section class="guide-categories-overview" id="all-topics">
		<div class="container">
			<div class="guide-section-head">
				<h2>{{ __('Browse by SEO topic') }}</h2>
				<p>{{ __('Choose a category to explore guides, subtopics and in-depth articles.') }}</p>
			</div>

			<div class="row g-4">
				@foreach($sections as $sec)
					<div class="col-xl-4 col-md-6">
						<a href="#sec-{{ $sec['category']->slug }}" class="guide-topic-card">
							<div class="guide-topic-card__icon">
								@php
									$icon = match(true) {
										str_contains(strtolower($sec['category']->nav_label), 'link') => 'fa-link',
										str_contains(strtolower($sec['category']->nav_label), 'strategy') => 'fa-chart-line',
										str_contains(strtolower($sec['category']->nav_label), 'type') => 'fa-layer-group',
										str_contains(strtolower($sec['category']->nav_label), 'analys') => 'fa-chart-simple',
										str_contains(strtolower($sec['category']->nav_label), 'tool') => 'fa-wrench',
										default => 'fa-book-open',
									};
								@endphp
								<i class="fa-solid {{ $icon }}"></i>
							</div>
							<h3 class="guide-topic-card__title">{{ $sec['category']->nav_label ?: $sec['category']->title }}</h3>
							<p class="guide-topic-card__desc">
								{{ $sec['category']->excerpt ?: __('Learn fundamentals, strategies, execution methods and key metrics.') }}
							</p>
							<div class="guide-topic-card__arrow">
								<i class="fa-regular fa-arrow-right"></i>
							</div>
						</a>
					</div>
				@endforeach
			</div>
		</div>
	</section>
	@endif

	<!-- 3. Category-Wise Articles Listing Sections (Exact _seo_pages blog-card design, No Pagination) -->
	@foreach($sections as $sec)
		@php
			$cat = $sec['category'];
			$topics = $sec['topics'];
			$groups = $sec['groups'];
		@endphp
		<section class="guide-category-section" id="sec-{{ $cat->slug }}">
			<div class="container">
				<!-- Category Header -->
				<div class="guide-category-header">
					<div class="guide-category-header__left">
						<div class="guide-category-header__icon">
							@php
								$icon = match(true) {
									str_contains(strtolower($cat->nav_label), 'link') => 'fa-link',
									str_contains(strtolower($cat->nav_label), 'strategy') => 'fa-chart-line',
									str_contains(strtolower($cat->nav_label), 'type') => 'fa-layer-group',
									str_contains(strtolower($cat->nav_label), 'analys') => 'fa-chart-simple',
									str_contains(strtolower($cat->nav_label), 'tool') => 'fa-wrench',
									default => 'fa-book-open',
								};
							@endphp
							<i class="fa-solid {{ $icon }}"></i>
						</div>
						<div>
							<h2>{{ $cat->nav_label ?: $cat->title }}</h2>
							<p>{{ $cat->excerpt ?: __('Learn fundamentals, strategies, execution methods and how to measure impact.') }}</p>
						</div>
					</div>
					<a href="{{ $cat->url() }}" class="guide-viewall-link">
						{{ __('View all') }} {{ strtolower($cat->nav_label ?: $cat->title) }} {{ __('articles') }}
						<i class="fa-regular fa-arrow-right-long"></i>
					</a>
				</div>

				<!-- Nav Group Filter Pills (if category has grouped topics) -->
				@if(!empty($groups))
					<div class="guide-subnav-pills" data-cat="{{ $cat->slug }}">
						<button type="button" class="guide-pill-btn active" data-filter="all" onclick="filterGuideGroup('{{ $cat->slug }}', 'all', this)">
							{{ __('All') }}
						</button>
						@foreach($groups as $grp)
							<button type="button" class="guide-pill-btn" data-filter="{{ \Illuminate\Support\Str::slug($grp) }}" onclick="filterGuideGroup('{{ $cat->slug }}', '{{ \Illuminate\Support\Str::slug($grp) }}', this)">
								{{ $grp }}
							</button>
						@endforeach
					</div>
				@endif

				<!-- SEO Articles Grid (Same card design as _seo_pages.blade.php) -->
				@if($topics->isNotEmpty())
					<div class="row g-4" id="grid-{{ $cat->slug }}">
						@foreach($topics as $seoPage)
							@php
								$topicGroupSlug = $seoPage->nav_group ? \Illuminate\Support\Str::slug($seoPage->nav_group) : 'ungrouped';
							@endphp
							<div class="col-xl-4 col-lg-6 col-md-6 guide-item-col wow fadeInUp"
							     data-group="{{ $topicGroupSlug }}"
							     data-wow-delay="{{ ($loop->iteration % 3 + 1) }}00ms"
							     data-wow-duration="1500ms">
								<div class="blog__item">
									<a href="{{ $seoPage->url() }}" class="blog__image d-block image">
										@if ($seoPage->hasFeaturedImageFile())
											<img class="component-blog-cover" src="{{ asset($seoPage->featured_image) }}"
											     alt="{{ $seoPage->featured_image_alt }}" loading="lazy">
										@else
											<img class="component-blog-cover" src="{{ asset(setting('meta_image')) }}"
											     alt="{{ $seoPage->title }}" loading="lazy">
										@endif
										<div class="blog-tag">
											<h3 class="text-white">{{ $seoPage->created_at->format('d') }}</h3>
											<span class="text-white">{{ $seoPage->created_at->format('M') }}</span>
										</div>
									</a>
									<div class="blog__content">
										<ul class="blog-info pb-10 bor-bottom mb-10">
											@if($seoPage->author)
												<li>
													<x-svg i="user"/>
													<span class="text-black">{{ __('By') }} {{ $seoPage->author->first_name . ' ' . $seoPage->author->last_name }}</span>
												</li>
											@endif
										</ul>
										<h3><a href="{{ $seoPage->url() }}" class="primary-hover">{{ $seoPage->title }}</a></h3>
										<a class="mt-15 read-more-btn" href="{{ $seoPage->url() }}">
											{{ __('Read More') }} <i class="fa-regular fa-arrow-right-long"></i>
										</a>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@else
					<!-- If this category has no subtopics, show card for this category page itself -->
					<div class="row g-4" id="grid-{{ $cat->slug }}">
						<div class="col-xl-4 col-lg-6 col-md-6 guide-item-col wow fadeInUp"
						     data-wow-delay="100ms"
						     data-wow-duration="1500ms">
							<div class="blog__item">
								<a href="{{ $cat->url() }}" class="blog__image d-block image">
									@if ($cat->hasFeaturedImageFile())
										<img class="component-blog-cover" src="{{ asset($cat->featured_image) }}"
										     alt="{{ $cat->featured_image_alt }}" loading="lazy">
									@else
										<img class="component-blog-cover" src="{{ asset(setting('meta_image')) }}"
										     alt="{{ $cat->title }}" loading="lazy">
									@endif
									<div class="blog-tag">
										<h3 class="text-white">{{ $cat->created_at->format('d') }}</h3>
										<span class="text-white">{{ $cat->created_at->format('M') }}</span>
									</div>
								</a>
								<div class="blog__content">
									<ul class="blog-info pb-10 bor-bottom mb-10">
										@if($cat->author)
											<li>
												<x-svg i="user"/>
												<span class="text-black">{{ __('By') }} {{ $cat->author->first_name . ' ' . $cat->author->last_name }}</span>
											</li>
										@endif
									</ul>
									<h3><a href="{{ $cat->url() }}" class="primary-hover">{{ $cat->title }}</a></h3>
									<a class="mt-15 read-more-btn" href="{{ $cat->url() }}">
										{{ __('Read More') }} <i class="fa-regular fa-arrow-right-long"></i>
									</a>
								</div>
							</div>
						</div>
					</div>
				@endif
			</div>
		</section>
	@endforeach
@endsection

@push('script')
<script>
function filterGuideGroup(catSlug, filterVal, btnElement) {
	const pills = document.querySelectorAll(`.guide-subnav-pills[data-cat="${catSlug}"] .guide-pill-btn`);
	pills.forEach(p => p.classList.remove('active'));
	btnElement.classList.add('active');

	const grid = document.getElementById(`grid-${catSlug}`);
	if (!grid) return;

	const items = grid.querySelectorAll('.guide-item-col');
	items.forEach(item => {
		if (filterVal === 'all' || item.dataset.group === filterVal) {
			item.style.display = '';
		} else {
			item.style.display = 'none';
		}
	});
}
</script>
@endpush
