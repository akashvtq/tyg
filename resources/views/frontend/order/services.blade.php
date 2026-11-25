@extends('frontend.page.index') @section('title', __('Order / Services'))
@section('page_content') @include('frontend.page.partial._breadcrumb')

<section class="service-single-area pt-120 pb-120">
    <div class="container">
        <form class="card" id="form-order" method="POST" action="{{ route('user.service.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="progress d-none" id="progress-bar">
                <div class="progress-bar progress-bar-indeterminate bg-green"></div>
            </div>
            <div class="row g-4 m-4">
                <div class="col-md-8">
                    <div class="card p-4 mb-4">
                        <h4 class="text-dark">Services</h4>
                        <div id="services-list">
                            <!-- Static services -->
                            @forelse($order_summary as $key => $s)
                            <div
                                class="row align-items-center border-bottom py-2 service-item"
                                data-id="{!! $key !!}"
                                data-price="{!! $s['price'] !!}">
                                <span class="col-md-6 text-dark">@if(in_array($key, \Database\Seeders\TygSeoServicesSeeder::SLUGS))
										<b>SEO Services</b>
									@elseif($s['price']>300)
										<b>Branded SAAS Links</b>
									@else
									<b>SAAS Links</b>
									@endif
									<br/>
									{{ $s["name"] }}</span>
                                <div class="col-md-2 qty-box" id="qty-{!! $key !!}">
                                    <button class="btn btn-sm btn-dark minus-btn" type="button">
                                        -
                                    </button>
                                    <input
                                        type="text"
                                        class="form-control form-control-sm qty-input"
                                        value="{!! $s['value'] !!}"
                                        readonly />
                                    <button class="btn btn-sm btn-dark plus-btn" type="button">
                                        +
                                    </button>
                                </div>
                                <span class="col-md-4 text-dark text-end">{!! setting('currency_symbol') ." ".$s['price']
                                    !!} each</span>
                            </div>
                            @empty
                            <div>No services found</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Dynamic Project Inputs -->
                    <div id="project-sections">
                        @foreach($order_summary as $key => $s)
                        @if(count($s['projects']))
                        <div class="card p-3 mt-3" data-id="{!! $key !!}">
                            <h5 class="text-dark mb-2">{!! $s['name'] !!}</h5>
                            <div class="project-list">
                                @forelse($s['projects'] as $p)
                                <div class="project-entry" id="{!! $p['project_id'] !!}">
                                    <div class="mb-2 fw-bold">{!! $p['name'] !!}</div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="anchor-text">Suggested Anchor Text (Not
                                                Guaranteed)</label>
                                            <input
                                                type="text"
                                                class="form-control form-control-sm project_anchor_text"
                                                value="{!! $p['anchor_text'] !!}" data-service="{!! $key !!}" />
                                        </div>
                                        <div class="col-md-5">
                                            <label class="anchor-text">Target URL *</label>
                                            <input
                                                type="text"
                                                class="form-control form-control-sm project_target_url"
                                                value="{!! $p['target_url'] !!}" data-service="{!! $key !!}" />
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button class="btn btn-sm btn-dark duplicate-btn" data-slug="{!! $key !!}" type="button">+</button>
                                            <span class="delete-entry" title="Delete Row">🗑️</span>
                                        </div>
                                    </div>
                                </div>
                                @empty @endforelse
                            </div>
                            <button type="button" class="btn btn-dark w-100" onclick="$('#qty-{!! $key !!}').find('.plus-btn').click();">
                                Add Row
                            </button>
                        </div>
                        @endif
                        @endforeach
                    </div>

                    <!-- Notes -->
                    <div class="card p-4 mt-4">
                        <h6 class="text-dark mb-2">Order Notes</h6>
                        <textarea
                            id="order_notes"
                            class="form-control"
                            maxlength="2500"
                            rows="4"
                            placeholder="Please note: special order requests are not guaranteed."></textarea>
                    </div>
                </div>

                <!-- Price Summary -->
                <div class="col-md-4">
                    <!-- <form class="card" method="POST" action="{{ route('user.service.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="progress d-none" id="progress-bar">
                        <div class="progress-bar progress-bar-indeterminate bg-green"></div>
                    </div> -->
                    <div class="card-body">
                        <h3 class="card-title theme-text-color mb-4">{{ __('Payment Information') }}</h3>
                        <div class="row g-4">
                            {{-- Deposit Method Selection --}}
                            <div class="col-md-12" id="payment_information">
                                <div class="mb-3">
                                    <label class="form-label" for="deposit-method-select">{{ __('Payment Method') }}</label>
                                    <select class="form-select" name="deposit_method" id="deposit-method-select" aria-label="{{ __('Payment Method') }}" required aria-describedby="deposit-method-hint">
                                        <option value="0" disabled selected>{{ __('Select Payment Method') }}</option>
                                        <option value="user_balance">{{ __('User Balance:') }} {{ \App\Facades\BalanceFacade::get(Auth::user()) }} {{ setting('site_currency') }}</option>
                                        @foreach($depositMethods as $depositMethod)
                                        <option value="{{ $depositMethod->method_code }}" {{ old('payment_method') == $depositMethod->id ? 'selected' : '' }}>{{ $depositMethod->name }}</option>
                                        @endforeach
                                    </select>
                                    <small id="deposit-method-hint" class="form-hint text-muted">{{ __('Choose your preferred method of payment.') }}</small>
                                    <small class="form-hint text-danger" id="charge"></small>
                                </div>
                            </div>

                            {{-- Amount Input --}}
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="deposit-amount">{{ __('Amount') }}</label>
                                    <div class="input-group">
                                        <input type="text" name="amount" value="" id="deposit-amount" disabled class="form-control" aria-describedby="amount-hint" autocomplete="off">
                                        <span class="input-group-text">{{ setting('site_currency') }}</span>
                                    </div>
                                    <small id="amount-hint" class="form-hint text-muted">{{ __('This amount will be deducted from your account') }}</small>
                                    <small class="form-hint text-danger" id="amount-limit"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row g-1" id="manual-deposit-info">

                        </div>

                        <div class="row g-4" id="manual-deposit-row">

                        </div>
                    </div>
                    <!-- </form> -->
                    <div
                        class="price-summary card p-3 mb-4"
                        style="top: 20px">
                        <!-- <h5>Net Total</h5>
                    <p>£<span id="net-total">0</span>.00</p>
                    <p class="text-muted">
                        Approx USD: $<span id="usd-total">0</span>.00
                    </p> -->
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title theme-text-color mb-4">{{ __('Review Details:') }}</h5>

                                    <div class="review-section mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">{{ __('Payment Method') }}:</span>
                                            <strong class="payment-method text-end"></strong>
                                        </div>
                                    </div>

                                    <div class="review-section mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">{{ __('Amount') }}:</span>
                                            <strong class="amount text-end"></strong>
                                        </div>
                                    </div>

                                    <div class="review-section mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">{{ __('Charge') }}:</span>
                                            <strong class="charge text-end"></strong>
                                        </div>
                                    </div>

                                    <div class="review-section mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">{{ __('Total') }}:</span>
                                            <strong class="total text-end text-success"></strong>
                                        </div>
                                    </div>

                                    <hr class="my-3 dashed">

                                    <div class="conversion-section mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">{{ __('Conversion Rate') }}:</span>
                                            <strong class="conversion-rate text-end"></strong>
                                        </div>
                                    </div>

                                    <div class="conversion-section mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">{{ __('Pay Amount') }}:</span>
                                            <strong class="pay-amount text-end text-primary"></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <hr />
                        <button type="button" class="btn-one common-btn text-white" id="process_to_pay"> <x-svg i="right-arrow" /> {{ __('Process To
                            Pay')
                            }}</button>
                        <!-- <button class="btn btn-dark w-100">
                        Continue to Inputs
                    </button> -->
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
@push('user-script')
@include('frontend.user.service.partial._script')
@include('frontend.order.updatedata')
@endpush