<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function index($slug)
    {
        $dataPath = base_path().'/assets/frontend/data.json';
        $data = json_decode(file_get_contents($dataPath), true);

        if (!isset($data[$slug])) {
            abort(404);
        }

        $d = $data[$slug];

        $metaTitle = $d['product_name'];

        $metaDescription = "Build links for {$d['product_name']} using competitor gap analysis, branded placements, and listicle opportunities. Tap into {$d['total_pages']} pages and {$d['global_search_volume']} demand to improve visibility and traffic.";
        return view('frontend.company.index', compact('slug','d', 'metaTitle', 'metaDescription'));
    }

    public function seoServices(){
        $metaTitle ='Programmatic SEO for SaaS Companies | Build SaaS Links';

        $metaDescription = "Generate thousands of targeted landing pages at scale. We build programmatic comparison, integration, alternative, and case study pages that rank and convert.";
        return view('frontend.company.seo-services', compact('metaTitle', 'metaDescription'));
    }
}