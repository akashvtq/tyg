{{-- Package grid (dark band). Expects: $services (collection of App\Models\Services), $heading (HTML) --}}
@php($symbol = setting('currency_symbol') ?: '$')
@php($currency = setting('site_currency') ?: 'USD')
<section class="tyg-packages tyg-dark" id="packages">
    <div class="tyg-container">
        <h2 class="tyg-display tyg-display--md tyg-center" data-split>{!! $heading !!}</h2>

        <div class="tyg-pkg-grid" data-packages>
            @forelse($services as $i => $service)
                @php($tags = is_string($service->tags) ? json_decode($service->tags, true) : $service->tags)
                @php($duration = collect($tags ?? [])->pluck('value')->first())
                @php($name = $service->translate('name'))
                <article class="tyg-pkg {{ $loop->last && $loop->count % 3 === 1 ? 'tyg-pkg--wide' : '' }}" data-pkg>
                    <div class="tyg-pkg__head">
                        <span class="tyg-pkg__no">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}.</span>
                        <span class="tyg-pkg__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M3 6h8M3 11h6M3 16h5"/><circle cx="15.5" cy="12.5" r="4"/><path d="M18.5 15.5L21 18"/></svg>
                        </span>
                    </div>
                    <div class="tyg-pkg__main">
                        <h3>{{ $name }}</h3>
                        <p class="tyg-pkg__meta">{{ $symbol }}{{ number_format($service->price) }} {{ $currency }}@if($duration) · {{ $duration }}@endif</p>
                    </div>
                    <div class="tyg-pkg__body">
                        <div class="tyg-pkg__list">{!! $service->translate('description') !!}</div>
                        <button type="button" class="tyg-btn tyg-btn--red tyg-btn--block tyg-pkg__add"
                                data-add-to-cart
                                data-slug="{{ $service->slug }}"
                                data-name="{{ $name }}"
                                data-price="{{ $service->price }}">
                            <span>Add to Cart</span>
                            <svg viewBox="0 0 16 16" width="12" height="12"><path d="M2 8h11M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
                        </button>
                    </div>
                </article>
            @empty
                <p class="tyg-center">Services will be available soon.</p>
            @endforelse
        </div>
    </div>
</section>
