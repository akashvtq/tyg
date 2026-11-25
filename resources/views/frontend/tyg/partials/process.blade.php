{{-- Process tabs + photo panel. Expects: $heading (HTML), $intro, $steps ([title, description, label][]), $imageAlt --}}
<section class="tyg-seoprocess">
    <div class="tyg-container tyg-seoprocess__grid">
        <div>
            <h2 class="tyg-display tyg-display--md" data-split>{!! $heading !!}</h2>
            <p class="tyg-seoprocess__intro" data-reveal>{{ $intro }}</p>
            <div class="tyg-tabs" data-tabs>
                @foreach($steps as $i => [$t, $d])
                    <button type="button" class="tyg-tab {{ $i === 0 ? 'is-active' : '' }}" data-tab="{{ $i }}">
                        <strong>{{ $t }}</strong>
                        <span>{{ $d }}</span>
                    </button>
                @endforeach
            </div>
        </div>
        <div class="tyg-seoprocess__panel" data-tab-panel>
            <img src="{{ asset('frontend/tyg/seo-process.png') }}" alt="{{ $imageAlt }}" class="tyg-seoprocess__img" loading="lazy">
            @foreach($steps as $i => [$t, $d, $label])
                <div class="tyg-panel {{ $i === 0 ? 'is-active' : '' }}" data-panel="{{ $i }}">
                    <span class="tyg-panel__no">0{{ $i + 1 }}</span>
                    <span class="tyg-panel__label">{{ $label }}</span>
                    <h3>{{ $t }}</h3>
                </div>
            @endforeach
        </div>
    </div>
</section>
