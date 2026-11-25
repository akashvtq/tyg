<section class="seo-page__faq">
	<h2 class="seo-page__section-title">{{ __('Frequently asked questions') }}</h2>
	@foreach($page->faq as $item)
		<details class="seo-page__faq-item">
			<summary>{{ $item['question'] }}</summary>
			<div class="seo-page__faq-answer">{!! $item['answer'] !!}</div>
		</details>
	@endforeach
</section>
