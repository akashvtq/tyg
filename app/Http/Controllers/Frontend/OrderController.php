<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DepositMethod;
use App\Models\Services;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    //  protected $paymentService;

    public function __construct()
    {
        //   $this->paymentService = $paymentService;

    }

    public function orderServices()
    {
        $services = Services::getAll();
        $order_summary = array();
        foreach (count($services) > 0 ? $services : array() as $s) {
            if (!isset($order_summary[$s->slug])) {
                $order_summary[$s->slug] = array(
                    'name' => _tr($s->name),
                    'projects' => array(),
                    'value' => 0,
                    'price' => $s->price
                );
                // $projects = array();
                // for ($i = 1; $i <= $order_summary[$s->slug]['value']; $i++) {
                //     $projects['project_' . $i] = array('name' => 'Project ' . $i, 'project_id' => 'project_' . $i, 'anchor_text' => '', 'target_url' => '');
                // }
                // $order_summary[$s->slug]['projects'] = $projects;
            }
        }
        $depositMethods = DepositMethod::active()->get();
        return view('frontend.order.services', compact('services', 'order_summary', 'depositMethods'));
    }
}
