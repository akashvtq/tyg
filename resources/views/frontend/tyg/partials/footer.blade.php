{{-- Footer — Figma node 687-3117 (1440 frame): gold hairline, logo + tagline / direct desk, 4 columns, legal bar --}}
@php($email = setting('support_email'))
@php($phone = setting('support_phone'))
<footer class="tyg-footer">
    <div class="tyg-footer__inner">
        <div class="tyg-footer__top">
            <div class="tyg-footer__brand">
                <a href="{{ route('home') }}" class="tyg-footer__logo" aria-label="There You Grow home">TYG</a>
                <p class="tyg-footer__tag">The creative consulting atelier and productized design engine for ambitious market leaders.</p>
            </div>
            @if($email)
                <div class="tyg-footer__desk">
                    <span>Direct desk</span>
                    <a href="mailto:{{ $email }}">{{ $email }}</a>
                </div>
            @endif
        </div>

        <div class="tyg-footer__cols">
            <div>
                <h6>Studio Locations</h6>
                <ul>
                    <li><b>Mumbai:</b> Strategy, SEO &amp; content studio</li>
                    <li><b>London:</b> Brand &amp; design partnerships</li>
                    <li><b>Berlin:</b> Product &amp; web engineering</li>
                </ul>
            </div>
            <div>
                <h6>Direct Channel</h6>
                <ul>
                    @if($phone)<li>Tel: <a href="tel:{{ preg_replace('/[^+\d]/', '', $phone) }}">{{ $phone }}</a></li>@endif
                    @if($email)<li>General: <a href="mailto:{{ $email }}">{{ $email }}</a></li>@endif
                    <li>Partner Inquiries: <a href="{{ url('/contact') }}">Contact us</a></li>
                    <li>Hours: Mon - Fri, 09:00 - 19:00 IST</li>
                </ul>
            </div>
            <div>
                <h6>Core Practices</h6>
                <ul>
                    <li><a href="{{ route('tyg.seo-services') }}">SEO &amp; Organic Growth</a></li>
                    <li><a href="{{ route('tyg.branding-services') }}">Brand Strategy &amp; Identity</a></li>
                    <li><a href="{{ route('tyg.development-services') }}">Product Design &amp; UX Sprint</a></li>
                    <li><a href="{{ route('tyg.development-services') }}">Web Design &amp; Development</a></li>
                </ul>
            </div>
            <div>
                <h6>Editorial &amp; Legal</h6>
                <ul>
                    <li><a href="{{ url('/case-studies') }}">Case Archives {{ date('Y') }}</a></li>
                    <li><a href="{{ url('/blog') }}">Blog &amp; Guides</a></li>
                    <li><a href="{{ url('/terms-condition') }}">Standard Engagement Terms</a></li>
                    <li><a href="{{ url('/privacy-policy') }}">Privacy Governance</a></li>
                </ul>
            </div>
        </div>

        <div class="tyg-footer__bottom">
            <span>© {{ date('Y') }} There You Grow (TYG) Digital LLP. All rights reserved.</span>
            <span class="tyg-footer__meta">INR / USD Enabled <i aria-hidden="true">•</i> Global Production Node</span>
        </div>
    </div>
</footer>
