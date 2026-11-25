{{-- Shared TYG sections. Include with ['section' => 'marquee' | 'results' | 'testimonials' | 'faq' | 'explore'] --}}
@php($logoDir = 'frontend/tyg/logos/')

@if($section === 'marquee')
    @php($logos = ['aureate', 'akash-agro', 'hostduplex', 'sciencesoft', 'florian', 'abis', 'greenlab', 'schwarzkopf', 'godrej', 'nagindas', 'henkel'])
    <div class="tyg-marquee" data-marquee>
        <div class="tyg-marquee__track">
            @foreach([0, 1] as $copy)
                <div class="tyg-marquee__group" @if($copy) aria-hidden="true" @endif>
                    @foreach($logos as $logo)
                        <img src="{{ asset($logoDir.$logo.'.png') }}" alt="{{ $copy ? '' : ucwords(str_replace('-', ' ', $logo)) }}" loading="lazy">
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endif

@if($section === 'results')
    @php($cards = [
        ['akash-agro', 'Content / Design / Ebook', -8],
        ['sciencesoft', 'Backlink / Content', -3],
        ['hostduplex', 'Content Plan / LinkedIn', 0],
        ['henkel', 'Content / Ebook', 3],
        ['aureate', 'Concept & SEO', 7],
    ])
    <section class="tyg-results">
        <div class="tyg-container tyg-center">
            <h2 class="tyg-display" data-split>Real growth.<br>Real <em>Result</em></h2>
            <p class="tyg-lead" data-reveal>A glimpse of the brands we’ve helped grow<br>with content, SEO, and digital marketing.</p>
        </div>
        <div class="tyg-fan" data-fan data-fan-rot="4" data-fan-y="26">
            @foreach($cards as $i => [$logo, $label, $rot])
                <a href="{{ url('/case-studies') }}" class="tyg-fan__card tyg-results__card {{ $i === 2 ? 'is-red' : '' }}" data-fan-card>
                    <img src="{{ asset($logoDir.$logo.'.png') }}" alt="{{ ucwords(str_replace('-', ' ', $logo)) }}" loading="lazy">
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </section>
@endif

@if($section === 'testimonials')
    @php($reviews = [
        ['Working with Kajol Motiramani has been inspiring! Her leadership blends approachability and strategic thinking, making every team member feel valued.', 'Batul Painter', 'Content Writer · Brand Communications', 'r38'],
        ['Hardworking, intelligent, reliable — a true value add to the team. Every brief came back sharper than we sent it.', 'Tasneem Electricalwala', 'Media & Digital Marketing', 'r39'],
        ['Our organic traffic doubled within two quarters. The keyword research alone paid for the whole engagement.', 'Rhea Kapoor', 'Founder · D2C Skincare', 'r40'],
        ['The technical audit was the clearest SEO document we have ever received — prioritised, practical and actually implemented.', 'Arjun Mehta', 'Head of Growth · SaaS', 'r41'],
        ['TYG turned our blog from a cost centre into our best lead source. Calm, structured and genuinely invested.', 'Karan Shah', 'Marketing Lead · Fintech', 'r42'],
        ['From topical map to published content, the process was transparent and the results showed up in Search Console.', 'Emma Clarke', 'Content Director · Agency', 'r43'],
    ])
    <section class="tyg-reviews tyg-dark">
        <div class="tyg-container tyg-reviews__grid">
            <div class="tyg-reviews__intro">
                <h2 class="tyg-display tyg-display--md" data-split>Our customer<br>love what <em>we do</em></h2>
                <p data-reveal>Our customers love our work! Read their reviews to discover why they’re raving about our quality, services, and overall experience. Join satisfied ranks today!</p>
                <a href="{{ url('/case-studies') }}" class="tyg-btn tyg-btn--red tyg-btn--lg" data-reveal data-magnetic>Get to know us</a>
            </div>
            <div class="tyg-reviews__slider" data-slider="reviews">
                <div class="tyg-reviews__nav">
                    <button type="button" class="tyg-arrow tyg-arrow--light" data-prev aria-label="Previous">
                        <img src="{{ asset('frontend/tyg/arrow-left.png') }}" alt="">
                    </button>
                    <button type="button" class="tyg-arrow tyg-arrow--light" data-next aria-label="Next">
                        <img src="{{ asset('frontend/tyg/arrow-right.png') }}" alt="">
                    </button>
                </div>
                <div class="tyg-reviews__viewport">
                    <div class="tyg-reviews__track" data-track>
                        @foreach($reviews as [$quote, $name, $role, $avatar])
                            <figure class="tyg-review" data-slide>
                                <span class="tyg-review__q">“</span>
                                <blockquote>{{ $quote }}</blockquote>
                                <figcaption>
                                    <img src="{{ asset('frontend/tyg/avatars/'.$avatar.'.png') }}" alt="" loading="lazy">
                                    <span><b>{{ $name }}</b><small>{{ $role }}</small></span>
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>
                </div>
                <div class="tyg-dots" data-dots></div>
            </div>
        </div>
    </section>
