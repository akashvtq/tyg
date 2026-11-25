@extends('frontend.layouts.app')
@section('title')
    {{ __('Home') }}
@endsection

@push('meta')
<meta name="title" content="{{ $metaTitle }}">
<meta name="description" content="{{ $metaDescription }}">
@endpush
@section('content')

    <div class="container-fluid container-padding mt-50">
      <?php
/**
 * pSEO page - usage:
 *   include 'pseo.php' with $slug pre-set, OR
 *   visit pseo.php?slug=crm, OR
 *   php pseo.php crm
 */
if (!isset($slug)) {
    $slug = $_GET['slug'] ?? ($argv[1] ?? 'crm');
}

$dataPath = base_path().'/assets/frontend/data.json';

$data = json_decode(file_get_contents($dataPath), true);

if (!isset($data[$slug])) {
    http_response_code(404);
    echo "Slug '" . htmlspecialchars($slug) . "' not found.";
    return;
}

$d = $data[$slug];

// Derive competitor name from first entry of competitors list
$competitors = array_map('trim', explode(',', $d['example_companies_competitors'] ?? ''));
$competitorRaw = $competitors[0] ?? '';
$competitor = preg_replace('/\.[a-z]+$/i', '', $competitorRaw);
$competitor = ucfirst($competitor);

// Number formatter — pretty-print integers, leave strings like "12 million" alone
$fmt = function ($v) {
    if ($v === null || $v === '') return '';
    return is_numeric($v) ? number_format((int)$v) : $v;
};

$map = [
    '{product_name}'                 => $d['product_name'],
    '{total_pages}'                  => $fmt($d['total_pages']),
    '{pages_with_traffic}'           => $fmt($d['pages_with_traffic']),
    '{1%_pages}'                     => $fmt($d['1%_pages']),
    '{total_keywords}'               => $fmt($d['total_keywords']),
    '{global_search_volume}'         => $d['global_search_volume'],
    '{branded_links}'                => $fmt($d['branded_links']),
    '{best_listicles_opportunities}' => $fmt($d['total_listicle_opportunities']),
    '{links_lost_2_years}'           => $fmt($d['link_lost_2_years']),
    '{competitor}'                   => $competitor,
    '{company_name}'                 => $d['example_company_w/o_.com'] ?? $d['example_company'],
    '{people_vs_competitor}'         => $competitor,
    '{common_referring_domains}'         => $d['common_referring_domains'],
    '{example_company}'         => $d['example_company'],
    '{example_companies_competitors}'         => $d['example_companies_competitors'],
    '{top_informational_keywords}'         => $d['top_informational_keywords'],
    '{top_listicle_keywords}'         => $d['top_listicle_keywords'],
    '{top_buying_intent_terms}'         => $d['top_buying_intent_terms'],
    '{best}'                         => 'best',
    '{topic}'                        => strtolower($d['product_name']),
];

