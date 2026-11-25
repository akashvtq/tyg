@extends('frontend.layouts.app')
@section('title')
    {{ __('Home') }}
@endsection
@section('content')
    @php
        $videos = [
            ['id' => 'banner', 'title' => 'Build SaaS Links', 'src' => asset('frontend/videos/home_banner.mp4')],
            ['id' => 'step1', 'title' => 'Step 1: Create use cases', 'src' => asset('frontend/videos/step1.mp4')],
            ['id' => 'step2', 'title' => 'Step 2: Filtering the sites', 'src' => asset('frontend/videos/step2.mp4')],
            [
                'id' => 'step3',
                'title' => 'Step 3: Building the contextual orders',
                'src' => asset('frontend/videos/step3.mp4'),
            ],
            // [
            //     'id' => 'Case_Study_1',
            //     'title' => 'Alliance Virtual Office- Link Building Campaign',
            //     'src' => asset('frontend/videos/case_study/Case Study 1_ Alliance Virtual Office.mp4'),
            // ],
            // [
            //     'id' => 'Case_Study_2',
            //     'title' => 'Hostinger.com - Links To Stat Pages',
            //     'src' => asset('frontend/videos/case_study/Case Study 2_ Hostinger.com'),
            // ],
            // [
            //     'id' => 'Case_Study_3',
            //     'title' => 'Freshworks CRM - Landing Page Link Building',
            //     'src' => asset('frontend/videos/case_study/Case Study 3_ Freshworks CRM.mp4'),
            // ],
            // [
            //     'id' => 'Case_Study_4',
            //     'title' => 'Polo- The QR Code Generator - Link Building Campaign',
            //     'src' => asset('frontend/videos/case_study/Case Study 4_ The QR Code Generator.mov'),
            // ],
            // [
            //     'id' => 'Case_Study_5',
            //     'title' => 'Science Soft - Branded Link Building',
            //     'src' => asset('frontend/videos/case_study/Case Study 5_ Science Soft.mp4'),
            // ],
            // [
            //     'id' => 'Case_Study_6',
            //     'title' => 'Advantage Club - Links To The Product Pages',
            //     'src' => asset('frontend/videos/case_study/Case Study 6_ Advantage Club.mp4'),
            // ],
            // [
            //     'id' => 'Case_Study_7',
            //     'title' => 'Prospeo.io - Link To Landing & Blogs',
            //     'src' => asset('frontend/videos/case_study/Case Study 7_ Prospeo.io.mp4'),
            // ],
            // [
            //     'id' => 'Case_Study_8',
            //     'title' => 'CMS Minds - Links To The Service Pages',
            //     'src' => asset('frontend/videos/case_study/Case Study 8_ CMS Minds.mp4'),
            // ],
        ];
    @endphp
    {{-- @foreach ($components as $data)
		@includeIf('frontend.page.component._'.$data->section)
	@endforeach --}}

    <div class="container-fluid container-padding ">
        <!-- Section: Heading -->
        <div class="row mb-4">
            <div class="col-md-6 banner-left-div">
                <p class="hero-eyebrow">Build SaaS Links</p>
                <h2 class="mb-2">SaaS <span class="bolder">Link Building</span></h2>
                <ul class="list-unstyled mt-3">
                    <li class="d-inline-block">Links on real business/services website <a
                            href="https://docs.google.com/spreadsheets/d/1hDc1YxFYop6Jx5sBAt24OgwkO6chT-6CVvLwECp84qU/edit?gid=0#gid=0"
                            target="_blank" class="theme-light-font">(See examples)</a></li>
                    <li>Insertions after approval</li>
                    <li>1 year refundable guarantee</li>
                </ul>
                <button type="button" class="hero-watch" data-bs-toggle="modal" data-bs-target="#videoModalbanner">
                    <span class="hero-watch__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="14" height="14"><path d="M8 5l11 7-11 7z" fill="currentColor"/></svg>
                    </span>
                    Watch Video
                </button>
                <a class="btn common-btn mb-2" target="_blank" href="https://calendly.com/saurav-buildsaaslinks/30min">Book a meeting</a>
            </div>
        </div>

        <div class="mt-100 gradient-border logo-banner">
            <h2 class="fw-bold mb-5 text-center">Brands That <span class="bolder">Trust Us</span></h2>
            <div class="logo_items">
                <img src="{{ asset('frontend/brands/HOSTINGER.png') }}" alt="Hostinger" width="130">
                <img src="{{ asset('frontend/brands/FRESHWORKS.png') }}" alt="Freshworks" width="130">
                <img src="{{ asset('frontend/brands/UNIQODE.png') }}" alt="Uniqode" width="130">
                <img src="{{ asset('frontend/brands/ScienceSoft.png') }}" alt="ScienceSoft" width="130">
                <img src="{{ asset('frontend/brands/ALLIANCE.png') }}" alt="Alliance Virtual Offices" width="130">
                <img src="{{ asset('frontend/brands/CMS.png') }}" alt="CMS Minds" width="130">
                <img src="{{ asset('frontend/brands/THE_QR_CODE_GENERATOR.png') }}" alt="The QR code Generator"
                    width="130">
                <img src="{{ asset('frontend/brands/prospeo.png') }}" alt="Prospeo" width="130">
            </div>
			<div class="logo_items">
                <img src="{{ asset('frontend/brands/HOSTINGER.png') }}" alt="Hostinger" width="130">
                <img src="{{ asset('frontend/brands/FRESHWORKS.png') }}" alt="Freshworks" width="130">
                <img src="{{ asset('frontend/brands/UNIQODE.png') }}" alt="Uniqode" width="130">
                <img src="{{ asset('frontend/brands/ScienceSoft.png') }}" alt="ScienceSoft" width="130">
                <img src="{{ asset('frontend/brands/ALLIANCE.png') }}" alt="Alliance Virtual Offices" width="130">
                <img src="{{ asset('frontend/brands/CMS.png') }}" alt="CMS Minds" width="130">
                <img src="{{ asset('frontend/brands/THE_QR_CODE_GENERATOR.png') }}" alt="The QR code Generator"
                    width="130">
                <img src="{{ asset('frontend/brands/prospeo.png') }}" alt="Prospeo" width="130">
            </div>
			<div class="logo_items">
                <img src="{{ asset('frontend/brands/HOSTINGER.png') }}" alt="Hostinger" width="130">
                <img src="{{ asset('frontend/brands/FRESHWORKS.png') }}" alt="Freshworks" width="130">
                <img src="{{ asset('frontend/brands/UNIQODE.png') }}" alt="Uniqode" width="130">
                <img src="{{ asset('frontend/brands/ScienceSoft.png') }}" alt="ScienceSoft" width="130">
                <img src="{{ asset('frontend/brands/ALLIANCE.png') }}" alt="Alliance Virtual Offices" width="130">
                <img src="{{ asset('frontend/brands/CMS.png') }}" alt="CMS Minds" width="130">
                <img src="{{ asset('frontend/brands/THE_QR_CODE_GENERATOR.png') }}" alt="The QR code Generator"
                    width="130">
                <img src="{{ asset('frontend/brands/prospeo.png') }}" alt="Prospeo" width="130">
            </div>
        </div>

        <div class="text-center mt-100 pricing">
            <h2 class="fw-bold mb-4">Transparent <span class="bolder">Pricing</span></h2>
            <h5 class="mb-4 fw-normal theme-light-font secondary-title">Choose your right plan today for your journey</h5>
        </div>

        <div class="row g-3 justify-content-center rounded-4 pt-20">
            <div class="p-3 d-block d-md-flex flex-md-column flex-lg-row gap-3 align-items-start pricing-container">
                <div class="col-md-12 col-lg-9">
                    <div class="row">
                        @foreach (count($services) > 0 ? $services : [] as $s)
                            <div class="col-md-4 mb-4">
                                <div class="rounded p-4 text-start h-100 theme-light-border">
                                    <h4 class="fw-bold text-white mb-2">
									@if($s->price>300)
										Branded SAAS Links
									@else
									SAAS Links
									@endif
									</h4>
                                    <p class="fw-normal text-white mb-1">{!! _tr($s->name) !!}</p>
                                    <div class="small theme-light-font mb-20" style="color: #585858">{!! _tr($s->description) !!}
                                    </div>
                                    <h2 class="fw-bold text-white mb-2">{!! '$' . $s->price !!}<small
                                            class="text-white">/link</small></h2>
                                    <a class="btn common-btn mt-3 w-100 mb-2"
                                        href="{{ route('user.order.services') }}">Order Now</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-12 col-lg-3 whats_included pe-2">
                    <h4 class="fw-bold text-white mb-3">What's Covered :</h4>
                    <ul>
                        <li>Site filtrations</li>
                        <li>Monthly links target</li>
                        <li>Chosen DR guaranteed</li>
                        <li>5k traffic guaranteed</li>
                        <li>1 year restoration, replacement, refund guarantee (on order)</li>
                        <li>Includes content editing</li>
                        <li>1 link / URL</li>
                        <li>Do-follow linkt</li>
                        <li>Monthly report sheet</li>
                        
                    </ul>
                    <a class="btn common-btn mt-5 w-100 mb-2" target="_blank" href="https://calendly.com/saurav-buildsaaslinks/30min">Book a meeting</a>
                </div>
            </div>
        </div>

        <div class="text-center mt-100 pricing">
            <h2 class="fw-bold mb-4">Proof in <span class="bolder">Performance</span></h2>
            <div class="proof-performance h-100 mt-60">
                <div class="table-responsive">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Total Links</th>
                                <th>Work Span</th>
                                <th>DR Change</th>
                                <th>Traffic Change</th>
                                <th>Traffic Value Change</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><img class="brand-icon" src="{{ asset('frontend/brands/ALLIANCE.png') }}" alt="Alliance Virtual Offices"
                                        width="130">
										<span class="badge showmobileinline">225+ Links</span>
										</td>
                                <td class="hidedesktop">
                                    <span class="badge">225+</span>
                                </td>
                                <td class="lefttd">
								<span class="showmobile">Work Span</span>
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="{{ asset('general/images/timer.png') }}" />
                                        <span class="pt-1">15+ months</span>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">DR Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">+7.6 %</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">66 → 71</div>
                                    </div>
                                </td>
                                <td class="lefttd"><span class="showmobile">Traffic Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">46.4 %</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">28K → 41K</div>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">Traffic Value Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/money.png') }}" /></span>
                                            <span class="font-18">20.0%</span>
                                        </div>
                                        <div class="graph-bottom-text text-start  w-100">$125K → $150K</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img class="brand-icon" src="{{ asset('frontend/brands/THE_QR_CODE_GENERATOR.png') }}"
                                        alt="The QR code Generator" width="130">
										<span class="badge showmobileinline">500+ Links</span>
										</td>
                                <td class="hidedesktop">
                                    <span class="badge">500+</span>
                                </td>
                                <td class="lefttd"><span class="showmobile">Work Span</span>
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="{{ asset('general/images/timer.png') }}" />
                                        <span class="pt-1">17+ months</span>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">DR Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">+4.1 %</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">74 → 77</div>
                                    </div>
                                </td>
                                <td class="lefttd"><span class="showmobile">Traffic Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">16.7%</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">1.2M → 1.4M</div>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">Traffic Value Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/money.png') }}" /></span>
                                            <span class="font-18">00.0%</span>
                                        </div>
                                        <div class="graph-bottom-text text-start  w-100">$260K → $260K</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img class="brand-icon" src="{{ asset('frontend/brands/ScienceSoft.png') }}" alt="ScienceSoft"
                                        width="130">
										<span class="badge showmobileinline">300+ Links</span>
										</td>
                                <td class="hidedesktop">
                                    <span class="badge">300+</span>
                                </td>
                                <td class="lefttd"><span class="showmobile">Work Span</span>
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="{{ asset('general/images/timer.png') }}" />
                                        <span class="pt-1">36+ months</span>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">DR Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">+1.3 %</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">76 → 77</div>
                                    </div>
                                </td>
                                <td class="lefttd"><span class="showmobile">Traffic Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">65.7%</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">140K → 232K</div>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">Traffic Value Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/money.png') }}" /></span>
                                            <span class="font-18">66.7%</span>
                                        </div>
                                        <div class="graph-bottom-text text-start  w-100">$600K → $1M</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img  class="brand-icon" src="{{ asset('frontend/brands/AdvantageClub.png') }}" alt="AdvantageClub.ai" style="max-width: 150px;">
								<span class="badge showmobileinline">60+ Links</span>
                                </td>
                                <td class="hidedesktop">
                                    <span class="badge">60+</span>
                                </td>
                                <td class="lefttd"><span class="showmobile">Work Span</span>
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="{{ asset('general/images/timer.png') }}" />
                                        <span class="pt-1">4 months</span>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">DR Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">+29.3 %</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">41 → 53</div>
                                    </div>
                                </td>
                                <td class="lefttd"><span class="showmobile">Traffic Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">242.1%</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">19K → 65K</div>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">Traffic Value Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/money.png') }}" /></span>
                                            <span class="font-18">218.2%</span>
                                        </div>
                                        <div class="graph-bottom-text text-start  w-100">$3.3K → $10.5K</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img  class="brand-icon" src="{{ asset('frontend/brands/CMS.png') }}" alt="CMS Minds" width="130"><span class="badge showmobileinline">60+ Links</span>
                                </td>
                                <td class="hidedesktop">
                                    <span class="badge">60+</span>
                                </td>
                                <td class="lefttd"><span class="showmobile">Work Span</span>
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="{{ asset('general/images/timer.png') }}" />
                                        <span class="pt-1">11 months</span>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">DR Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">+58.5 %</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">41 → 65</div>
                                    </div>
                                </td>
                                <td class="lefttd"><span class="showmobile">Traffic Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">400.0%</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">1.3K → 6.5K</div>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">Traffic Value Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/money.png') }}" /></span>
                                            <span class="font-18">422.7%</span>
                                        </div>
                                        <div class="graph-bottom-text text-start  w-100">$4.4K → $23K</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img  class="brand-icon" src="{{ asset('frontend/brands/FRESHWORKS.png') }}" alt="Freshworks"
                                        width="130"><span class="badge showmobileinline">120+ Links</span></td>
                                <td class="hidedesktop">
                                    <span class="badge">120+</span>
                                </td>
                                <td class="lefttd"><span class="showmobile">Work Span</span>
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="{{ asset('general/images/timer.png') }}" />
                                        <span class="pt-1">3 months</span>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">DR Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">+1.1 %</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">90 → 91</div>
                                    </div>
                                </td>
                                <td class="lefttd"><span class="showmobile">Traffic Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">5.0%</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">400K → 420K</div>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">Traffic Value Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/money.png') }}" /></span>
                                            <span class="font-18">28.6%</span>
                                        </div>
                                        <div class="graph-bottom-text text-start  w-100">$1.4M → $1.8M</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img  class="brand-icon" src="{{ asset('frontend/brands/prospeo.png') }}" alt="Prospeo" width="130"><span class="badge showmobileinline">60+ Links</span>
                                </td>
                                <td class="hidedesktop">
                                    <span class="badge">60+</span>
                                </td>
                                <td class="lefttd"><span class="showmobile">Work Span</span>
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="{{ asset('general/images/timer.png') }}" />
                                        <span class="pt-1">6 months</span>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">DR Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">+112.0 %</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">25 → 53</div>
                                    </div>
                                </td>
                                <td class="lefttd"><span class="showmobile">Traffic Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/graph.png') }}" /></span>
                                            <span class="graph-text">962.5%</span>
                                        </div>
                                        <div class="graph-bottom-text w-100">800K → 8.5K</div>
                                    </div>
                                </td>
                                <td class="righttd"><span class="showmobile">Traffic Value Change</span>
                                    <div class="d-inline-block">
                                        <div class="text-start">
                                            <span><img src="{{ asset('general/images/money.png') }}" /></span>
                                            <span class="font-18">700.0%</span>
                                        </div>
                                        <div class="graph-bottom-text  w-100">$1k → $8k</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="text-center  mt-100" id="work">
            <div class="d-flex gap-3 justify-content-center">
                <h2 class="text-white">Case <span class="theme-text-color">Studies</span></h2>
                
            </div>
            <div class="">
                @php
                    $casestudies = [
                        [
                            'id' => 'cs_1',
                            'bgimg' => 'AllianceVirtualoffice.png',
                            'video_link' => 'Case Study 1_ Alliance Virtual Office.mp4',
                            'title' => 'Alliance Virtual Office- Link Building Campaign',
                            'description' =>
                                'Alliance Virtual Office offers virtual offices to the small businesses who can\'t afford to have physical locations in different parts of the country in the US.',
                        ],
                        [
                            'id' => 'cs_2',
							'bgimg' => 'Hostinger.png',
                            'video_link' => 'Case Study 2_ Hostinger-com.mp4',
                            'title' => 'Hostinger.com - Links To Stat Pages',
                            'description' =>
                                'Hostinger, a well-known hosting company based in Cyprus wanted us to get links to their statistics and landing pages.',
                        ],
                        [
                            'id' => 'cs_3','bgimg' => 'FreshWork.png',
                            'video_link' => 'Case Study 3_ Freshworks CRM.mp4',
                            'title' => 'Freshworks CRM - Landing Page Link Building',
                            'description' =>
                                'Freshworks CRM is a powerful, easy-to-use CRM solution to unlock value across Sales, Marketing, and Customer Support teams. They wanted to acquire SaaS links at a rapid pace.',
                        ],
                        [
                            'id' => 'cs_4','bgimg' => 'TheQRCodeGenerator.png',
                            'video_link' => 'Case Study 4_ The QR Code Generator.mov',
                            'title' => 'Polo- The QR Code Generator - Link Building Campaign',
                            'description' =>
                                'Polo is a custom QR and dynamic QR code generator that wants to acquire SaaS links, but isn’t satisfied with the existing context.',
                        ],
                        [
                            'id' => 'cs_5','bgimg' => 'SCIENCESOFT.png',
                            'video_link' => 'Case Study 5_ Science Soft.mp4',
                            'title' => 'Science Soft - Branded Link Building',
                            'description' =>
                                'Science Soft is an IT consulting firm that is mainly into building healthcare, Education, Manufacturing, Finance software. They had some of the toughest criteria in the industry. ',
                        ],
                        [
                            'id' => 'cs_6','bgimg' => 'Advantageclub.png',
                            'video_link' => 'Case Study 6_ Advantage Club.mp4',
                            'title' => 'Advantage Club - Links To The Product Pages',
                            'description' =>
                                'Advantage Club is an employee recognition platform that helps companies reward achievements, foster appreciation, and boost productivity to improve retention.',
                        ],
                        [
                            'id' => 'cs_7','bgimg' => 'Prospeo.png',
                            'video_link' => 'Case Study 7_ Prospeo-io.mp4',
                            'title' => 'Prospeo.io - Link To Landing & Blogs',
                            'description' =>
                                'Prospeo is an email finder and verifier tool. They wanted to acquire contextual SaaS links and rank for their product pages such as email finder and verifier.',
                        ],
                        [
                            'id' => 'cs_8','bgimg' => 'CMSMinds.png',
                            'video_link' => 'Case Study 8_ CMS Minds.mp4',
                            'title' => 'CMS Minds - Links To The Service Pages',
                            'description' =>
                                'cmsMinds is a dedicated WordPress design & development company that also deals in custom design, development, migration & maintenance.',
                        ],
                    ];
                @endphp
                <div class="row">
                    <div class="swiper case-studies pt-50 pb-100">
                        <div class="swiper-wrapper">
                            @foreach ($casestudies as $key => $c)
                                <div class="card swiper-slide">
                                    <div class="card-body p-4">
                                        <div role="button" data-id="{{ $key + 1 }}" class="cs_video"
                                            style="border: 1.04px solid rgba(255, 255, 255, 0.34);position: relative;margin-bottom:20px;border-radius:15px;    background-color: #020425;
											">
											<img src="{{ asset('frontend/casestudy/') }}/{{$c['bgimg']}}" style="width:100%;" />
                                            <img src="{{ asset('general/images/video.png') }}" height="16"
                                                style="position: absolute;bottom:10px;right:10px"
                                                 />
                                        </div>
                                        <h4 class="card-title text-white mb-2 text-start">{{ $c['title'] }}</h4>
                                        <h6 class="card-text fw-normal text-white truncate-1 text-start">
                                            {{ $c['description'] }}
                                        </h6>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="cs-swiper-button-prev me-5">‹</button>
                        <button class="cs-swiper-button-next">›</button>
                    </div>
                </div>
                @include('frontend.casestudies', ['casestudies' => $casestudies])
            </div>
        </div>
		
		  <div class="mt-100 gradient-border logo-banner">
            <h2 class="fw-bold mb-5 text-center">Where our <span class="bolder">Clients Get Seen</span></h2>
            <div class="logo_items">
                <img width="150" src="{{ asset('frontend/clients/Big Commerce.png') }}" alt="Big Commerce">
				<img width="150" src="{{ asset('frontend/clients/Hubspot.png') }}" alt="Hubspot">
				<img width="150" src="{{ asset('frontend/clients/Zoom.png') }}" alt="Zoom">
				<img width="150" src="{{ asset('frontend/clients/Canva.png') }}" alt="Canva">
				<img width="150" src="{{ asset('frontend/clients/Cloud Ways.png') }}" alt="Cloud Ways">
            </div>
			<div class="logo_items">
                <img width="150" src="{{ asset('frontend/clients/Big Commerce.png') }}" alt="Big Commerce">
				<img width="150" src="{{ asset('frontend/clients/Hubspot.png') }}" alt="Hubspot">
				<img width="150" src="{{ asset('frontend/clients/Zoom.png') }}" alt="Zoom">
				<img width="150" src="{{ asset('frontend/clients/Canva.png') }}" alt="Canva">
				<img width="150" src="{{ asset('frontend/clients/Cloud Ways.png') }}" alt="Cloud Ways">
            </div>
			<div class="logo_items">
                <img width="150" src="{{ asset('frontend/clients/Big Commerce.png') }}" alt="Big Commerce">
				<img width="150" src="{{ asset('frontend/clients/Hubspot.png') }}" alt="Hubspot">
				<img width="150" src="{{ asset('frontend/clients/Zoom.png') }}" alt="Zoom">
				<img width="150" src="{{ asset('frontend/clients/Canva.png') }}" alt="Canva">
				<img width="150" src="{{ asset('frontend/clients/Cloud Ways.png') }}" alt="Cloud Ways">
            </div>
        </div>
		
        <div class="text-center mt-100">
            <div class="d-flex gap-3 justify-content-center">
                
                <h2 class="text-white"><span class="theme-text-color">You</span> And <span class="theme-text-color">US</span></h2>
                
            </div>
            <img src="{{ asset('general/images/you_us.png') }}" class="img-fluid mt-4 pt-4" />
        </div>

        <div class="text-center mt-100">
            <div class="d-flex gap-3 justify-content-center">
                <h2 class="text-white">How We Make <span class="theme-text-color">It Happen</span></h2>
                
            </div>
            <div class="row how_we_do_it pt-50 gap-sm-1 gap-lg-5 justify-content-center">
                <div class="card col-sm-3">
                    <div class="card-body">
                        <h3 class="mb-4">Build use cases</h3>
                    </div>
                    <div class="card-footer">
                        <a class="watch_now ps-5" data-bs-toggle="modal" data-bs-target="#videoModalstep1">Watch now</a>
                    </div>
                </div>
                <div class="card col-sm-3">
                    <div class="card-body">
                        <h3 class="mb-4">Filter The Domain</h3>
                    </div>
                    <div class="card-footer">
                        <a class="watch_now ps-5" data-bs-toggle="modal" data-bs-target="#videoModalstep2">Watch
                            now</a>
                    </div>
                </div>
                <div class="card col-sm-3">
                    <div class="card-body">
                        <h3 class="mb-4">Create The Order</h3>
                    </div>
                    <div class="card-footer">
                        <a class="watch_now ps-5" data-bs-toggle="modal" data-bs-target="#videoModalstep3">Watch now</a>
                    </div>
                </div>
            </div>
            @foreach ($videos as $video)
                @include('frontend.modal', ['video' => $video])
            @endforeach

        </div>
    </div>
    <div class="text-center mt-100 pricing">
        <div class="">
            <div class="">
                <div class="d-flex gap-3 justify-content-center">
                    <h2 class="fw-bold text-white mb-2">What <span class="theme-text-color">Client Say</span></h2>
                    
                </div>
                <h4 class="mb-4 fw-normal ml-20 mr-20 mt-50 theme-light-font secondary-title">Join thousand of satisfied users who rely
                    on
                    our
                    platform for their
                    <br />personal
                    and professional needs
                </h4>
            </div>
        </div>
        <div class="">
            <div class="swiper testimonials pt-50 pb-50">
                <div class="swiper-wrapper" style="height: auto">
                    @php
                        $clients = [
                            [
                                'avatar' => 'ujjval_ladha.png',
                                'name' => 'Ujjawal Laddha',
                                'designation' => 'Business Development Executive, Cmsminds.com',
                                'review' =>
                                    '“Saurav has a strong link building network and offers great value for the price. His links helped us grow our traffic 4x and improve our SEO rankings. I recommend him to any SaaS or website owner looking to build site authority.”',
                            ],
                            [
                                'avatar' => 'collin_davis.png',
                                'name' => 'Collin Davis',
                                'designation' => 'Chief Search Experience Officer at Blue Digits',
                                'review' =>
                                    '“I have worked with multiple link vendors in the past but the good part about working with Saurav is that he understands your brief and delivers accordingly.”',
                            ],
                            [
                                'avatar' => 'mery_derosa.png',
                                'name' => 'Mary J Derosa',
                                'designation' => 'CEO and Chief Editor at PRable.org',
                                'review' =>
                                    '“Working with Saurav has been an absolute pleasure! We hired his services, and from day one, he impressed us with his organization, efficiency.”',
                            ],
                            [
                                'avatar' => 'pooja_agrawal.png',
                                'name' => 'Pooja Agrawal',
                                'designation' => 'SEO Organic growth specialist, Uniqode',
                                'review' =>
                                    '“The platform has revolutionized the way I work. It’s intuitive flexible and has made me more productive than ever before.”',
                            ],
                            [
                                'avatar' => 'tania_shchahrykovich.png',
                                'name' => 'Tatsiana Shchahrykovich',
                                'designation' => 'Marketing Specialist, Science Soft',
                                'review' =>
                                    '“My Experience of working with Saurav is very positive. Saurav is genuiely passionate about his work. I can always rely on him if I need meaningful.”',
                            ],
                            [
                                'avatar' => 'lekha.png',
                                'name' => 'Lekha Kothari',
                                'designation' => 'Head-Business Ops, Aureate Labs',
                                'review' =>
                                    '“Gem of a partner is how I’d describe Saurav from BSL. He consistently finds high-quality, relevant links that actually make a difference. He’s smart, thorough, and easy to work with, always on top of the process and delivers results without you having to micromanage”',
                            ],
                            [
                                'avatar' => 'ParthMavani.png',
                                'name' => 'Parth Mavani',
                                'designation' => 'SEO Specialist at Alliance Virtual Offices',
                                'review' =>
                                    '“Saurav from Build SAAS Link is an exceptional link-building partner. He identifies high-quality opportunities, builds strong relationships, and aligns every project with SEO goals. Reliable, communicative, and results-focused—a true asset.”',
                            ],
                            [
                                'avatar' => 'Matt.png',
                                'name' => 'Matt Emgi',
                                'designation' => 'Founder at Emgi Group ',
                                'review' =>
                                    '“Working with BSL on link-building projects was great. His SaaS connections and SEO knowledge added real value. We shared insights and grew a mutually beneficial partnership. Highly recommend for anyone seeking link-building expertise.”',
                            ]
                        ];
                    @endphp
                    @foreach ($clients as $c)
                        <div class="card swiper-slide" style="height: auto;">
                            <div class="card-body">
                                <div class="d-flex gap-2 align-items-center">
                                    <img src="{{ asset('frontend/testimonials/' . $c['avatar']) }}"
                                        class="img-thumbnail rounded-circle border-0 bg-transparent" width="74px" />
                                    <div class=" text-start ps-2">
                                        <h3 class="testimonialname text-white">{{ $c['name'] }}</h3>
                                        <div class="text-white testimonial-designation">{{ $c['designation'] }}</div>
                                    </div>
                                </div>
                                <div class="mt-4 text-start testimonial-review theme-light-font">{{ $c['review'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
				<button class="cs-swiper-button-prev me-5">‹</button>
                <button class="cs-swiper-button-next">›</button>
            </div>
        </div>
        
    </div>
    <div class="container-fluid container-padding">
        <div class="text-center mt-100">
            <a href="/login"><img src="{{ asset('general/images/not_confident_yet.png?v1') }}" class="img-fluid mt-4 pt-4" /></a>
        </div>
    </div>
    @include('frontend.page.component._seo_pages')
    @php
        $faqs = [
            [
                'title' => 'How long is the link guarantee?',
                'answer' => 'Most of our links remain live for over a year, depending on how often the publisher updates their site. Each link comes with a 12-month guarantee. If a link goes offline within this period, we first attempt restoration. If that fails, we provide a replacement. If neither is possible, we refund 95% of the link cost.'
            ],
            [
                'title' => 'Do you create full orders?',
                'answer' => 'Yes. Once you provide your criteria for filtering sites, we handle all the heavy lifting. You only need to review and approve the placements.'
            ],
            [
                'title' => 'How can I claim my risk-free link?',
                'answer' => 'You can test us with your first link by paying the standard fee. If you’re not satisfied with the link, we’ll issue a full refund right away.'
            ],
            [
                'title' => 'How long does it take to build a link?',
                'answer' => 'Most links are delivered within a week. However, factors like publisher responsiveness or order complexity can occasionally cause delays.'
            ],
            [
                'title' => 'How many links can you build per campaign?',
                'answer' => 'We can build as many SaaS-relevant links as you need each month. Campaign size is fully customizable.'
            ],
            [
                'title' => 'What types of sites do you build links on?',
                'answer' => 'We focus on high-authority, niche-relevant websites that have strong organic traffic and established editorial standards. This ensures the links are both valuable and sustainable.'
            ],
            [
                'title' => 'Can I choose the anchor text and target URL?',
                'answer' => 'Yes. You can specify the exact anchor text and URL for each link, or let us suggest options based on SEO best practices.'
            ],
            [
                'title' => 'What happens if I want to scale my campaign?',
                'answer' => 'You can scale at any time. Whether you want 10 links or 100+ per month, we adjust the campaign to meet your goals while maintaining quality.'
            ],
            [
                'title' => 'Do you provide reports for the links built?',
                'answer' => 'Absolutely. Every order includes a detailed report with live URLs, anchor texts, target pages, and key site metrics.'
            ],
            [
                'title' => 'Are all your links white-hat?',
                'answer' => 'Yes. We only use outreach-driven, editorially placed links. No PBNs, no spammy directories, and no shortcuts that could harm your site.'
            ]
        ]
    @endphp

    <div class="container-fluid container-padding">
        <div class="text-center mt-100 pricing">
            <h2 class="fw-bold text-white mb-2">Your Questions?</h2>
            <h2 class="theme-text-color mb-3">Answered</h2>
            <h4 class="mb-4 fw-normal theme-light-font secondary-title">Find clear, concise answer to common questions and
                get the<br />
                information you need-fast and hassle free</h4>
            <div class="row pt-20 faq">
                <div class="col-12">
                    <div class="accordion">
                        @foreach ($faqs as $key => $f)
                        <div class="accordion-item gradient-border">
                            <h2 class="accordion-header" id="flush-heading{{$key}}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse{{$key}}" aria-expanded="false"
                                    aria-controls="flush-collapse{{$key}}">
                                    {!! $f['title'] !!}
                                </button>
                            </h2>
                            <div id="flush-collapse{{$key}}" class="accordion-collapse collapse"
                                aria-labelledby="flush-heading{{$key}}" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body"> {!! $f['answer'] !!}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const players = {};
            @foreach ($videos as $video)
                document.getElementById('videoModal{{ $video['id'] }}').addEventListener('shown.bs.modal',
                    function() {
                        if (!players['{{ $video['id'] }}']) {
                            players['{{ $video['id'] }}'] = videojs('video_player_{{ $video['id'] }}');
                        }
                        players['{{ $video['id'] }}'].playbackRate(1.25);
                    });

                document.getElementById('videoModal{{ $video['id'] }}').addEventListener('hidden.bs.modal',
                    function() {
                        if (players['{{ $video['id'] }}']) {
                            players['{{ $video['id'] }}'].pause();
                            players['{{ $video['id'] }}'].pause()
                            players['{{ $video['id'] }}'].currentTime(0);
                            players['{{ $video['id'] }}'].trigger('loadstart');
                        }
                    });
            @endforeach

            document.getElementById('csModal').addEventListener('shown.bs.modal',
                function() {
                    @foreach ($casestudies as $c)
                        if (!players['{{ $c['id'] }}']) {
                            players['{{ $c['id'] }}'] = videojs('video_player_{{ $c['id'] }}');
                        }
                        players['{{ $c['id'] }}'].playbackRate(1.25);
                    @endforeach
                });
            document.getElementById('csModal').addEventListener('hidden.bs.modal',
                function() {
                    @foreach ($casestudies as $c)
                        if (players['{{ $c['id'] }}']) {
                            players['{{ $c['id'] }}'].pause()
                            players['{{ $c['id'] }}'].currentTime(0);
                            players['{{ $c['id'] }}'].trigger('loadstart');
                        }
                    @endforeach
                });

            let currentIndex = 1;
            let totalSections = 8;

            function showSection(index) {
                document.querySelectorAll('.csModal-modal-content').forEach(div => div.style.display = 'none');
                document.getElementById(`modal-content_cs_${index}`).style.display = 'block';
                document.getElementById('counter').innerText = `${index} of ${totalSections}`;
            }

            // Handle slider icon click
            document.querySelectorAll('.cs_video').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentIndex = parseInt(this.getAttribute('data-id'));
                    showSection(currentIndex);
                    new bootstrap.Modal(document.getElementById('csModal')).show();
                });
            });

            // Prev Button
            document.getElementById('prevBtn').addEventListener('click', function() {
                currentIndex = (currentIndex === 1) ? totalSections : currentIndex - 1;
                showSection(currentIndex);
                @foreach ($casestudies as $c)
                    if (players['{{ $c['id'] }}']) {
                        players['{{ $c['id'] }}'].pause();
                    }
                @endforeach
            });

            // Next Button
            document.getElementById('nextBtn').addEventListener('click', function() {
                currentIndex = (currentIndex === totalSections) ? 1 : currentIndex + 1;
                showSection(currentIndex);
                @foreach ($casestudies as $c)
                    if (players['{{ $c['id'] }}']) {
                        players['{{ $c['id'] }}'].pause();
                    }
                @endforeach
            });
        });
    </script>
@endsection