@endif

@if($section === 'explore')
    {{-- Other services, shown on a service page. Pass 'except' => 'seo' | 'branding' | 'development' --}}
    {{-- Figma node 687-2318: grey band with a torn-paper top edge, heading left, two tilted cards right --}}
    @php($others = collect([
        'seo' => ['SEO', 'Search strategy, technical fixes and content that win high-intent organic traffic.', route('tyg.seo-services')],
        'branding' => ['Branding', 'Category positioning, visual identity systems, and distinctive creative brand foundations.', route('tyg.branding-services')],
        'development' => ['Development', 'Creative digital solutions engineered to build high-performing websites and digital experiences.', route('tyg.development-services')],
    ])->except($except ?? 'seo'))
    <section class="tyg-explore">
        <img src="{{ asset('frontend/tyg/torn.png') }}" alt="" class="tyg-torn tyg-torn--l" aria-hidden="true"><img src="{{ asset('frontend/tyg/torn.png') }}" alt="" class="tyg-torn tyg-torn--r" aria-hidden="true">
        <div class="tyg-container tyg-explore__grid">
            <div class="tyg-explore__intro">
                <h2 class="tyg-explore__title" data-split>Explore<br>other<br><em>services</em></h2>
                <p data-reveal>A clear and proven approach to deliver measurable results.</p>
            </div>
            @foreach($others as [$title, $intro, $link])
                <article class="tyg-explore__card" data-reveal>
                    <h3>{{ $title }}<br>Services</h3>
                    <p>{{ $intro }}</p>
                    <a href="{{ $link }}" class="tyg-btn tyg-btn--red tyg-explore__btn">Explore {{ $title }}</a>
                </article>
            @endforeach
        </div>
    </section>
@endif

@if($section === 'faq')
    <section class="tyg-faq">
        <div class="tyg-container tyg-faq__grid">
            <div class="tyg-faq__img" data-reveal><img src="{{ asset('frontend/tyg/faq-new.png') }}" alt="Person working at a computer" loading="lazy"></div>
            <div>
                <h2 class="tyg-display tyg-display--md" data-split>Frequently<br>asked <em>questions</em></h2>
                <div class="tyg-acc" data-accordion>
                    @foreach([
                        ['How quickly will we see measurable SEO results?', 'Technical fixes and on-page improvements often show movement within 4–8 weeks. Content and authority work compounds over 3–6 months, and we report progress every step of the way.'],
                        ['Do you follow white-hat, algorithm-safe practices?', 'Always. Everything we do follows Google’s Search Essentials — no private blog networks, no spun content, no shortcuts that put your domain at risk.'],
                        ['What is programmatic SEO and is it right for my website?', 'Programmatic SEO uses templates and structured data to publish many high-quality pages targeting repeatable search patterns. It suits sites with rich data — marketplaces, directories, SaaS integrations. Our strategy service tells you if it fits.'],
                        ['Can I order individual services without a long-term contract?', 'Yes. Every service is productized with a fixed scope, price and timeline. Add what you need to your cart and check out — no retainer required.'],
                        ['How does TYG’s keyword research differ from free tools?', 'Free tools give you lists. We give you grouped topics, verified intent, realistic difficulty for your domain and a priority order — so you know exactly what to write first.'],
                        ['Will you implement the technical fixes or just send a checklist?', 'Audits include a prioritised, developer-ready report. If you want us to implement the fixes, our development team can take it on as a follow-up project.'],
                        ['What format are the final deliverables provided in?', 'Spreadsheets in Google Sheets, reports in Google Docs or PDF, and content in Google Docs ready for your CMS. Everything is yours to keep.'],
                        ['How do we get started?', 'Add a service to your cart and check out, or click “Let’s Talk” to book a call. We kick off within 24 hours of your order.'],
                    ] as [$q, $a])
                        <div class="tyg-acc__item">
                            <button type="button" class="tyg-acc__q"><span>{{ $q }}</span><i></i></button>
                            <div class="tyg-acc__a"><p>{{ $a }}</p></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
