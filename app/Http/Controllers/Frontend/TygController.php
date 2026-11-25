<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Services;
use App\Support\TygServicePages;
use Database\Seeders\TygSeoServicesSeeder;
use Illuminate\Support\Collection;

/**
 * Pages of the TYG marketing frontend. Read-only: ordering still goes
 * through the existing user.order.services checkout and ServiceController.
 */
class TygController extends Controller
{
    public function seoServices()
    {
        $seoServices = $this->packages(TygSeoServicesSeeder::SLUGS);

        return view('frontend.tyg.seo-services', compact('seoServices'));
    }

    public function brandingServices()
    {
        return $this->servicePage('branding');
    }

    public function developmentServices()
    {
        return $this->servicePage('development');
    }

    private function servicePage(string $key)
    {
        $page = TygServicePages::get($key);
        $services = $this->packages($page['slugs']);

        return view('frontend.tyg.service', compact('page', 'services'));
    }

    /** Active services for the given slugs, in slug order. */
    private function packages(array $slugs): Collection
    {
        return Services::whereIn('slug', $slugs)
            ->where('status', Status::ACTIVE)
            ->get()
            ->sortBy(fn ($s) => array_search($s->slug, $slugs))
            ->values();
    }
}
