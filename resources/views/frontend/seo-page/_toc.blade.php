<nav class="seo-page__toc" aria-label="{{ __('Table of contents') }}">
	<p class="seo-page__toc-title">{{ __('On this page') }}</p>
	<ol>
		@foreach($toc['items'] as $item)
			<li>
				<a href="#{{ $item['id'] }}">{{ $item['text'] }}</a>
				@if($item['children'])
					<ol>
						@foreach($item['children'] as $child)
							<li><a href="#{{ $child['id'] }}">{{ $child['text'] }}</a></li>
						@endforeach
					</ol>
				@endif
			</li>
		@endforeach
	</ol>
</nav>
