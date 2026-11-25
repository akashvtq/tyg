<aside class="seo-page__sidebar">
	<nav aria-label="{{ __('Categories') }}">
		@foreach($categoryGroups as $categoryGroup)
			@if($categoryGroup['group'])
				<p class="seo-page__nav-group">{{ $categoryGroup['group'] }}</p>
			@endif
			@foreach($categoryGroup['pages'] as $category)
				<p class="seo-page__sidebar-title">
					<a href="{{ $category->url() }}" @if($category->is($root)) aria-current="page" @endif>{{ $category->nav_label }}</a>
				</p>
				@foreach($pageGroups[$category->id] ?? [] as $subcategoryGroup)
					@if($subcategoryGroup['group'])
						<p class="seo-page__nav-group seo-page__nav-group--nested">{{ $subcategoryGroup['group'] }}</p>
					@endif
					<ul class="seo-page__nav seo-page__nav--nested">
						@foreach($subcategoryGroup['pages'] as $subcategory)
							<li @class(['is-current' => $subcategory->is($page)])>
								<a href="{{ $subcategory->url() }}" @if($subcategory->is($page)) aria-current="page" @endif>{{ $subcategory->nav_label }}</a>
								@foreach($pageGroups[$subcategory->id] ?? [] as $topicGroup)
									@if($topicGroup['group'])
										<p class="seo-page__nav-group seo-page__nav-group--nested">{{ $topicGroup['group'] }}</p>
									@endif
									<ul class="seo-page__nav seo-page__nav--nested">
										@foreach($topicGroup['pages'] as $topic)
											<li @class(['is-current' => $topic->is($page)])>
												<a href="{{ $topic->url() }}" @if($topic->is($page)) aria-current="page" @endif>{{ $topic->nav_label }}</a>
											</li>
										@endforeach
									</ul>
								@endforeach
							</li>
						@endforeach
					</ul>
				@endforeach
			@endforeach
		@endforeach
	</nav>
</aside>
