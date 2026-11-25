<div class="faq-row border rounded p-3 mb-2">
	<div class="d-flex justify-content-between mb-2">
		<strong>{{ __('Question') }}</strong>
		<div class="btn-group btn-group-sm">
			<button type="button" class="btn btn-outline-gray-600 faq-up" title="{{ __('Move up') }}" aria-label="{{ __('Move up') }}">↑</button>
			<button type="button" class="btn btn-outline-gray-600 faq-down" title="{{ __('Move down') }}" aria-label="{{ __('Move down') }}">↓</button>
			<button type="button" class="btn btn-outline-danger faq-remove" title="{{ __('Remove') }}" aria-label="{{ __('Remove') }}">×</button>
		</div>
	</div>
	<input class="form-control mb-2" name="faq[{{ $index }}][question]" maxlength="500" aria-label="{{ __('FAQ question') }}" value="{{ $item['question'] ?? '' }}" placeholder="{{ __('Question') }}">
	<textarea class="form-control" name="faq[{{ $index }}][answer]" rows="3" maxlength="5000" aria-label="{{ __('FAQ answer') }}" placeholder="{{ __('Answer (simple HTML allowed)') }}">{{ $item['answer'] ?? '' }}</textarea>
</div>
