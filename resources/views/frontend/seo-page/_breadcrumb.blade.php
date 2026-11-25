<nav class="seo-page__breadcrumb" aria-label="{{ __('Breadcrumb') }}">
	<ol>
		<li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
		@foreach($trail as $crumb)
			@if($loop->last)
				<li aria-current="page">{{ $crumb->nav_label }}</li>
			@else
				<li><a href="{{ $crumb->url() }}">{{ $crumb->nav_label }}</a></li>
			@endif
		@endforeach
	</ol>
</nav>
