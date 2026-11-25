<header class="tyg-header" data-header>
    <nav class="tyg-nav">
        <a href="{{ route('home') }}" class="tyg-nav__logo" aria-label="There You Grow home">TYG</a>

        <ul class="tyg-nav__links">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a></li>
            <li class="tyg-nav__drop">
                <button type="button" class="{{ request()->routeIs('tyg.seo-services', 'tyg.branding-services', 'tyg.development-services') ? 'is-active' : '' }}" aria-haspopup="true">
                    Services
                    <svg width="10" height="10" viewBox="0 0 10 10" aria-hidden="true"><path d="M1 3l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>
                </button>
                <div class="tyg-nav__menu">
                    <a href="{{ route('tyg.seo-services') }}"><span>SEO</span> Get Found.</a>
                    <a href="{{ route('tyg.branding-services') }}"><span>Branding</span> Get Known.</a>
                    <a href="{{ route('tyg.development-services') }}"><span>Development</span> Get Built.</a>
                </div>
            </li>
            <li><a href="{{ route('tyg.seo-services') }}#packages">Packages</a></li>
            <li><a href="{{ url('/case-studies') }}">Our Work</a></li>
        </ul>

        <div class="tyg-nav__icons">
            <button type="button" class="tyg-icon-btn" data-search-open aria-label="Search">
                <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
            </button>
            <a href="{{ auth()->check() ? route('user.dashboard') : route('login') }}" class="tyg-icon-btn" aria-label="{{ auth()->check() ? 'Dashboard' : 'Login' }}">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </a>
            <button type="button" class="tyg-icon-btn tyg-cart-btn" data-cart-open aria-label="Open cart">
                <svg viewBox="0 0 24 24"><path d="M5 8h14l-1.2 12H6.2z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>
                <span class="tyg-cart-count" data-cart-count>0</span>
            </button>
        </div>

        <a href="{{ url('/contact') }}" class="tyg-btn tyg-btn--red tyg-nav__cta" data-magnetic>Let’s Talk</a>

        <button type="button" class="tyg-burger" data-menu-toggle aria-label="Menu"><span></span><span></span></button>
    </nav>

    <div class="tyg-mobile" data-mobile-menu>
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('tyg.seo-services') }}">SEO Services</a>
        <a href="{{ route('tyg.branding-services') }}">Branding Services</a>
        <a href="{{ route('tyg.development-services') }}">Development Services</a>
        <a href="{{ route('tyg.seo-services') }}#packages">Packages</a>
        <a href="{{ url('/case-studies') }}">Our Work</a>
        <a href="{{ auth()->check() ? route('user.dashboard') : route('login') }}">{{ auth()->check() ? 'Dashboard' : 'Login' }}</a>
        <a href="{{ url('/contact') }}" class="tyg-btn tyg-btn--red">Let’s Talk</a>
    </div>
</header>

<div class="tyg-search" data-search>
    <form action="{{ route('blog.filter') }}" method="get" class="tyg-search__form">
        <input type="search" name="search" placeholder="Search guides & articles…" aria-label="Search">
        <button type="submit" class="tyg-btn tyg-btn--red">Search</button>
    </form>
    <button type="button" class="tyg-search__close" data-search-close aria-label="Close search">×</button>
</div>