ob_start();
?>
<!-- pSEO page - paste between buildsaaslinks header/footer -->
<style>
  .subnav-wrap * { box-sizing: border-box; }
  .subnav {
    max-width: 1200px;
    margin: 0 auto;
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 14px;
    padding: 8px;
    display: flex;
    align-items: center;
    gap: 4px;
    overflow-x: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(167,139,250,0.3) transparent;
  }
  .subnav::-webkit-scrollbar { height: 6px; }
  .subnav::-webkit-scrollbar-thumb { background: rgba(132,99,221,0.25); border-radius: 3px; }
  .subnav a {
    display: inline-block;
    padding: 10px 18px;
    color: #333333;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    border-radius: 10px;
    white-space: nowrap;
    transition: all 0.2s ease;
  }
  .subnav a:hover { background: rgba(132,99,221,0.08); color: #111111; }
  .subnav a.active {
    background: linear-gradient(90deg,#b9342b,#b9342b);
    color: #fff;
    font-weight: 600;
    box-shadow: 0 0 20px rgba(132,99,221,0.3);
  }
  
  .pseo * { box-sizing: border-box; }
  .pseo {
   
  }
  .pseo .wrap {  margin: 0 auto; }
  .pseo h1, .pseo h2, .pseo h3, .pseo h4 { margin: 0; font-weight: 700; line-height: 1.2; }
  .pseo p { margin: 0; color: #555555; line-height: 1.6; }
  .pseo .accent { color: #b9342b; }
  .pseo .token { color: #b9342b; font-style: italic; }

  /* Section heading */
  .pseo .pill {
    display: inline-block;
    background: rgba(132,99,221,0.12);
    border: 1px solid rgba(132,99,221,0.3);
    color: #b9342b;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.6px;
    padding: 6px 14px;
    border-radius: 30px;
    text-transform: uppercase;
  }
  .pseo .sec-head { text-align: center; margin-bottom: 40px; }
  .pseo .sec-head h2 { font-size: clamp(24px,3vw,36px); margin: 16px 0 12px; }
  .pseo .sec-head p { max-width: 640px; margin: 0 auto; font-size: 14px; }
  .pseo section { margin-bottom: 90px; }

.flex {
    display: flex;
}
.text-white {
    --tw-text-opacity: 1;
    color: #111111 !important;
}
.text-xs {
    font-size: .75rem;
    line-height: 1rem;
	color:#666666;
}
.flex.gap-3 {
    margin-bottom: 10px;
}
.flex.gap-4 {
    margin-bottom: 15px;
}
.text-lg {
    font-size: 1.125rem;
    line-height: 1.75rem;
}
.font-bold {
    font-weight: 700;
}
  /* Hero */
  .pseo .hero { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; padding-top: 20px; }
  .pseo .hero h1 { font-size: clamp(32px,4vw,48px); }
  .pseo .hero ul { list-style: none; padding: 0; margin: 24px 0; }
  .pseo .hero li { padding: 6px 0 6px 26px; position: relative; font-size: 14px; color: #333333; }
  .pseo .hero li::before { content:"✓"; position:absolute; left:0; color:#b9342b; font-weight:700; }
  .pseo .hero-cta { display: flex; gap: 12px; margin-top: 20px; }
  .pseo .hero-form {
    
  }
  .pseo .hero-form label { display:block; font-size:12px; color:#666666; margin-bottom:6px; }
  .pseo .hero-form .field {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.15);
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 14px;
    font-size: 13px;
    color: #333333;
  }
  .pseo .hero-form .row { display: flex; gap: 12px; }
  .pseo .hero-form .row .field { flex: 1; }

  /* Buttons */
  .pseo .btn {
    display: inline-block;
    background: linear-gradient(90deg,#b9342b,#b9342b);
    color: #fff;
    padding: 11px 24px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
  }
  .pseo .btn.ghost { background: transparent; border: 1px solid rgba(132,99,221,0.4); }

  /* Stat cards row */
  .pseo .stats-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
  .pseo .stats-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
  .pseo .stat {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 14px;
    padding: 22px;
  }
  
  .stats-2.data2x {
    width: 70%;
    margin: 0 auto;
	}
  .stats-3.data3x {
    width: 70%;
    margin: 0 auto;
	}
  
  .pseo .stat .num { font-size: 28px; font-weight: 700; color: #b9342b; margin-bottom: 6px; }
  .pseo .stat .lbl { font-size: 12px; color: #666666; margin-bottom: 4px;  letter-spacing: 0.4px; }
  .pseo .stat p { font-size: 13px; }

  /* Callout boxes */
  .pseo .callout {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.15);
    border-radius: 14px;
    padding: 20px;
    font-size: 13px;
    color: #333333;
  }
  .pseo .callout strong { color: #b9342b; display: block; margin-bottom: 6px; font-size: 13px; }

  .pseo .cta-strip {
    background: linear-gradient(90deg, rgba(124,58,237,0.4), rgba(167,139,250,0.3));
    border: 1px solid rgba(132,99,221,0.4);
    border-radius: 30px;
    padding: 14px 24px;
    text-align: center;
    margin-top: 24px;
    font-size: 13px;
    font-weight: 600;
	margin: 0 auto;
	max-width:50%;
  }

  /* Gap analysis */
  .pseo .gap-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
  .pseo .gap-col {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 14px;
    padding: 20px;
    text-align: center;
  }
  .pseo .gap-col .tag { font-size: 11px; color: #666666; margin-bottom: 6px; }
  .pseo .gap-col h4 { font-size: 18px; margin-bottom: 8px; }
  .pseo .gap-col.miss h4 { color: #DC2626; }
  .pseo .gap-col.cov h4 { color: #059669; }
  .pseo .gap-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .pseo .gap-card {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 14px;
    padding: 20px;
  }
  .pseo .gap-card h4 { font-size: 15px; margin-bottom: 10px; color: #b9342b; }
  .pseo .gap-card p { font-size: 13px; margin-bottom: 10px; }
  .pseo .chip {
    display: inline-block;
    background: rgba(132,99,221,0.1);
    color: #333333;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    margin-right: 6px;
  }

  /* Featured / AI */
  .pseo .ai-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
  .pseo .ai-form, .pseo .ai-list {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 14px;
    padding: 22px;
  }
  .pseo .ai-form .field { background: #F6F5FA; border: 1px solid rgba(132,99,221,0.15); border-radius: 8px; padding: 10px 12px; margin-bottom: 10px; font-size: 17px; color: #666666; }
  .pseo .ai-list h4 { font-size: 14px; margin-bottom: 14px; color: #b9342b; }
  .pseo .ai-list-item { display:flex; justify-content: space-between; padding: 10px 0 5px 0;  font-size: 13px; color: #333333; }
  .pseo .ai-list-item span:last-child { color: #b9342b; font-weight: 600; }
  .pseo .ai-list-item2{display:flex; justify-content: space-between; padding: 2px 0; font-size: 13px; color: #333333;}

  /* Branded links */
  .pseo .branded-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
  .pseo .branded-grid, .pseo .branded-check {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 14px;
    padding: 22px;
  }
  .pseo .branded-list ul { list-style: none; padding: 0; margin: 14px 0 0; }
  .pseo .branded-list li { padding: 8px 0; font-size: 13px; color: #333333; display: flex; justify-content: space-between; }
  .pseo .branded-check ul { list-style: none; padding: 0; margin: 0; }
  .pseo .branded-check li { padding: 8px 0 8px 26px; position: relative; font-size: 13px; color: #333333; }
  .pseo .branded-check li::before { content:"✓"; position:absolute; left:0; color:#b9342b; font-weight:700; }
  .pseo .branded-bottom-container {
    margin-top: 20px;
   
    padding: 20px;
   
    font-size: 13px;
  }
  .pseo .branded-bottom {
    margin-top: 20px;
    background: rgba(132,99,221,0.06);
    border: 1px solid rgba(132,99,221,0.2);
    border-radius: 14px;
    padding: 20px;
    text-align: center;
    font-size: 13px;
  }

  /* Why links don't last */
  .pseo .last-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
  .pseo .last-card {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 14px;
    padding: 22px;
  }
  .pseo .last-card h4 { font-size: 14px; color: #b9342b; margin-bottom: 12px; }
  .pseo .last-card .row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 12px; color: #333333; border-bottom: 1px solid #E6E3F0; }
  .pseo .warn {
    background: rgba(248,113,113,0.08);
    border: 1px solid rgba(248,113,113,0.3);
    border-radius: 12px;
    padding: 16px;
    font-size: 13px;
    color: #B91C1C;
    margin-top: 14px;
  }
  .pseo .guarantee {
    background: linear-gradient(90deg, rgba(124,58,237,0.2), rgba(167,139,250,0.15));
    border: 1px solid rgba(132,99,221,0.3);
    border-radius: 14px;
    padding: 22px;
    text-align: center;
  }
  .pseo .guarantee h4 { font-size: 18px; margin-bottom: 8px; }

  /* Keyword strategy table */
  .pseo .kw-table {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 14px;
    padding: 6px 22px;
    margin-bottom: 20px;
  }
  .pseo .kw-row { display: grid; grid-template-columns: 1fr 2fr 1fr; padding: 14px 0; border-bottom: 1px solid #E6E3F0; font-size: 13px; color: #333333; }
  .pseo .kw-row:last-child { border-bottom: none; }
  .pseo .kw-row.head { color: #666666; font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; }
  .pseo .kw-cards { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
  .pseo .kw-card {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 14px;
    padding: 20px;
  }
  .pseo .kw-card .num { font-size: 22px; font-weight: 700; color: #b9342b; margin-bottom: 6px; }
  .pseo .kw-card h4 { font-size: 14px; margin-bottom: 6px; }
  .pseo .kw-card p { font-size: 12px; }

  /* Approach / What you get */
  .pseo .ap-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
  .pseo .ap-col {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 14px;
    padding: 24px;
  }
  .pseo .ap-col h4 { font-size: 16px; margin-bottom: 16px; }
  .pseo .ap-col ul { list-style: none; padding: 0; margin: 0; }
  .pseo .ap-col li { padding: 8px 0 8px 26px; position: relative; font-size: 13px; color: #333333; }
  .pseo .ap-col li::before { content:"✓"; position:absolute; left:0; color:#b9342b; font-weight:700; }
  .pseo .price-box {
    margin-top: 20px;
    background: linear-gradient(90deg, rgba(124,58,237,0.25), rgba(167,139,250,0.18));
    border: 1px solid rgba(132,99,221,0.35);
    border-radius: 14px;
    padding: 22px;
    display: flex; justify-content: space-between; align-items: center; gap: 20px;
  }
  .pseo .price-box .price { font-size: 26px; font-weight: 700; }
  .pseo .price-box .price small { font-size: 12px; color: #555555; font-weight: 400; margin-left: 6px; }

  /* Case studies */
  .pseo .case-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; }
  .pseo .case {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 14px;
    padding: 22px;
  }
  .pseo .case .num { font-size: 26px; font-weight: 700; color: #b9342b; margin-bottom: 10px; }
  .pseo .case h4 { font-size: 14px; margin-bottom: 6px; }
  .pseo .case p { font-size: 12px; margin-bottom: 12px; }
  .pseo .case .meta { font-size: 11px; color: #666666; padding-top: 10px; border-top: 1px solid #E6E3F0; }

  /* Final CTA */
  .pseo .final-cta {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.3);
    border-radius: 22px;
    padding: 50px 30px;
    text-align: center;
  }
  .pseo .final-cta h2 { font-size: clamp(24px,3vw,36px); margin-bottom: 14px; }
  .pseo .final-cta p { max-width: 540px; margin: 0 auto 24px; font-size: 14px; }
  .pseo .final-cta .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

  /* Integration footer */
  .pseo .integ {
    margin-top: 30px;
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.15);
    border-radius: 14px;
    padding: 20px;
    text-align: center;
    font-size: 12px;
    color: #666666;
  }
  .pseo .integ-logos { display: flex; gap: 16px; justify-content: center; margin-top: 12px; flex-wrap: wrap; }
  .pseo .integ-logos span {
    background: rgba(132,99,221,0.1);
    border: 1px solid rgba(132,99,221,0.2);
    padding: 6px 14px;
    border-radius: 20px;
    color: #333333;
    font-size: 11px;
  }
  .highintent {
	  color:#059669;
	  background-color: rgba(52, 211, 153, .08);
	      border-color: rgba(52, 211, 153, .2);
		  border-width: 1px;
		  border-radius: 9999px;
		      padding-left: .625rem;
    padding-right: .625rem;
	float:right;
	font-size:12px;
	    padding-top: 3px;
    padding-bottom: 3px;
  }
.h-2 {
	    width: 100%;
    height: 5px;
    background: rgba(124, 58, 237, .1);
    border-radius: 999px;
	
}
.h-2-filled{
	height: 5px;
    background: linear-gradient(to right, rgb(124, 58, 237), rgb(167, 139, 250));
    border-radius: 999px;
}
.check-item {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    color: #444444;
    font-size: .9375rem;
    line-height: 1.6;
}
.check-icon {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    background: rgba(124, 58, 237, .2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 2px;
}
.gap-3 {
    gap: .75rem;
}
.gap-4 {
    gap: 1rem;
}
.flex-col {
    flex-direction: column;
}
.buildh3 {
	font-size: 1.5rem;
    line-height: 2rem;
	font-weight: 700;
	text-align: center;
	margin-top: 2rem;
	margin-bottom: 2rem;
	
}
.hwbuild-listcontainer
{
	display: grid;
	grid-template-columns: repeat(2, minmax(0, 1fr));
	gap: 1rem;
	text-align:left;
	    margin-top: 1rem;
	
}
.branded-bottom p {
    font-size: large;
}
.branded-subcontainer {
	    text-align: center;
}
.thismattersdiv{
	border: 1px solid;
	    padding: .75rem;
		border-radius: .75rem;
		    align-items: center;
}
.tm1{
	background-color: rgba(239, 68, 68, .08);
	border-color: rgba(239, 68, 68, .2);
	color: #DC2626;
}
.tm2{
	background-color: rgba(251, 146, 60, .08);
	border-color: rgba(251, 146, 60, .2);
	color: #C2410C;
}
.tm3{
	background-color: rgba(250, 204, 21, .08);
	border-color: rgba(250, 204, 21, .2);
	color: #A16207;
}
.tm4{
	background-color: rgba(239, 68, 68, .12);
	border-color: rgba(239, 68, 68, .3);
	color: rgb(239 68 68);
}
.ml-auto {
    margin-left: auto;
}
.subbox{
	    margin-top: 20px;
		background: rgba(132,99,221, 0.06);
		border: 1px solid rgba(132,99,221, 0.2);
		border-radius: 14px;
		padding: 20px;
		text-align: center;
		font-size: 13px;
		display: inline-block;
}
.iconx1 {
    background-color: rgba(124, 58, 237, .15);
    border-radius: .75rem;
    justify-content: center;
    align-items: center;
    flex-shrink: 0;
	width: 2.5rem;
    height: 2.5rem;
	    text-align: center;
    display: flex;

}


.wg-wrap * { box-sizing: border-box; }
  .wg-wrap {
    font-family: 'Montserrat', sans-serif;
    background: #F6F5FA;
    padding: 40px 20px;
    display: grid;
    gap: 24px;
    max-width: 980px;
    margin: 0 auto;
  }

  /* Warn box */
  .wg-warn {
     background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 16px;
    padding: 32px 36px;
    display: flex;
    gap: 24px;
    align-items: flex-start;
  }
  .wg-warn .icon {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: rgba(251,191,36,0.12);
    border: 1px solid rgba(251,191,36,0.4);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    color: #B45309;
    font-size: 22px;
    font-weight: 700;
  }
  .wg-warn .body { flex: 1; }
  .wg-warn .lead {
    color: #555555;
    font-size: 15px;
    margin: 0 0 6px;
  }
  .wg-warn .lead em {
    color: #b9342b;
    font-style: italic;
    font-weight: 600;
  }
  .wg-warn .num {
    color: #b9342b;
    font-size: 44px;
    font-weight: 800;
    font-style: italic;
    line-height: 1.1;
    margin: 4px 0 6px;
  }
  .wg-warn .sub {
    color: #666666;
    font-size: 14px;
    margin: 0 0 16px;
  }
  .wg-warn .note {
    color: #b9342b;
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
  }

  /* Guarantee box */
  .wg-guarantee {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.3);
    border-radius: 16px;
    padding: 40px 36px;
    text-align: center;
  }
  .wg-guarantee .shield {
    width: 56px; height: 56px;
    margin: 0 auto 18px;
    display: flex; align-items: center; justify-content: center;
    color: #b9342b;
	font-size: 38px;
  }
  .wg-guarantee h3 {
    color: #111111;
    font-size: 26px;
    font-weight: 700;
    margin: 0 0 14px;
  }
  .wg-guarantee p {
    color: #666666;
    font-size: 15px;
    line-height: 1.6;
    max-width: 560px;
    margin: 0 auto;
  }
.fg-wrap * { box-sizing: border-box; }
.fg-wrap{  margin-bottom: 20px; }

  .fg-grid {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
  }
  .fg-card {
    border-radius: 18px;
    padding: 28px 28px 32px;
    border: 1px solid rgba(132,99,221,0.18);
    position: relative;
  }
  .fg-card.top {
    background: #F6F5FA;
    border-color: rgba(96,165,250,0.25);
  }
  .fg-card.mid {
    background: #F6F5FA;
    border-color: rgba(132,99,221,0.3);
  }
  .fg-card.bot {
    background: #F6F5FA;
    border-color: rgba(52,211,153,0.25);
  }

  .fg-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
  .fg-icon {
    width: 48px; height: 48px;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px;
  }
  .fg-tag {
    font-size: 12px;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 20px;
    border: 1px solid;
  }
  .fg-card.top .fg-tag { color: #b9342b; border-color: rgba(96,165,250,0.4); background: rgba(96,165,250,0.08); }
  .fg-card.mid .fg-tag { color: #b9342b; border-color: rgba(132,99,221,0.4); background: rgba(132,99,221,0.08); }
  .fg-card.bot .fg-tag { color: #059669; border-color: rgba(52,211,153,0.4); background: rgba(52,211,153,0.08); }

  .fg-num {
    color: #777777;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.6px;
    margin-bottom: 8px;
  }
  .fg-title {
    color: #111111;
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 18px;
  }
  .fg-chip {
    display: inline-block;
    background: rgba(132,99,221,0.12);
    border: 1px solid rgba(132,99,221,0.25);
    color: #b9342b;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-style: italic;
    font-weight: 500;
       margin-bottom: 10px;
    margin-top: 10px;
  }
  .fg-desc {
    color: #666666;
    font-size: 14px;
    line-height: 1.6;
    font-style: italic;
    margin: 0;
  }

  @media (max-width: 860px) {
    .fg-grid { grid-template-columns: 1fr; }
  }  
  @media (max-width: 860px) {
    .pseo .hero, .pseo .stats-3, .pseo .stats-2,
    .pseo .gap-grid, .pseo .gap-cards,
    .pseo .ai-grid, .pseo .branded-grid,
    .pseo .last-grid, .pseo .ap-grid,
    .pseo .case-grid, .pseo .kw-cards,
    .pseo .kw-row { grid-template-columns: 1fr; }
    .pseo .price-box { flex-direction: column; text-align: center; }
  }
  
   .ag-wrap * { box-sizing: border-box; }
  .ag-wrap {
    margin-bottom: 20px;
  }
  .ag-grid {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 28px;
  }
  .ag-card {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 18px;
    padding: 36px;
  }
  .ag-card h3 {
    color: #111111;
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 28px;
  }
  .ag-card h3 em {
    color: #b9342b;
    font-style: italic;
    font-weight: 700;
  }

  /* Left: numbered list */
  .ag-list { list-style: none; padding: 0; margin: 0; }
  .ag-list li {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 12px 0;
    color: #333333;
    font-size: 15px;
    font-style: italic;
  }
  .ag-list .ag-num {
    flex-shrink: 0;
    width: 32px; height: 32px;
    border-radius: 50%;
    background: rgba(132,99,221,0.12);
    border: 1px solid rgba(132,99,221,0.3);
    color: #b9342b;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    font-weight: 600;
    font-style: normal;
  }

  /* Right top: icon items */
  .ag-right { display: flex; flex-direction: column; gap: 24px; }
  .ag-items { display: flex; flex-direction: column; gap: 14px; }
  .ag-item {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.15);
    border-radius: 12px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    color: #111111;
    font-size: 15px;
    font-weight: 500;
  }
  .ag-item .ico { font-size: 22px; flex-shrink: 0; }

  /* Right bottom: fail card */
  .ag-fail {
    background: #F6F5FA;
    border: 1px solid rgba(248,113,113,0.35);
    border-radius: 18px;
    padding: 30px 32px;
  }
  .ag-fail h4 {
    color: #111111;
    font-size: 20px;
    font-weight: 700;
    margin: 0 0 18px;
  }
  .ag-fail h4 em { color: #b9342b; font-style: italic; }
  .ag-fail ul { list-style: none; padding: 0; margin: 0 0 16px; }
  .ag-fail li {
    color: #333333;
    font-size: 14px;
    padding: 6px 0 6px 26px;
    position: relative;
  }
  .ag-fail li::before {
    content: "✕";
    position: absolute;
    left: 0;
    color: #DC2626;
    font-weight: 700;
  }
  .ag-fail .result {
    color: #DC2626;
    font-size: 15px;
    font-weight: 700;
    margin: 0;
  }

  @media (max-width: 860px) {
    .ag-grid { grid-template-columns: 1fr; }
  }
  
   .cs-wrap * { box-sizing: border-box; }
  
  .cs-grid {
    max-width: 1240px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
  }
  .cs-card {
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 18px;
    padding: 32px;
  }

  .cs-head {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 28px;
  }
  .cs-logo {
    width: 56px; height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(124,58,237,0.4), rgba(167,139,250,0.25));
    border: 1px solid rgba(132,99,221,0.35);
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.5px;
    flex-shrink: 0;
  }
  .cs-name { color: #111111; font-size: 20px; font-weight: 700; }

  .cs-spike {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #111111;
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 8px;
  }
  .cs-spike .arrow {
    color: #059669;
    font-size: 22px;
  }
  .cs-range {
    color: #777777;
    font-size: 16px;
    margin-bottom: 32px;
  }
  .cs-range .to {
    margin: 0 6px;
    color: #666666;
  }

  .cs-divider {
    border-top: 1px solid rgba(132,99,221,0.12);
    margin: 0 -32px 24px;
  }
  .cs-cat {
    color: #b9342b;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    margin-bottom: 12px;
  }
  .cs-title {
    color: #111111;
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 14px;
    line-height: 1.4;
  }
  .cs-desc {
    color: #666666;
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
  }
.justify-center {
    justify-content: center;
}
.callinfo{
	font-size:16px !important;
}
  @media (max-width: 960px) {
    .cs-grid { grid-template-columns: 1fr; }
  }
  
   .cal-wrap * { box-sizing: border-box; }
  .cal-wrap {
    font-family: 'Montserrat', sans-serif;
    background: #F6F5FA;
    padding: 40px 20px;
  }
  .cal-card {
    max-width: 1100px;
    margin: 0 auto;
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.18);
    border-radius: 18px;
    overflow: hidden;
  }

  .cal-head {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 22px 28px;
    background: #F6F5FA;
    border-bottom: 1px solid rgba(132,99,221,0.15);
  }
  .cal-head-ico {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: rgba(132,99,221,0.15);
    border: 1px solid rgba(132,99,221,0.3);
    display: flex; align-items: center; justify-content: center;
    color: #b9342b;
    flex-shrink: 0;
  }
  .cal-head h3 {
    color: #111111;
    font-size: 18px;
    font-weight: 700;
    margin: 0;
  }
  .cal-head p {
    color: #666666;
    font-size: 13px;
    margin: 2px 0 0;
  }

  .cal-body {
    padding: 70px 30px 80px;
    text-align: center;
  }
  .cal-body-ico {
    width: 64px; height: 64px;
    border-radius: 14px;
    background: rgba(132,99,221,0.12);
    border: 1px solid rgba(132,99,221,0.3);
    display: inline-flex; align-items: center; justify-content: center;
    color: #b9342b;
    margin-bottom: 24px;
  }
  .cal-body h2 {
    color: #111111;
    font-size: 26px;
    font-weight: 700;
    margin: 0 0 14px;
  }
  .cal-body p {
    color: #666666;
    font-size: 15px;
    line-height: 1.6;
    max-width: 520px;
    margin: 0 auto 30px;
  }

  .cal-code {
    max-width: 720px;
    margin: 0 auto 30px;
    background: #F6F5FA;
    border: 1px solid rgba(132,99,221,0.2);
    border-radius: 12px;
    padding: 22px 26px;
    text-align: left;
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    font-size: 13px;
    color: #333333;
    line-height: 1.8;
    overflow-x: auto;
  }
  .cal-code .comment { color: #b9342b; }

  .cal-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(90deg,#b9342b,#b9342b);
    color: #fff;
    padding: 14px 32px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    box-shadow: 0 0 30px rgba(132,99,221,0.4);
  }

  .cal-trust {
    text-align: center;
    color: #666666;
    font-size: 14px;
    margin-top: 26px !important;
  }
  .cal-trust strong { color: #111111; font-weight: 600; }
  .metric-badge {
    background: rgba(124, 58, 237, .15);
    border: 1px solid rgba(124, 58, 237, .3);
    border-radius: .5rem;
    padding: .5rem .75rem;
    font-size: .875rem;
    color: #b9342b;
    font-weight: 600;
}

 @media (max-width: 460px) {
    .cta-strip {
    max-width: 90% !important;
}
.hwbuild-listcontainer {
    grid-template-columns: repeat(1, minmax(0, 1fr)) !Important;
 }
  }


</style>

<div class="pseo">
  <div class="subnav-wrap">
  <nav class="subnav">
    <?php foreach ($data as $slugx => $item): ?>
      <a href="/software/<?= htmlspecialchars($slugx) ?>"
         class="<?= $slug === $slugx ? 'active' : '' ?>">
        <?= htmlspecialchars($item['product_name']) ?>
      </a>
    <?php endforeach; ?>
  </nav>
</div>
  
  
  <div class="wrap">

    <!-- HERO -->
    <section class="hero">
      <div>
        <span class="pill"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>SaaS Link Building Agency</span>
        <h1 style="margin-top:18px;">Build High-<br>Quality Links for<br><span class="accent">{product_name}</span></h1>
        <p style="margin-top:16px; font-size:14px;">Capture High-Intent Traffic and Outrank Competitors in {product_name}.</p>
        <ul>
          <li>Links on real business/service websites</li>
          <li>Insertions after approval — no surprises</li>
          <li>1 year refundable guarantee</li>
        </ul>
        <div class="hero-cta">
          <a href="#calendy" class="btn">Build Links for {product_name} → </a> <a href="#opportunities" class="btn ghost">See the Opportunity</a>
        </div>
      </div>
      <div class="hero-form">
        <img src="{{ asset('frontend/bannerimage.png') }}" style="width:100%;" />
      </div>
    </section>

    <!-- OPPORTUNITY -->
    <section id="opportunities">
      <div class="sec-head">
        <span class="pill"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4l3 3"></path></svg> The Opportunity</span>
        <h2>Opportunity in <span class="token">{product_name}</span> SEO</h2>
        <p>There are <strong>{total_pages}</strong> pages on the internet targeting terms related to <span class="token">{product_name}</span>. If your brand gets featured on even 1% of these pages, you unlock massive visibility.</p>
      </div>
      <div class="stats-3 ">
        <div class="stat">
          
          <div class="num">{total_pages}</div>
          <p>Pages targeting {product_name} terms</p>
		  <div class="lbl">Total pages on the internet</div>
          
        </div>
        <div class="stat">
          
          <div class="num">{pages_with_traffic}</div>
          <p>Pages getting 1+ monthly visits</p>
		  <div class="lbl">Real traffic opportunity</div>
        </div>
        <div class="stat">
          
          <div class="num">{1%_pages}</div>
          <p>1% = your link building target</p>
		  <div class="lbl">Massive visibility unlock</div>
        </div>
      </div>
	  <br>
	  <br>
	  <div class="stat">
          <div class="lbl"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg></div>
          <p><strong>This is where strategic link building changes the game.</strong> Getting your brand featured on pages already ranking means instant authority transfer — no waiting months for new content to rank.</p>
        </div>
		
    </section>

    <!-- HIGH LEVERAGE -->
    <section>
      <div class="sec-head">
        
        <h2>Why Link Building for <span class="token">{product_name}</span> is a High-Leverage Move</h2>
      </div>
      <div class="stats-2 data2x">
        <div class="stat">
          <div class="lbl">🔑</div>
          <div class="num">{total_keywords}</div>
          <p>Total Keywords</p>
        </div>
        <div class="stat">
          <div class="lbl">🌍</div>
          <div class="num">{global_search_volume}</div>
          <p>Global Search Volume</p>
        </div>
      </div>
      <div class="stats-3 data3x" style="margin-top:20px;">
        <div class="callout">
          <strong>🔥</strong>
          Massive search demand exists
        </div>
        <div class="callout">
          <strong>🏆</strong>
          Thousands of ranking opportunities
        </div>
		<div class="callout">
          <strong>⚡</strong>
          High competition → authority matters
        </div>
      </div>
	  
	  <div class="">
	  <p style="text-align:center;margin: 16px 0 12px;">
	  <strong>Without links, you don't rank.</strong> With links, you accelerate visibility.</p>
	  
	  </div>
	  
      <div style="text-align:center;">
        <div class="cta-strip">Get Links for {product_name}  That Drive Traffic &amp; Revenue →</div>
      </div>
    </section>

    <!-- COMPETITOR GAP -->
    <section id="gap">
      <div class="sec-head">
        <span class="pill"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"></path></svg> Competitor Gap Analysis</span>
        <h2>Competitor Gap Analysis for <span class="token">{product_name}</span></h2>
        <p>Your competitors already have links from <span class="token">{common_referring_domains}</span> shared domains that <span class="token">{example_company}</span> is missing.
		</p>
      </div>
      <div class="gap-grid">
        <div class="gap-col miss">
          <div class="tag">{example_company}</div>
          <p>Your brand</p>
          
		  <h4>Missing</h4>
          <span class="btn ghost" style="font-size:11px; padding:6px 14px;">{common_referring_domains} domains</span>
        </div>
        <div class="gap-col cov">
          <div class="tag">{example_companies_competitors}</div>
          <p>Your competitors</p>
          
		  <h4>Covered</h4>
		  <span class="btn ghost" style="font-size:11px; padding:6px 14px;">{common_referring_domains} domains</span>
        </div>
      </div>
      <div class="gap-cards">
        <div class="gap-card">
          <h4>Find Top Competitors</h4>
          <p>Identify the top players ranking for {product_name} keywords.</p>
          <span class="chip">{example_companies_competitors}</span>
        </div>
        <div class="gap-card">
          <h4>Map Common Referring Domains</h4>
          <p>Find domains linking to competitors but NOT to {example_company}.</p>
          <span class="chip">{common_referring_domains} shared domains</span>
        </div>
        <div class="gap-card">
          <h4>Prioritize High-Traffic Pages</h4>
          <p>Target pages already ranking, driving traffic, and influencing buying decisions.</p>
          <span class="chip">Pages already converting</span>
        </div>
        <div class="gap-card">
          <h4>Execute Placements</h4>
          <p>Insert your brand contextually into these pages — instantly capturing competitor authority.</p>
          <span class="chip">Fastest starting point</span>
        </div>
      </div>
      <div style="text-align:center; margin-top:20px;">
        <div class="cta-strip">👉 This is the fastest starting point for link building in {product_name}</div>
      </div>
    </section>

    <!-- AI / BEST PAGES -->
    <section>
      <div class="sec-head">
        <span class="pill">Listicle Opportunity</span>
        <h2>Get Featured in AI &amp; <span class="accent">"Best"</span> Pages</h2>
        <p>Rank on <strong>{best_listicles_opportunities}</strong> pages and get cited by Perplexity, ChatGPT, and Gemini. We do the outreach — you get the placements.</p>
      </div>
      <div class="ai-grid">
        <div class="ai-form">
          <div class="field"><span class="text-2xl">🥇</span>"Best {product_name} software"<div class="highintent"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline></svg>High-intent</div></div>
          <div class="field"><span class="text-2xl">📋</span>"Top {product_name} tools"<div class="highintent"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline></svg>High-intent</div></div>
          <div class="field"><span class="text-2xl">🔍</span>"{product_name} platforms list"<div class="highintent"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline></svg>High-intent</div></div>
          <div class="field"><span class="text-2xl">🤖</span>"Alternatives to {product_name}"<div class="highintent"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline></svg>High-intent</div></div>
        </div>
        <div class="ai-list">
          <h4>AI Visibility Targets</h4>
		  <span>Getting listed = visibility + trust + conversions</span>
          <div class="ai-list-item"><span>Google AI Overview</span><span>+85%</span>
		  </div>
		   <div class="ai-list-item2">
			<div class="h-2"><div class="h-2-filled" style="width:85%"></div></div>
		  </div>
          <div class="ai-list-item"><span>ChatGPT Recommendations</span><span>+72%</span></div>
		   <div class="ai-list-item2">
			<div class="h-2"><div class="h-2-filled" style="width:72%"></div></div>
		  </div>
          <div class="ai-list-item"><span>Perplexity Citations</span><span>+68%</span></div>
		   <div class="ai-list-item2">
			<div class="h-2"><div class="h-2-filled" style="width:68%"></div></div>
		  </div>
          <div class="ai-list-item"><span>Decision-Stage Conversions</span><span>+91%</span></div>
		   <div class="ai-list-item2">
			<div class="h-2"><div class="h-2-filled" style="width:91%"></div></div>
		  </div>
        </div>
      </div>
    </section>

    <!-- BRANDED LINKS -->
    <section>
      <div class="sec-head">
        <span class="pill"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"></path></svg>
		Branded Links</span>
        <h2>Branded Links: Why They're Important For <span class="token">{product_name}</span> Search</h2>
      </div>
      <div class="branded-grid">
        <div class="branded-list">
          <p style="font-size:13px;">Take {example_company} — they have a massive brand presence across the internet.</p>
          <div class="stats-2 data2x" style="width:100%;">
			<div class="stat" style="text-align: center;padding: 12px;
    margin-top: 10px;">
			 
			  <div class="num">{branded_links}</div>
			  <p>Total branded links</p>
			</div>
			<div class="stat" style="text-align: center;padding: 12px;
    margin-top: 10px;">
			  
			  <div class="num" style="color:rgb(52, 211, 153)">Everywhere</div>
			  <p>Brand mentions</p>
			</div>
		  </div>
		  <ul>
            <li><span class="check-item"><span class="check-icon"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg></span>Listicles</span></li>
            <li><span class="check-item"><span class="check-icon"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg></span>Comparison pages</span></li>
            <li><span class="check-item"><span class="check-icon"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg></span>Industry blogs</span></li>
            <li><span class="check-item"><span class="check-icon"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg></span>High-traffic guides</span></li>
            
            
          </ul>
        </div>
        <div class="branded-check">
          <p style="font-size:13px; margin-bottom:14px;text-transform: uppercase;font-weight: 700;">Why They Show Up Everywhere</p>
         <div class="space-y-3">
			<div class="flex gap-3">
				<span class="text-xl flex-shrink-0 iconx1">🤖</span>
				<div><div class="text-sm font-semibold text-white">AI Platforms Recommend You</div>
				<div class="text-xs text-[#8892b0] mt-0.5">Google AI Overviews, ChatGPT, and Perplexity recommend brands they see mentioned everywhere.</div></div></div>
				<div class="flex gap-3"><span class="text-xl flex-shrink-0  iconx1">🔍</span>
				<div><div class="text-sm font-semibold text-white">Google Rewards Brands</div>
				<div class="text-xs text-[#8892b0] mt-0.5">Repeated brand mentions across authoritative pages signal trust and authority to Google.</div></div></div>
				<div class="flex gap-3"><span class="text-xl flex-shrink-0  iconx1">💡</span>
				<div><div class="text-sm font-semibold text-white">Buyers Trust Familiar Brands</div>
				<div class="text-xs text-[#8892b0] mt-0.5">Decision-stage buyers trust brands they keep encountering across comparison and review pages.</div>
			</div>
			</div>
		 </div>
        
		</div>
      </div>
      
	  <div class="branded-bottom-container">
       <h3 class="buildh3">How We Build Branded Links for<!-- --> <span class="accent">{product_name}</span> Companies</h3>
        <div class="branded-bottom">
		<p>We don't wait for rankings. We place your brand directly into pages that already rank.</p>
		<br>
		
			<div class="hwbuild-listcontainer">
				<div class="check-item">
					<div class="check-icon">
						<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
					</div>
					<span class="text-sm placeholder-tag">Insert your brand into existing high-traffic articles</span>
				</div>
				<div class="check-item">
					<div class="check-icon">
						<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
					</div>
					<span class="text-sm placeholder-tag">Get featured alongside competitors like {example_companies_competitors}</span>
				</div>
				<div class="check-item">
					<div class="check-icon">
						<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
					</div>
					<span class="text-sm placeholder-tag">Secure placements in "best", "top", guides, and comparison pages</span>
				</div>
				<div class="check-item">
					<div class="check-icon">
						<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
					</div>
					<span class="text-sm placeholder-tag">Build links that look natural, contextual, and editorial</span>
				</div>
			</div>
		</div>
      </div>
	  
		<div class="branded-subcontainer">
			<div class="subbox">
			<p class="text-lg font-bold text-white mb-1">You don't build authority over time —</p>
			<p class="text-lg font-bold purple-text">You plug into authority that already exists.</p>
			</div>
		</div>
	  
    </section>

    <!-- WHY LINKS DON'T LAST -->
    <section>
      <div class="sec-head">
        <span class="pill"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>Link Reclamation</span>
        <h2>Why Most <span class="token">{product_name}</span> Links Don't Last</h2>
        <p>Here's something most agencies won't tell you: a large % of links don't stay live.</p>
      </div>
      <div class="last-grid">
        <div class="last-card">
          <h4>Why This Matters for Your SEO</h4>
          <div class="flex flex-col gap-3">
			
			<div>
				<div class="flex items-center gap-3 thismattersdiv tm1">
					<span class="text-xl">🔗</span><span class="font-semibold text-red-400">Lost links</span>
					<svg class="ml-auto" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8892b0" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"></path></svg>
				</div>
			</div>
			<div>
				<div class="flex items-center gap-3 thismattersdiv tm2">
					<span class="text-xl">📉</span>
					<span class="font-semibold text-orange-400">Lost rankings</span>
						<svg class="ml-auto" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8892b0" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"></path></svg>
				</div>
			</div>
			<div>
				<div class="flex items-center gap-3 thismattersdiv tm3">
					<span class="text-xl">👥</span><span class="font-semibold text-yellow-400">Lost traffic</span>
					<svg class="ml-auto" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8892b0" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"></path></svg>
				</div>
			</div>
			<div>
			<div class="flex items-center gap-3 thismattersdiv tm4">
				<span class="text-xl">💸</span><span class="font-semibold text-red-500">Lost revenue</span>
			</div>
		  </div>
		  </div>
        </div>
      <div class="branded-check">
          <p style="font-size:13px; margin-bottom:7px;text-transform: uppercase;font-weight: 700;">How We Protect Your Links</p>
          <p style="color: rgb(136 146 176);margin-bottom:14px;">We don't just build links — we track and protect them.</p>
		 <div class="space-y-3">
			<div class="flex gap-4">
				<span class="text-xl flex-shrink-0  iconx1">📊</span>
				<div><div class="text-sm font-semibold text-white">Monthly Monitoring</div>
				<div class="text-xs text-[#8892b0] mt-0.5">Every live link tracked and verified each month.</div>
				</div>
				</div>
				<div class="flex gap-4"><span class="text-xl flex-shrink-0  iconx1">🤖</span>
				<div>
				<div class="text-sm font-semibold text-white">Automated + Manual Checks</div>
				<div class="text-xs text-[#8892b0] mt-0.5">Dual-layer verification for link status across all placements.</div>
				</div></div><div class="flex gap-4"><span class="text-xl flex-shrink-0  iconx1">⚡</span>
				<div><div class="text-sm font-semibold text-white">Immediate Action</div>
				<div class="text-xs text-[#8892b0] mt-0.5">When a link is removed, we act within 48 hours to restore it.</div>
			</div>
			</div>
		 </div>
        
		</div>
      

	  <!-- Warn box -->
	  <div class="wg-warn">
		<div class="icon">!</div>
		<div class="body">
		  <p class="lead">Look at <em>{example_company}</em>:</p>
		  <div class="num">{links_lost_2_years}</div>
		  <p class="sub">Referring domains lost in last 2 years</p>
		  <p class="note">That's authority leaking over time. Building links is only half the job — maintaining them is what compounds results.</p>
		</div>
	  </div>

	  <!-- Guarantee box -->
	  <div class="wg-guarantee">
		<div class="shield">
		  🛡️
		</div>
		<h3>1-Year Refundable Guarantee</h3>
		<p>If a link goes down and we can't replace it within 30 days, you get a full refund for that placement.</p>
	  </div>

	
	  
	  
	  </div>
    </section>

    <!-- KEYWORD STRATEGY -->
    <section>
      <div class="sec-head">
        <span class="pill"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="M21 21l-4.35-4.35"></path></svg>Keyword Strategy</span>
        <h2>Keyword Strategy for <span class="token">{product_name}</span> Link Building</h2>
        <p>Your backlink strategy depends on your goal. We map every placement to the right funnel stage.</p>
      </div>
      <div class="kw-table">
        <div class="kw-row head"><span>Intent Type</span><span>Example Keywords</span><span>Goal</span></div>
        <div class="kw-row"><span>Informational</span><span>{top_informational_keywords}</span><span><span class="metric-badge">Scale Traffic</span></span></div>
        <div class="kw-row"><span>Listicle / Discovery</span><span>{top_listicle_keywords}</span><span><span class="metric-badge">AI Visibility</span></span></div>
        <div class="kw-row"><span>Buying Intent</span><span>{top_buying_intent_terms}</span><span><span class="metric-badge">Drive Conversions</span></span></div>
      </div>
      <div class="fg-wrap">
  <div class="fg-grid">

    <!-- Top of Funnel -->
    <div class="fg-card top">
      <div class="fg-head">
        <div class="fg-icon">📊</div>
        <span class="fg-tag">Top of Funnel</span>
      </div>
      <div class="fg-num">GOAL 01</div>
      <h3 class="fg-title">Scale Traffic</h3>
      <div class="fg-chip">{top_informational_keywords}</div>
      <p class="fg-desc">Build links on educational content. Capture early-stage users researching {product_name}.</p>
    </div>

    <!-- Mid Funnel -->
    <div class="fg-card mid">
      <div class="fg-head">
        <div class="fg-icon">🤖</div>
        <span class="fg-tag">Mid Funnel</span>
      </div>
      <div class="fg-num">GOAL 02</div>
      <h3 class="fg-title">Get Featured in AI / LLMs</h3>
      <div class="fg-chip">{top_listicle_keywords}</div>
      <p class="fg-desc">"Best", "Top", "Alternatives" pages. High visibility across AI platforms and search.</p>
    </div>

    <!-- Bottom Funnel -->
    <div class="fg-card bot">
      <div class="fg-head">
        <div class="fg-icon">💰</div>
        <span class="fg-tag">Bottom Funnel</span>
      </div>
      <div class="fg-num">GOAL 03</div>
      <h3 class="fg-title">Drive Conversions</h3>
      <div class="fg-chip">{top_buying_intent_terms}</div>
      <p class="fg-desc">Pricing, demo, comparison pages. High purchase intent — users ready to buy.</p>
    </div>

  </div>
</div>
	<div class="ag-wrap">
  <div class="ag-grid">

    <!-- LEFT: Approach -->
    <div class="ag-card">
      <h3>Our Approach to Link Building for <em>{product_name}</em></h3>
      <ul class="ag-list">
        <li><span class="ag-num">1</span>Identify competitor link gaps</li>
        <li><span class="ag-num">2</span>Map high-intent keyword clusters related to {product_name}</li>
        <li><span class="ag-num">3</span>Build use cases of {product_name}</li>
        <li><span class="ag-num">4</span>Find pages that talk about the use cases of {product_name}</li>
        <li><span class="ag-num">5</span>Build contextual placements on relevant pages</li>
        <li><span class="ag-num">6</span>Scale across listicles, comparisons, and guides</li>
      </ul>
    </div>

    <!-- RIGHT: What You Get + Why Fail -->
    <div class="ag-right">
      <div class="ag-card">
        <h3>What You Actually Get</h3>
        <div class="ag-items">
          <div class="ag-item"><span class="ico">✅</span>Links from pages already ranking</div>
          <div class="ag-item"><span class="ico">🎯</span>Placement in high-intent content</div>
          <div class="ag-item"><span class="ico">📈</span>Coverage across decision-stage queries</div>
          <div class="ag-item"><span class="ico">🚀</span>Scalable SEO growth system</div>
        </div>
      </div>

      <div class="ag-fail">
        <h4>Why Most <em>{product_name}</em> Companies Fail at SEO</h4>
        <ul>
          <li>Focus only on content, ignore distribution (links)</li>
          <li>Miss high-intent placements</li>
          <li>Don't leverage competitor gaps</li>
        </ul>
        <p class="result">Result: slow growth, low visibility</p>
      </div>
    </div>

  </div>
</div>  
	  
	  
    </section>

  

    <!-- CASE STUDIES -->
    <section>
      <div class="sec-head">
        <span class="pill"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>Results</span>
        <h2>Case <span class="accent">Studies</span></h2>
        <p>Real results from real campaigns. Traffic spikes, revenue growth, authority built.</p>
      </div>
      <div class="cs-wrap">
  <div class="cs-grid">

    <!-- Card 1 -->
    <div class="cs-card">
      <div class="cs-head">
        <div class="cs-logo">AVO</div>
        <div class="cs-name">Alliance Virtual Offices</div>
      </div>
      <div class="cs-spike">20% Spike in Traffic <span class="arrow">↗</span></div>
      <div class="cs-range">$125K <span class="to">→</span> $150K</div>
      <div class="cs-divider"></div>
      <div class="cs-cat">VIRTUAL OFFICES</div>
      <h4 class="cs-title">Alliance Virtual Office – Link Building Campaign</h4>
      <p class="cs-desc">Alliance Virtual Office offers virtual offices to small businesses who can't afford physical locations. We built contextual links across business resource pages.</p>
    </div>

    <!-- Card 2 -->
    <div class="cs-card">
      <div class="cs-head">
        <div class="cs-logo">H</div>
        <div class="cs-name">Hostinger</div>
      </div>
      <div class="cs-spike">100% Spike in Traffic <span class="arrow">↗</span></div>
      <div class="cs-range">$1M <span class="to">→</span> $2M</div>
      <div class="cs-divider"></div>
      <div class="cs-cat">WEB HOSTING</div>
      <h4 class="cs-title">Hostinger.com – Links To Stat Pages</h4>
      <p class="cs-desc">Hostinger, a well-known hosting company based in Cyprus, wanted us to get links to their statistics and landing pages.</p>
    </div>

    <!-- Card 3 -->
    <div class="cs-card">
      <div class="cs-head">
        <div class="cs-logo">FW</div>
        <div class="cs-name">Freshworks CRM</div>
      </div>
      <div class="cs-spike">28.6% Spike in Traffic <span class="arrow">↗</span></div>
      <div class="cs-range">$1.4M <span class="to">→</span> $1.8M</div>
      <div class="cs-divider"></div>
      <div class="cs-cat">CRM SOFTWARE</div>
      <h4 class="cs-title">Freshworks CRM – Landing Page Link Building</h4>
      <p class="cs-desc">Freshworks CRM is a powerful, easy-to-use CRM solution to unlock value across Sales, Marketing, and Customer Support.</p>
    </div>

  </div>
</div>
    </section>

    <!-- FINAL CTA -->
    <section class="final-cta">
      <span class="pill">Strategy calls available this week</span>
      <h2 style="margin-top:14px;">Start Building Links for <span class="token">{product_name}</span></h2>
      <p>Capture traffic from high-intent pages your competitors already rank on. Get a free competitor link gap report and a 30-minute strategy call.</p>
      <div class="actions">
        <a href="#calendy" class="btn">👉 Book a 30-Minute Strategy Call</a>
        <a href="#gap" class="btn ghost">👉 Get Free Competitor Gap Report</a>
      </div>

      <div class="integ callinfo">
        <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6"><div class="flex items-center gap-2 text-sm text-[#8892b0]"><span>🔒</span><span>100% Confidential</span></div><div class="flex items-center gap-2 text-sm text-[#8892b0]"><span>🎯</span><span>No Sales Pressure</span></div><div class="flex items-center gap-2 text-sm text-[#8892b0]"><span>⏱️</span><span>30-Minute Strategy Call</span></div><div class="flex items-center gap-2 text-sm text-[#8892b0]"><span>🆓</span><span>Free Competitor Gap Report</span></div></div>
      </div>
    </section>
	
	<section id="calendy">
	<div class="">
  <div class="cal-card">

    <div class="cal-head">
      <div class="cal-head-ico">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
      </div>
      <div>
        <h3>Schedule Your Strategy Call</h3>
        <p>30 minutes · Free · No obligation</p>
      </div>
    </div>

    <div class="cal-body">
      <!-- Calendly inline widget begin -->
<div class="calendly-inline-widget" data-url="https://calendly.com/saurav-buildsaaslinks/30min" style="min-width:320px;height:700px;"></div>
<script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
<!-- Calendly inline widget end -->
    </div>

  </div>

  <p class="cal-trust">Trusted by SaaS companies including <strong>Alliance Virtual Offices</strong>, <strong>Hostinger</strong>, and <strong>Freshworks</strong></p>
</div>
</section>

  </div>
</div>
<?php
$html = ob_get_clean();
echo strtr($html, $map);
?>

    </div>
@endsection
