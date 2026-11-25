<style>
h1.aboutheader {
	font-size:50px;
    font-weight: bold;
    margin: auto 20%;
    line-height: 120%;
}
.aboutheaderpara {padding-top:30px;margin: auto 10%;color: var(--theme-light-color);}
.bolder {
      color: #b9342b;
    }
	
.mission-vision {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      margin: 60px auto;
      max-width: 1200px;
      padding: 0 20px;
    }
    .cardmvparent {
      flex: 1 1 300px;
      background: rgba(255,255,255,0.05);
      border-radius: 12px;
      padding: 30px;
      text-align: center;
    }
	.cardmv {
      flex: 1 1 300px;
      border: 1px solid var(--theme-light-color);
      border-radius: 12px;
      padding: 30px;
      text-align: center;
	  background: linear-gradient(0deg, #211f2800, transparent) 0%, linear-gradient(0deg, #211f2833, transparent) 17%;
    }
    .cardmv h2 {
      font-size: 1.5rem;
      margin-bottom: 15px;
      color: #FFF;
    }
	.cardmv h2 span {
      
      color: #b9342b;
    }
    .cardmv p {
      color: #ddd;
      font-size: 0.95rem;
	  min-height: 170px;
    }
.missionvision-icon {
    width: 56px;
    height: 56px;
    line-height: 56px;
    text-align: center;
    border-radius: 50%;
    transition: var(--transition);
    border: 1px solid #211f28;
	margin:0px auto 15px auto;
}

.missionvision-icon > .fa {
	font-size: 24px;
    padding: 15px;
}

 /* Founder story */
    .story {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      max-width: 1000px;
      margin: 80px auto;
      gap: 40px;
      padding: 0 20px; 
	  
	  
    }
    .story img {
      width: 320px;
      border-radius: 12px;
    }
    .story-content {
      flex: 1;
	  border: 1px solid var(--theme-light-color);
      border-radius: 12px;
	   padding: 30px 50px;
	  background: linear-gradient(0deg, #211f2800, transparent) 0%, linear-gradient(0deg, #211f2833, transparent) 17%;
    }
    .story-content h2 {
      margin-bottom: 15px;
    }
    .story-content p {
      color: #fff;
    }
	
	
	/* What we offer */
    .offers {
		  max-width: 1000px;
		  margin: 80px auto;
		  padding: 0 20px;
		  text-align: center;
		}

		.offer-wrapper {
		  display: flex;
		  flex-wrap: wrap;
		  justify-content: center; /* centers items */
		  gap: 20px;
		}

		.offer-item {
		  flex: 0 0 33%;   /* each item takes 33% width */
		  max-width: 300px; /* prevents being too wide on large screens */
		  background: rgba(255,255,255,0.05);
		 
		  padding: 20px;
		  text-align: center;
		  color: #ccc;
		  border: 1px solid var(--theme-light-color);
			border-radius: 12px;
			
			background: linear-gradient(0deg, #211f2800, transparent) 0%, linear-gradient(0deg, #211f2833, transparent) 17%;
		}
		.offer-item h4 {
		  color: #fff;
		  margin-bottom: 10px;
		}
.why-us {
      max-width: 1000px;
      margin: 80px auto;
      padding: 0 20px;
    }
    .why-us h2 {
      text-align: left;
      margin-bottom: 30px;
    }
    .why-us ul {
      list-style: none;
      padding: 0;
      max-width: 700px;
      margin: 0 auto;
    }
    .why-us li {
      margin-bottom: 15px;
      padding-left: 28px;
      position: relative;
      color: #ccc;
    }
    .why-us li::before {
      content: "✔";
      position: absolute;
      left: 0;
      color: #b9342b;
    }
	.why-us li span{
      margin-bottom: 15px;
      position: relative;
      color: var(--theme-light-color);
    }
	.whyussection img {    width: 90%;
    float: right;}
	
	/* Founder */
    .founder {
      text-align: center;
      padding: 0 20px;
    }
    .founder img {
      width: 200px;
      border-radius: 50%;
      border: 3px solid #b9342b;
    }
    .founder h2 {
		text-align: center;
      margin-bottom: 15px;
    }
    .founder p {
		text-align: left;
      max-width: 500px;
      margin: 10px;
      color: #ccc;
    }
	
	/* getstarted */
    .getstarted {
      text-align: left;
      padding: 0 20px;
    }
    .getstarted img {
      width: 100%;
    }
    .getstarted h2 {
		
      margin-bottom: 15px;
    }
    .getstarted p {
		
      max-width: 500px;
      margin: 10px;
      color: #ccc;
    }
	
@media (max-width: 575px) {	
	h1.aboutheader {
		font-size: 40px;
    margin: auto 5%;
		
	}
	.offer-item {
    flex: 0 0 45%;
	padding: 15px;
	}
	.why-us {
    margin: 0px auto;
	}
	.getstarted {
    text-align: center;
	}
	
}
</style>
<section class="about-three-area pt-80">	
	
	<div class="container">
		<div class="row g-4 align-items-center">
			<div class="">
				<div class="text-center">
				 <h1 class="aboutheader">Build SaaS Links - No <span class="bolder">"Hello Deer"</span> Link Types</h1>
					<p class="aboutheaderpara">
					  Founded in 2025, Build SaaS Links is built on a simple belief: SaaS companies deserve backlinks 
					  that actually make sense – not random “hello deer” link types placed on irrelevant sites.
					</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="about-three-area pt-80">	
	
	<div class="container">
		<div class="row g-4 align-items-center">
			<div class="mission-vision">
				<div class="cardmvparent wow fadeInLeft">
					<div class="cardmv">
					  <h2>Our <span>Mission</span></h2>
					  <div class="missionvision-icon"><i class="fa fa-eye" aria-hidden="true"></i></div>
					  <p>We help SaaS companies get the best possible links on the internet without overspending. From prospecting ideal sites to identifying page-level placement and adding contextual content, every step is done with your approval.</p>
					</div>
				</div>
				<div class="cardmvparent wow fadeInRight">
					<div class="cardmv">
					  <h2>Our <span>Vision</span></h2>
					  <div class="missionvision-icon"><i class="fa fa-bullseye" aria-hidden="true"></i></div>
					  <p>Link building doesn’t have to be shady. Too many agencies suggest sites without understanding your product, use case, or the importance of context. We’re changing that by making link building responsible, transparent, and trustworthy – even to the point of offering refunds when a link fails.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="about-three-area pt-80">	
	
	<div class="container">
		<div class="row g-4 align-items-center">
			<div class="story">
				<img src="{{ asset($data->content->main_image->value) }}" alt="Founder">
				<div class="story-content">
				  <h2 class="wow fadeInUp" data-wow-delay="200ms" data-wow-duration="1500ms">{{ $data->content->section_heading->value }}</h2>
				  <p class="wow fadeInUp" data-wow-delay="400ms" data-wow-duration="1500ms">{{ $data->content->section_description->value }}</p>
				</div>
			  </div>
		</div>
	</div>
</section>



<section class="about-three-area pt-80">	
	
	<div class="container">
		<div class="row g-4 align-items-center">
		<h2 class="fw-bold mb-5 text-center">Along the way, we’ve worked with <span class="bolder">clients like</span></h2>
			<div class="gradient-border logo-banner">
				
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
		</div>
	</div>
</section>

<section class="about-three-area pt-80">	
	
	<div class="container">
		<div class="row g-4 align-items-center">
			<div class="offers">
			  <h2 class="fw-bold mb-5 text-center">What We <span class="bolder">Offer?</span></h2>
			  <div class="offer-wrapper">
				<div class="offer-item">
				  <h4>Branded links</h4>
				  <p>To strengthen authority</p>
				</div>
				<div class="offer-item">
				  <h4>Refund-backed guarantee</h4>
				  <p>If we can’t restore or replace a link within 1 year, you get 95% back</p>
				</div>
				<div class="offer-item">
				  <h4>À-la-carte model</h4>
				  <p>Pay only per link, no forced packages</p>
				</div>
				<div class="offer-item">
				  <h4>Landing page links</h4>
				  <p>To drive growth where you need it most</p>
				</div>
				<div class="offer-item">
				  <h4>Risk-free first link</h4>
				  <p>Test us before committing</p>
				</div>
			  </div>
			</div>		
		</div>
	</div>
</section>


<section class="about-three-area whyussection pt-80 ">	
	
	<div class="container">
		<div class="row g-4 align-items-center">
			<div class="col-lg-6 order-1 order-lg-1 d-none d-md-block">
				<img class="sway_Y__animationY" src="{{ asset($data->content->box_up_shape->value) }}" alt="shape">
			</div>
			<div class="col-lg-6 order-2 order-lg-1 wow slideInRight" data-wow-delay="00ms" data-wow-duration="1500ms">
				<div class="why-us">
					<h2>Why <span class="bolder">Choose Us?</span></h2>
					<ul>
					  <li>Approval before ordering <br><span>You always know where your link is going</span></li>
					  <li>No guest post farms <br><span>Only real, context-driven placements</span></li>
					  <li>Transparency at every step <br><span>No hidden packages, no forced commitments</span></li>
					  <li>Experience that counts <br><span>5+ years in SEO, 3+ years in SaaS link building, and over 3,000 SaaS links built</span></li>
					</ul>
				</div>
			</div>		
		
		
		</div>
	</div>
</section>

<section class="about-three-area pt-80">	
	
	<div class="container">
	<div class="founder">
	<h2>Meet the <span class="bolder">Founder</span></h2>
		<div class="row g-4 align-items-center">
				  
					<div class="col-lg-4 order-1 order-lg-1">
						<img src="{{ asset($data->content->section_title_icon->value) }}" alt="Saurav Agarwal">
						<h3>Saurav Agarwal</h3>
					</div>
					<div class="col-lg-8 order-2 order-lg-2">
						<p>I’m Saurav Agarwal, the person behind Build SaaS Links. After 5+ years in SEO and specializing 3 years in SaaS link building, I’ve placed over 3,000+ high-quality SaaS links.</p>
						<p><strong>Philosophy:</strong> I force myself to build only good links. No shortcuts, no guest post farms, no temptation to chase easy wins that don’t last.</p>
					</div>
					
					
				  </div>	
		</div>
	</div>
</section>


<section class="about-three-area pt-80">	
	
	<div class="container">
	<div class="getstarted">
	
		<div class="row g-4 align-items-center">
				  
					<div class="col-lg-8 order-1 order-lg-1">
						<img src="{{ asset('general/images/getstartedimage.png') }}" alt="Get Started">
					</div>
					<div class="col-lg-4 order-2 order-lg-2">
					<h2>Ready to Get Started?</h2>
						<p>Your SaaS deserves links that are strategic, contextual, and built to last.</p>
						<a href="https://buildsaaslinks.com/login" class="btn-one common-btn">Order your links today</a>
					</div>
					
					
				  </div>	
		</div>
	</div>
</section>
