<?php 
if(request()->path()!='build-comparision-pages') {
?>
<div class="banner-two-area">
	
	<div class="banner-three__container">
		<div class="row align-items-center">
			<div class="col-lg-7 order-2 order-lg-1">
				<div class="banner-three__content pt-0 pb-0">
					<h4 class="wow fadeInUp text-white mb-20" data-wow-delay="00ms" data-wow-duration="1500ms">
						{{ $data->content->sub_heading->value }}
					</h4>
					<h1 class="wow fadeInUp text-white" data-wow-delay="200ms" data-wow-duration="1500ms">
						{{ $data->content->heading->value }}
					</h1>
					<p class="wow fadeInUp mt-20" data-wow-delay="400ms" data-wow-duration="1500ms">
						{{ $data->content->content->value }}
					</p>
					@if($data->content->button_title->value!="" && $data->content->button_title->value!="test")
					<div class="banner-three__info wow fadeInUp mt-50" data-wow-delay="600ms"
					     data-wow-duration="1500ms">
						<a class="wow fadeInUp btn-one mt-0" data-delay="1s" href="{{ $data->content->button_link->value }}">{{ $data->content->button_title->value }} <i class="fa-regular fa-arrow-right-long"></i></a>
						<div class="banner-three__video-btn d-flex gap-4 align-items-center">
							<div class="video-btn video-pulse">
								<a class="video-popup" href="{{ $data->content->video_button_link->value }}"><i
										class="fa-solid fa-play"></i></a>
							</div>
							<h5 class="text-white">{{ $data->content->video_button_title->value }}</h5>
						</div>
					</div>
					@endif
				</div>
			</div>
			<div class="col-lg-5 order-1 order-lg-2 wow fadeInLeft" data-wow-delay="200ms"
			     data-wow-duration="1500ms">
				<!-- Calendly inline widget begin -->
<div class="calendly-inline-widget" data-url="https://calendly.com/connect-tenetquad/30min" style="min-width:320px;height:700px;"></div>
<script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
<!-- Calendly inline widget end -->
			</div>
		</div>
	</div>
</div>

<?php

} else { ?>
<div class="banner-two-area">
 <div class="container-fluid container-padding mt-100">	
	<div class="row mb-4">
            <div class="col-md-6 banner-left-div">
                <h2 class="mb-2">Build <span class="bolder">Comparison</span> Pages</h2>
                <p>We build high-intent comparison pages that target "vs" and "alternative" keywords — designed to rank and convert users already evaluating options.</p>
				<ul class="list-unstyled mt-3">
                    <li class="d-inline-block">Keyword + competitor research</li>
                    <li>SEO-optimized copy</li>
                    <li>Conversion-first design</li>
                    <li>Ready-to-deploy HTML/CSS</li>
                </ul>
                 <a class="btn common-btn mt-5 mb-2" target="_blank" href="https://calendly.com/saurav-buildsaaslinks/30min">Book a meeting</a>
            </div>
            <div class="col-md-6 mt-5 mt-sm-0">
                <div class="border rounded p-2 h-100">
                    <video id="video_player_banner" class="video-js vjs-default-skin w-100" controls preload="auto"
                    width="100%" height="360" data-setup='{"playbackRates": [0.5, 1, 1.25, 1.5, 2],"preferFullWindow":true,"responsive":true}'>
                    <source src="{{ asset('frontend/videos/lekha-comparison.mp4') }}" type="video/mp4" />
                    Your browser does not support the video tag.
                </video>
                </div>
            </div>
        </div>
</div>
</div>
<!-- Paste inside buildsaaslinks.com page between header and footer -->
<style>
  .bsl-wrap * { box-sizing: border-box; }
  .bsl-wrap {
    font-family: 'Montserrat', sans-serif;
    color: #fff;
    padding: 80px 20px;
  }
  .bsl-container { max-width: 1200px; margin: 0 auto; }
  .bsl-h2 {
    font-size: clamp(28px, 4vw, 44px);
    font-weight: 700;
    text-align: center;
    margin: 0 0 16px;
    line-height: 1.2;
  }
  .bsl-accent { color: #b9342b; }
  .bsl-sub {
    text-align: center;
    color: #b4a9d6;
    max-width: 640px;
    margin: 0 auto 48px;
    font-size: 16px;
    line-height: 1.6;
  }

  /* Carousel */
  .bsl-carousel {
    position: relative;
    margin-bottom: 120px;
  }
  .bsl-swiper { overflow: hidden; padding-bottom: 40px; }
  .bsl-swiper .swiper-slide { height: auto; }
  .bsl-pagination { bottom: 0 !important; }
  .bsl-pagination .swiper-pagination-bullet { background: #b9342b; opacity: 0.4; }
  .bsl-pagination .swiper-pagination-bullet-active { opacity: 1; }
  .bsl-card {
    background: linear-gradient(180deg, rgba(30,20,60,0.6), rgba(15,10,35,0.8));
    border: 1px solid rgba(167,139,250,0.15);
    border-radius: 16px;
    padding: 20px;
    backdrop-filter: blur(10px);
  }
  .bsl-card img {
    width: 100%;
    border-radius: 10px;
    display: block;
    margin-bottom: 20px;
  }
  .bsl-badge {
    display: inline-block;
    background: rgba(167,139,250,0.15);
    color: #b9342b;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
  }
  .bsl-card h3 { font-size: 20px; margin: 0 0 10px; }
  .bsl-card p { color: #b4a9d6; font-size: 14px; line-height: 1.6; margin: 0 0 16px; }
  .bsl-card-meta {
    display: flex;
    gap: 16px;
    padding-top: 16px;
    border-top: 1px solid rgba(255,255,255,0.08);
    font-size: 12px;
    color: #8a80b0;
  }
  .bsl-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px; height: 40px;
    border-radius: 50%;
    background: rgba(167,139,250,0.1);
    border: 1px solid rgba(167,139,250,0.3);
    color: #fff;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
  }
  .bsl-arrow.prev { left: -60px; }
  .bsl-arrow.next { right: -60px; }

  /* What You Get */
  .bsl-steps {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 120px;
  }
  .bsl-step {
    background: rgba(30,20,60,0.4);
    border: 1px solid rgba(167,139,250,0.15);
    border-radius: 16px;
    padding: 28px 24px;
    text-align: center;
  }
  .bsl-step-num {
    font-size: 24px;
    color: #8a80b0;
    margin-bottom: 14px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }
  .bsl-step h4 { color: #b9342b; font-size: 16px; margin: 0 0 12px; font-weight: 600; }
  .bsl-step p { color: #b4a9d6; font-size: 13px; line-height: 1.6; margin: 0; }

  /* Packages */
  .bsl-pkgs {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    margin-bottom: 80px;
  }
  .bsl-pkg {
    background: linear-gradient(180deg, rgba(30,20,60,0.6), rgba(15,10,35,0.8));
    border: 1px solid rgba(167,139,250,0.2);
    border-radius: 20px;
    padding: 36px;
    position: relative;
  }
  .bsl-pkg-head { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
  .bsl-pkg-head h3 { font-size: 22px; margin: 0; }
  .bsl-popular {
    background: linear-gradient(90deg,#b9342b,#b9342b);
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
  }
  .bsl-pkg-sub { color: #b4a9d6; font-size: 14px; margin: 0 0 20px; }
  .bsl-price { font-size: 40px; font-weight: 700; margin-bottom: 28px; }
  .bsl-price span { font-size: 14px; color: #8a80b0; font-weight: 400; margin-left: 8px; }
  .bsl-features { list-style: none; padding: 0; margin: 0 0 28px; }
  .bsl-features li {
    color: #d5cce8;
    font-size: 14px;
    padding: 8px 0 8px 28px;
    position: relative;
  }
  .bsl-features li::before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #b9342b;
    font-weight: 700;
  }
  .bsl-btn {
    display: inline-block;
    background: linear-gradient(90deg,#b9342b,#b9342b);
    color: #fff;
    padding: 12px 28px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
  }
  .bsl-btn-ghost {
    background: transparent;
    border: 1px solid rgba(167,139,250,0.4);
  }
  .bsl-btn-ghost:hover {
    color: #b9342b;

  }

  /* CTA */
  .bsl-cta {
    background: linear-gradient(180deg, rgba(40,25,80,0.5), rgba(20,12,45,0.7));
    border: 1px solid rgba(167,139,250,0.2);
    border-radius: 20px;
    padding: 60px 40px;
    text-align: center;
  }
  .bsl-cta h2 { font-size: clamp(24px,3vw,36px); margin: 0 0 14px; }
  .bsl-cta p { color: #b4a9d6; max-width: 560px; margin: 0 auto 28px; line-height: 1.6; }

  @media (max-width: 768px) {
    .bsl-carousel, .bsl-steps, .bsl-pkgs { grid-template-columns: 1fr; }
    .bsl-arrow { display: none; }
	.banner-left-div h2 {
		font-size: 40px;
	}
	.banner-left-div span{
		font-size: 40px;
	}
	.bsl-swiper .swiper-slide .bsl-card { min-height: 350px !important; }
	.swiper-wrapper {height:auto !important;}
  }
</style>

<section class="bsl-wrap">
  <div class="bsl-container">

    <!-- Comparison showcase -->
    <h2 class="bsl-h2">Users searching <span class="bsl-accent">"[product] vs [competitor]"</span><br>are ready to choose</h2>
    <p class="bsl-sub">If you're not ranking here, you're losing high-intent traffic. Here's what winning comparison pages look like — these rank and convert.</p>

    <div class="bsl-carousel">
      <button class="bsl-arrow prev bsl-prev" aria-label="Previous">‹</button>

      <div class="swiper bsl-swiper">
        <div class="swiper-wrapper">

          <div class="swiper-slide">
            <div class="bsl-card">
              <img src="{{ asset('frontend/clients/slackvsteams.png') }}" alt="Slack vs Teams">
              <span class="bsl-badge">◆ RANKING</span>
              <h3>Slack vs Teams</h3>
              <p>Compare team communication platforms by messaging features, integrations, collaboration tools, pricing, and productivity.</p>
              <div class="bsl-card-meta">
                <span>Communication Tools</span>
                <span>• 29K+ monthly searches</span>
              </div>
            </div>
          </div>

          <div class="swiper-slide">
            <div class="bsl-card">
              <img src="{{ asset('frontend/clients/asana-vs-clickup.png') }}" alt="Asana vs ClickUp">
              <span class="bsl-badge">◆ RANKING</span>
              <h3>Asana vs ClickUp</h3>
              <p>Compare project management tools by task management, collaboration features, automation, integrations, and pricing.</p>
              <div class="bsl-card-meta">
                <span>Communication Tools</span>
                <span>• 29K+ monthly searches</span>
              </div>
            </div>
          </div>

          <div class="swiper-slide">
            <div class="bsl-card">
              <img src="{{ asset('frontend/clients/ahrefs-vs-semrush.png') }}" alt="Ahrefs vs Semrush">
              <span class="bsl-badge">◆ RANKING</span>
              <h3>Ahrefs vs Semrush</h3>
              <p>Compare SEO tools by keyword research, backlink analysis, site audits, competitor insights, and pricing.</p>
              <div class="bsl-card-meta">
                <span>SEO Tools</span>
                <span>• 25K+ monthly searches</span>
              </div>
            </div>
          </div>

          <div class="swiper-slide">
            <div class="bsl-card">
              <img src="{{ asset('frontend/clients/asana-vs-trello.png') }}" alt="Asana vs Trello">
              <span class="bsl-badge">◆ RANKING</span>
              <h3>Asana-vs-Trello</h3>
              <p>Compare project management tools by task management, collaboration features, integrations, automation, and pricing.</p>
              <div class="bsl-card-meta">
                <span>Project Management Tools</span>
                <span>• ~14K+ monthly searches</span>
              </div>
            </div>
          </div>

        </div>
        <div class="swiper-pagination bsl-pagination"></div>
      </div>

      <button class="bsl-arrow next bsl-next" aria-label="Next">›</button>
    </div>

    <script>
      (function initBslSwiper(){
        function ready(){
          if (typeof Swiper === 'undefined') { return setTimeout(ready, 100); }
          new Swiper('.bsl-swiper', {
            slidesPerView: 1,
            spaceBetween: 24,
            loop: true,
            /*autoplay: { delay: 4000, disableOnInteraction: false },*/
            breakpoints: { 768: { slidesPerView: 2 } },
            navigation: { nextEl: '.bsl-next', prevEl: '.bsl-prev' },
            pagination: { el: '.bsl-pagination', clickable: true }
          });
        }
        ready();
      })();
    </script>

    <!-- What You Get -->
    <h2 class="bsl-h2">What You <span class="bsl-accent">Get</span></h2>
    <div class="bsl-steps" style="margin-top:48px;">
      <div class="bsl-step">
        <div class="bsl-step-num">01</div>
        <h4>Competitor + keyword mapping</h4>
        <p>We identify which "vs" and "alternative" keywords have real buyer intent — not just search volume.</p>
      </div>
      <div class="bsl-step">
        <div class="bsl-step-num">02</div>
        <h4>SEO-focused copy</h4>
        <p>Written to rank and position your product clearly. Honest, structured, conversion-led.</p>
      </div>
      <div class="bsl-step">
        <div class="bsl-step-num">03</div>
        <h4>Conversion-first design</h4>
        <p>Comparison tables, feature highlights, and CTAs where decision-stage buyers need them.</p>
      </div>
      <div class="bsl-step">
        <div class="bsl-step-num">04</div>
        <h4>Ready-to-use front-end files</h4>
        <p>Clean HTML/CSS your dev team deploys directly. Any stack, no lock-in.</p>
      </div>
    </div>

    <!-- Packages -->
    <h2 class="bsl-h2">Packages</h2>
    <div class="bsl-pkgs" style="margin-top:48px;">
      <div class="bsl-pkg">
        <div class="bsl-pkg-head"><h3>Starter</h3></div>
        <p class="bsl-pkg-sub">Up to 5 comparison pages</p>
        <div class="bsl-price">$1,500<span>one-time</span></div>
        <ul class="bsl-features">
          <li>Keyword research for 5 competitors</li>
          <li>SEO copy per page</li>
          <li>Conversion-first design</li>
          <li>HTML/CSS delivery</li>
          <li>1-2 design iterations</li>
        </ul>
        <a target="_blank" href="https://calendly.com/saurav-buildsaaslinks/30min" class="bsl-btn bsl-btn-ghost">Book a meeting →</a>
      </div>

      <div class="bsl-pkg">
        <div class="bsl-pkg-head"><h3>Growth</h3><span class="bsl-popular">Most popular</span></div>
        <p class="bsl-pkg-sub">Up to 10 comparison pages</p>
        <div class="bsl-price">$2,750<span>one-time</span></div>
        <ul class="bsl-features">
          <li>Keyword research for 10 competitors</li>
          <li>SEO copy per page</li>
          <li>Conversion-first design</li>
          <li>HTML/CSS delivery</li>
          <li>1-2 design iterations</li>
        </ul>
        <a target="_blank" href="https://calendly.com/saurav-buildsaaslinks/30min" class="bsl-btn">Book a meeting →</a>
      </div>
    </div>

    <!-- CTA -->
    <div class="bsl-cta">
      <h2>Start capturing <span class="bsl-accent">competitor</span> traffic</h2>
      <p>Every day without a comparison page is bottom-funnel traffic going to G2, Reddit, or your competitor's site.</p>
      <a target="_blank" href="https://calendly.com/saurav-buildsaaslinks/30min" class="bsl-btn">Book a meeting →</a>
    </div>

  </div>
</section>

<?php } ?>