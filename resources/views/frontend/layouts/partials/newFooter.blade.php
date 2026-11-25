<div class="container-fluid container-padding pt-100 pb-100">
    <div class="row">
        <div class="col-12 col-md-8">
            <a href="{{ route('home') }}" class="logo mb-30">
                <img src="{{ asset(setting('light_logo')) }}"
                    alt="logo"> </a>
            <!--<div class="mb-20 text-white"><img src="{{ asset('general/images/location.png') }}"
                    class="me-2" />{{ $contactInfo->content->address->value }}</div>
            <div class="mb-20 text-white"><img src="{{ asset('general/images/phone.png') }}"
                    class="me-2" />{{ $contactInfo->content->contact_number->value }}</div>-->
            <div class="mb-20 text-white"><img src="{{ asset('general/images/email.png') }}"
                    class="me-2" /><a href="mailto:saurav@buildsaaslinks.com">saurav@buildsaaslinks.com</a></div>
        </div>
        <!--<div class="col-12 col-md-4">
            <h3 class="footer-title mb-2">{{ __('Company') }}</h3>
            <ul>
                @foreach ($navigations as $nav)
                    <li class="mb-2"><a href="{{ $nav->slug == '/' ? route('home') : $nav->slug }}"
                            target="{{ $nav->target }}" class="text-white"> {{ _tr($nav->name) }}</a></li>
                @endforeach
            </ul>
        </div>-->
    </div>

</div>
<div class="footer-container">
    <div class="container-fluid container-padding">
        <div class="d-block d-md-flex justify-md-content-between gap-md-3" style="justify-content: center;" >
            <div class="text-white mb-2 mb-md-0">{{ setting('copyright') }} <a class="text-white"
                    href="{{ url('/') }}">{{ title(setting('site_title')) }}</a></div>
            <div class="mb-2 mb-md-0">
                <ul class="d-flex align-items-center gap-4 wow fadeInDown" data-wow-delay="200ms"
                    data-wow-duration="1500ms">
                   <!-- <li><img src="{{ asset('general/images/facebook.png') }}" /></li>
                    <li><img src="{{ asset('general/images/twitter.png') }}" /></li>
					<li><img src="{{ asset('general/images/instagram.png') }}" /></li>
					-->
                    <li><a href="https://www.linkedin.com/in/saurav-agarwal-there-you-grow/" target="_blank"><img src="{{ asset('general/images/linkedin.png') }}" /></a></li>
                    
                </ul>
            </div>
            <div class="mb-2 mb-md-0">
                <ul class="d-flex align-items-center gap-4 wow fadeInDown tc-link"  data-wow-delay="200ms"
                    data-wow-duration="1500ms">
                    <li><a href="{{ setting('terms_condition_link') }}"
                            class="text-white text-decoration-underline">{{ __('Terms & Conditions') }}</a></li>
                    <li><a href="{{ setting('privacy_policy_link') }}"
                            class="text-white text-decoration-underline">Privacy Policy</a></li>
                    <li><a href="{{ setting('refund_policy_link') }}"
                            class="text-white text-decoration-underline">Refund Policy</a></li>
                    <li><a href="{{ setting('cancellation_policy_link') }}"
                            class="text-white text-decoration-underline">Cancellation Policy</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
