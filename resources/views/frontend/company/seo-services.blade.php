@extends('frontend.layouts.app')
@section('title')
    {{ __('Home') }}
@endsection
@push('meta')
<meta name="title" content="{{ $metaTitle }}">
<meta name="description" content="{{ $metaDescription }}">
@endpush

@section('content')

  <div class="container-fluid">
    <style>
      *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #FFFFFF;
    --bg-section: #F5F5F7;
    --card: #FFFFFF;
    --accent: #b9342b;
    --accent-light: #8e271f;
    --cta: #b9342b;
    --text: #111111;
    --muted: #555555;
    --highlight: #8e271f;
    --border: #E6E3F0;
    --font-head: 'Kumbh Sans', sans-serif;
    --font-body: 'Montserrat', sans-serif;
  }

  html { scroll-behavior: smooth; }

  body {
    background: var(--bg);
    color: var(--text);
    font-family: var(--font-body);
    font-size: 16px;
    line-height: 1.7;
    overflow-x: hidden;
  }
      /* HERO */
      .hero {
        min-height: 100vh;
        display: flex; align-items: center; justify-content: center;
        flex-direction: column;
        text-align: center;
        padding: 120px 40px 80px;
        position: relative;
        overflow: hidden;
      }

      .hero::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(ellipse 70% 60% at 50% 40%, rgba(124,92,252,0.18) 0%, transparent 70%);
        pointer-events: none;
      }

      .hero-grid-lines {
        position: absolute; inset: 0;
        background-image:
          linear-gradient(rgba(132,99,221,0.08) 1px, transparent 1px),
          linear-gradient(90deg, rgba(132,99,221,0.08) 1px, transparent 1px);
        background-size: 60px 60px;
        mask-image: radial-gradient(ellipse 80% 60% at 50% 50%, black 30%, transparent 75%);
        pointer-events: none;
      }

      .badge {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(124,92,252,0.15);
        border: 1px solid rgba(124,92,252,0.3);
        color: var(--accent-light);
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        padding: 6px 16px;
        border-radius: 100px;
        margin-bottom: 28px;
        animation: fadeUp 0.6s ease both;
      }

      .badge::before {
        content: '';
        width: 6px; height: 6px;
        background: var(--accent);
        border-radius: 50%;
        animation: pulse 2s ease-in-out infinite;
      }

      @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.7); }
      }

      @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
      }

      h1 {
        font-family: var(--font-head);
        font-size: clamp(42px, 7vw, 82px);
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -2px;
        max-width: 900px;
        animation: fadeUp 0.7s 0.1s ease both;
      }

      h1 em {
        font-style: normal;
        color: var(--accent-light);
        position: relative;
      }

      .hero p {
        font-size: 18px;
        color: var(--muted);
        max-width: 560px;
        margin: 24px auto 40px;
        font-weight: 300;
        animation: fadeUp 0.7s 0.2s ease both;
      }

      .hero-btns {
        display: flex; gap: 14px; flex-wrap: wrap; justify-content: center;
        animation: fadeUp 0.7s 0.3s ease both;
      }

      .btn-primary {
        background: var(--cta);
        color: #fff;
        padding: 14px 30px;
        border-radius: 50px;
        font-size: 15px;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.2s, transform 0.15s;
      }
      .btn-primary:hover { background: var(--accent-light); color: #fff; transform: translateY(-2px); }

      .btn-ghost {
        background: transparent;
        color: var(--text);
        border: 1px solid var(--border);
        padding: 14px 30px;
        border-radius: 50px;
        font-size: 15px;
        font-weight: 500;
        text-decoration: none;
        transition: border-color 0.2s, transform 0.15s;
      }
      .btn-ghost:hover { border-color: var(--accent); color: var(--accent-light); transform: translateY(-2px); }

      .hero-stats {
        display: flex; gap: 48px; margin-top: 72px; justify-content: center; flex-wrap: wrap;
        animation: fadeUp 0.7s 0.4s ease both;
      }

      .stat-item { text-align: center; }
      .stat-num {
        font-family: var(--font-head);
        font-size: 36px;
        font-weight: 800;
        color: var(--text);
      }
      .stat-num span { color: var(--accent-light); }
      .stat-label { font-size: 13px; color: var(--muted); margin-top: 4px; }

      /* SECTIONS */
      section { padding: 96px 64px; }

      .section-alt { background: var(--bg-section); }

      .section-header {
        text-align: center;
        max-width: 640px;
        margin: 0 auto 64px;
      }

      .section-tag {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--accent-light);
        margin-bottom: 14px;
      }

      h2 {
        font-family: var(--font-head);
        font-size: clamp(32px, 4vw, 48px);
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -1px;
      }

      h2 span { color: var(--accent-light); }

      .section-sub {
        font-size: 16px;
        color: var(--muted);
        margin-top: 14px;
        font-weight: 300;
      }

      /* SERVICES GRID */
      .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        max-width: 1100px;
        margin: 0 auto;
      }

      .service-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 36px 32px;
        position: relative;
        overflow: hidden;
        transition: border-color 0.25s, transform 0.2s;
      }

      .service-card:hover {
        border-color: rgba(124,92,252,0.5);
        transform: translateY(-4px);
      }

      .service-card::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, var(--accent), var(--accent-light));
        opacity: 0;
        transition: opacity 0.25s;
      }

      .service-card:hover::before { opacity: 1; }

      .service-icon {
        width: 48px; height: 48px;
        background: rgba(124,92,252);
        border: 1px solid rgba(124,92,252);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 20px;
        font-size: 22px;
      }

      .service-card h3 {
        font-family: var(--font-head);
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 10px;
        letter-spacing: -0.3px;
      }

      .service-card p {
        font-size: 14px;
        color: var(--muted);
        line-height: 1.65;
      }

      .service-tags {
        display: flex; gap: 8px; flex-wrap: wrap; margin-top: 20px;
      }

      .tag {
        font-size: 11px;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 100px;
        background: rgba(124,92,252,0.1);
        color: var(--accent-light);
        border: 1px solid rgba(124,92,252,0.2);
      }

      /* COMPARISON TABLE */
      .table-wrap {
        max-width: 900px;
        margin: 0 auto;
        overflow-x: auto;
      }

      table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
      }

      table thead tr {
        border-bottom: 1px solid var(--border);
      }

      table th {
        font-family: var(--font-head);
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 16px 20px;
        text-align: left;
        color: var(--muted);
      }

      table th:nth-child(3) {
        color: var(--accent-light);
        background: rgba(124,92,252,0.07);
        border-radius: 8px 8px 0 0;
      }

      table td {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        color: var(--text);
        vertical-align: top;
      }

      table tr:last-child td { border-bottom: none; }

      table td:first-child { color: var(--muted); font-size: 14px; }

      table td:nth-child(2) { color: var(--muted); }

      table td:nth-child(3) {
        background: rgba(124,92,252,0.05);
        color: var(--accent-light);
        font-weight: 500;
      }

      .check { color: #1A9E55; font-size: 16px; }
      .cross { color: #D93838; font-size: 16px; }

      /* PROCESS */
      .process-wrap {
        max-width: 1100px;
        margin: 0 auto;
        position: relative;
      }

      .process-line {
        position: absolute;
        top: 36px; left: 72px; right: 72px;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--border), var(--accent), var(--border), transparent);
        z-index: 0;
      }

      .process-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        position: relative; z-index: 1;
      }

      .process-step {
        text-align: center;
        padding: 32px 20px;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        transition: border-color 0.25s, transform 0.2s;
        position: relative;
      }

      .process-step:hover {
        border-color: rgba(124,92,252,0.5);
        transform: translateY(-4px);
      }

      .step-num {
        font-family: var(--font-head);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 2px;
        color: var(--accent);
        text-transform: uppercase;
        margin-bottom: 14px;
      }

      .step-icon {
        width: 64px; height: 64px;
        background: rgba(124,92,252);
        border: 1px solid rgba(124,92,252);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px;
        font-size: 28px;
      }

      .process-step h3 {
        font-family: var(--font-head);
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 8px;
        letter-spacing: -0.2px;
      }

      .process-step p {
        font-size: 13px;
        color: var(--muted);
        line-height: 1.6;
      }

      .step-arrow {
        position: absolute;
        top: 50%; right: -14px;
        transform: translateY(-50%);
        width: 26px; height: 26px;
        background: var(--bg-section);
        border: 1px solid var(--border);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px;
        color: var(--accent-light);
        z-index: 2;
      }

      /* PRICING */
      .pricing-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 28px;
        max-width: 820px;
        margin: 0 auto;
      }

      .pricing-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 44px 40px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s;
      }

      .pricing-card:hover { transform: translateY(-4px); }

      .pricing-card.featured {
        border-color: var(--accent);
        background: linear-gradient(145deg, rgba(124,92,252,0.08) 0%, var(--card) 50%);
      }

      .pricing-card.featured::after {
        content: 'MOST POPULAR';
        position: absolute;
        top: 20px; right: -28px;
        background: var(--accent);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1.5px;
        padding: 5px 40px;
        transform: rotate(45deg);
      }

      .pricing-name {
        font-family: var(--font-head);
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 16px;
      }

      .pricing-price {
        font-family: var(--font-head);
        font-size: 56px;
        font-weight: 800;
        letter-spacing: -2px;
        line-height: 1;
        margin-bottom: 6px;
      }

      .pricing-price sup {
        font-size: 24px;
        font-weight: 600;
        vertical-align: super;
        letter-spacing: 0;
      }

      .pricing-sub {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 32px;
      }

      .pricing-divider {
        height: 1px;
        background: var(--border);
        margin: 28px 0;
      }

      .pricing-features { list-style: none; }

      .pricing-features li {
        font-size: 14px;
        color: var(--muted);
        padding: 9px 0;
        display: flex; align-items: flex-start; gap: 10px;
        border-bottom: 1px solid var(--border);
      }

      .pricing-features li:last-child { border-bottom: none; }

      .pricing-features li::before {
        content: '✓';
        color: var(--accent-light);
        font-weight: 600;
        flex-shrink: 0;
        margin-top: 1px;
      }

      .pricing-btn {
        display: block;
        width: 100%;
        text-align: center;
        padding: 14px;
        border-radius: 50px;
        font-size: 15px;
        font-weight: 500;
        text-decoration: none;
        margin-top: 32px;
        transition: all 0.2s;
        font-family: var(--font-body);
      }

      .pricing-card:not(.featured) .pricing-btn {
        background: transparent;
        border: 1px solid var(--accent);
        color: var(--accent-light);
      }

      .pricing-card:not(.featured) .pricing-btn:hover {
        background: rgba(124,92,252,0.1);
      }

      .pricing-card.featured .pricing-btn {
        background: var(--cta);
        color: #fff;
        border: none;
      }

      .pricing-card.featured .pricing-btn:hover {
        background: var(--accent-light);
      }

      /* CTA SECTION */
      .cta-section {
        background: var(--bg-section);
        padding: 96px 64px;
        text-align: center;
        position: relative;
        overflow: hidden;
      }

      .cta-section::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(ellipse 60% 80% at 50% 50%, rgba(124,92,252,0.12) 0%, transparent 65%);
        pointer-events: none;
      }

      .cta-section h2 { max-width: 600px; margin: 0 auto 16px; }

      .cta-section p {
        color: var(--muted);
        max-width: 480px;
        margin: 0 auto 40px;
        font-weight: 300;
      }
      @media (max-width: 768px) {
        section { padding: 72px 24px; }
        .process-grid { grid-template-columns: 1fr; }
        .process-line { display: none; }
        .pricing-grid { grid-template-columns: 1fr; }
        .step-arrow { display: none; }
      }
      

      @media only screen and (min-width: 300px) and (max-width: 767px) {
          .proof-performance table, .proof-performance tbody tr, td {
              display: revert;
          }
      }
    </style>

    <!-- HERO -->
    <section class="hero">
      <div class="hero-grid-lines"></div>
      <span class="badge">Programmatic SEO</span>
      <h1>Pages at Scale.<br><em>Rankings at Speed.</em></h1>
      <p>We build programmatic SEO systems for SaaS companies — thousands of targeted pages, automatically generated and optimized to capture long-tail demand.</p>
      <div class="hero-btns">
        <a href="#services" class="btn-primary">Explore Services</a>
        <a href="#pricing" class="btn-ghost">View Pricing</a>
      </div>
      <div class="hero-stats">
        <div class="stat-item">
          <div class="stat-num">1K<span>+</span></div>
          <div class="stat-label">Pages generated per project</div>
        </div>
        <div class="stat-item">
          <div class="stat-num">4<span>x</span></div>
          <div class="stat-label">Avg. organic traffic lift</div>
        </div>
        <div class="stat-item">
          <div class="stat-num">&#60;5<span>$</span></div>
          <div class="stat-label">Cost per optimized page</div>
        </div>
      </div>
    </section>

    <!-- SERVICES -->
    <section id="services">
      <div class="section-header">
        <div class="section-tag">What We Build</div>
        <h2>Four Proven <span>pSEO Formats</span></h2>
        <p class="section-sub">Each format is purpose-built for a specific search intent — no generic templates, no thin content.</p>
      </div>
      <div class="services-grid">

        <div class="service-card">
          <div class="service-icon"><img src="{{ asset('frontend/files/comparison.png') }}" alt="Comparison Page"></div>
          <h3>Comparison Pages</h3>
          <p>Head-to-head pages for every competitor pair your SaaS competes with. Capture high-intent buyers who are actively comparing tools.</p>
          <div class="service-tags">
            <span class="tag">X vs Y templates</span>
            <span class="tag">Schema markup</span>
            <span class="tag">Verdict blocks</span>
          </div>
        </div>

        <div class="service-card">
          <div class="service-icon"><img src="{{ asset('frontend/files/integration.png') }}" alt="Integration Page"></div>
          <h3>Integration Pages</h3>
          <p>One page per integration partner, capturing queries like "[Your Tool] + [App]" — a massive untapped traffic source for SaaS products.</p>
          <div class="service-tags">
            <span class="tag">API-driven data</span>
            <span class="tag">Use-case content</span>
            <span class="tag">CTA optimization</span>
          </div>
        </div>

        <div class="service-card">
          <div class="service-icon"><img src="{{ asset('frontend/files/alternative.png') }}" alt="Alternative Page"></div>
          <h3>Alternative Pages</h3>
          <p>Intercept buyers searching for alternatives to your competitors. Own the "Best [Competitor] alternatives" query across every vertical you serve.</p>
          <div class="service-tags">
            <span class="tag">Competitor mapping</span>
            <span class="tag">Feature grids</span>
            <span class="tag">Review synthesis</span>
          </div>
        </div>

        <div class="service-card">
          <div class="service-icon"><img src="{{ asset('frontend/files/case-studies.png') }}" alt="Case Study"></div>
          <h3>Case Studies</h3>
          <p>Programmatic customer success pages at scale — segmented by industry, company size, and use case to match every search intent.</p>
          <div class="service-tags">
            <span class="tag">Industry segments</span>
            <span class="tag">Results data</span>
            <span class="tag">Structured proof</span>
          </div>
        </div>

      </div>
    </section>

    <!-- COMPARISON TABLE -->
    <section class="section-alt">
      <div class="section-header">
        <div class="section-tag">Why Programmatic</div>
        <h2>Traditional SEO <span>vs Programmatic SEO</span></h2>
        <p class="section-sub">Manual content creation simply cannot compete with the scale, speed, and cost-efficiency of a well-built pSEO system.</p>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Factor</th>
              <th>Traditional SEO</th>
              <th>Programmatic SEO (Build SaaS Links)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Pages per month</td>
              <td><span class="cross">✕</span> 10–20 articles</td>
              <td><span class="check">✓</span> 500–5,000+ optimized pages</td>
            </tr>
            <tr>
              <td>Cost per page</td>
              <td><span class="cross">✕</span> $200–$500/article</td>
              <td><span class="check">✓</span> Under $5 at scale</td>
            </tr>
            <tr>
              <td>Long-tail keyword coverage</td>
              <td><span class="cross">✕</span> Major keywords only</td>
              <td><span class="check">✓</span> Thousands of long-tail queries</td>
            </tr>
            <tr>
              <td>Time to publish</td>
              <td><span class="cross">✕</span> 2–4 weeks per article</td>
              <td><span class="check">✓</span> Thousands of pages in days</td>
            </tr>
            <tr>
              <td>Content freshness</td>
              <td><span class="cross">✕</span> Manual updates one at a time</td>
              <td><span class="check">✓</span> Automated updates across all pages</td>
            </tr>
            <tr>
              <td>Topical authority</td>
              <td><span class="cross">✕</span> Limited topic depth</td>
              <td><span class="check">✓</span> Broad authority signals across verticals</td>
            </tr>
            <tr>
              <td>ROI timeline</td>
              <td><span class="cross">✕</span> 6–12 months</td>
              <td><span class="check">✓</span> Traffic growth in 30–60 days</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- PROCESS -->
    <section id="process">
      <div class="section-header">
        <div class="section-tag">Our Process</div>
        <h2>From Data to <span>Pages at Scale</span></h2>
        <p class="section-sub">A four-step system that transforms raw data into thousands of live, indexed, converting pages.</p>
      </div>
      <div class="process-wrap">
        <div class="process-grid">

          <div class="process-step">
            <div class="step-num">Step 01</div>
            <div class="step-icon"><img src="{{ asset('frontend/files/dataset-building.png') }}" alt="Dataset Building"></div>
            <h3>Dataset Building</h3>
            <p>We collect, clean, and structure the data that powers your pages — competitors, integrations, industries, keywords, and entities.</p>
            <div class="step-arrow">→</div>
          </div>

          <div class="process-step">
            <div class="step-num">Step 02</div>
            <div class="step-icon"><img src="{{ asset('frontend/files/content-template.png') }}" alt="Content Template"></div>
            <h3>Content Template</h3>
            <p>We design AI-driven content templates that generate unique, intent-matched copy for each page using your dataset variables.</p>
            <div class="step-arrow">→</div>
          </div>

          <div class="process-step">
            <div class="step-num">Step 03</div>
            <div class="step-icon"><img src="{{ asset('frontend/files/design-template.png') }}" alt="Design Template"></div>
            <h3>Design Template</h3>
            <p>A conversion-optimised page layout built for your brand — fast-loading, schema-ready, and designed to rank and convert.</p>
            <div class="step-arrow">→</div>
          </div>

          <div class="process-step">
            <div class="step-num">Step 04</div>
            <div class="step-icon"><img src="{{ asset('frontend/files/pages-at-scale.png') }}" alt="Pages at Scale"></div>
            <h3>Pages at Scale</h3>
            <p>We generate, validate, and deploy hundreds or thousands of pages in coordinated batches — with crawl budget management built in.</p>
          </div>

        </div>
      </div>
    </section>

    <!-- PRICING -->
    <section id="pricing" class="section-alt">
      <div class="section-header">
        <div class="section-tag">Pricing</div>
        <h2>Simple, <span>Transparent Pricing</span></h2>
        <p class="section-sub">Start with validation, then scale. No retainers, no surprises.</p>
      </div>
      <div class="pricing-grid">

        <div class="pricing-card">
          <div class="pricing-name">pSEO Validation</div>
          <div class="pricing-price"><sup>$</sup>1,000</div>
          <div class="pricing-sub">One-time — 10 pages delivered</div>
          <div class="pricing-divider"></div>
          <ul class="pricing-features">
            <li>10 fully built programmatic pages</li>
            <li>1 page format of your choice</li>
            <li>Dataset collection & structuring</li>
            <li>Content + design template</li>
            <li>Schema markup implementation</li>
            <li>Indexation setup & validation</li>
            <li>Performance baseline report</li>
          </ul>
          <a href="#" class="pricing-btn">Get Started →</a>
        </div>

        <div class="pricing-card featured">
          <div class="pricing-name">pSEO Scale</div>
          <div class="pricing-price"><sup>$</sup>3,000</div>
          <div class="pricing-sub">Per campaign — up to 1,000 pages</div>
          <div class="pricing-divider"></div>
          <ul class="pricing-features">
            <li>Up to 1,000 optimized pages</li>
            <li>All 4 pSEO formats available</li>
            <li>Full dataset pipeline build</li>
            <li>Custom content + design templates</li>
            <li>Internal linking automation</li>
            <li>Crawl budget & sitemap optimization</li>
            <li>30-day post-launch monitoring</li>
            <li>Ongoing template refinement</li>
          </ul>
          <a href="#" class="pricing-btn">Start Scaling →</a>
        </div>

      </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
      <div class="section-tag">Let's Build</div>
      <h2>Ready to Own <span>Every Long-Tail Query</span> in Your Niche?</h2>
      <p>Start with a validation sprint and see real results before committing to full scale.</p>
      <a target="_blank" href="https://calendly.com/saurav-buildsaaslinks/30min" class="btn-primary" style="display:inline-block;">Book a Strategy Call →</a>
    </section>

    </div>
    <script>
      document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
          const target = document.querySelector(a.getAttribute('href'));
          if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
      });

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, { threshold: 0.1 });

      document.querySelectorAll('.service-card, .process-step, .pricing-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(24px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease, border-color 0.25s, transform 0.2s';
        observer.observe(el);
      });
    </script>
@endsection
