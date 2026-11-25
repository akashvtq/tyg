<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\MethodType;
use App\Constants\OrderStatus;
use App\Constants\TxnType;
use App\Facades\TransactionFacade as Transaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Services\ServicePurchaseRequest;
use App\Models\Admin;
use App\Models\DepositMethod;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Services;
use App\Models\Task;
use App\Notifications\Admin\ServiceNotify as AdminServiceActionNotify;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Stripe\Service\Climate\OrderService;

class ServiceController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function details($slug)
    {
        $service = Services::getBySlugWithCache($slug);

        return view('frontend.service.details', compact('service'));
    }

    public function bookService($slug)
    {
        $service = Services::getBySlug($slug);
        if (! $service) {
            abort(404);
        }
        // $session_key = "order_" . Auth::user()->id;
        //session([$session_key => array('selected_service' => $slug, 'order_summary' => array())]);
        return redirect()->route('user.order.services');
        // $depositMethods = DepositMethod::active()->get();
        // return view('frontend.user.service.book_now', compact('service', 'depositMethods'));
    }

    public function store(Request $request)
    {
        $order_summary = json_decode($request->order_summary, true);
        $filtered = collect($order_summary)->filter(function ($item) {
            return isset($item['value']) && $item['value'] > 0;
        });
        if ($filtered->count() == 0) {
            return response()->json(['error' => true, 'message' => 'Please Select Services']);
        } else if (!$request->payment_method) {
            return response()->json(['error' => true, 'message' => 'Please Select Payment Method']);
        } else {
            $allValid = collect($order_summary)->every(function ($service) {
                return collect($service['projects'] ?? [])->every(function ($project) {
                    return !empty($project['anchor_text']) && !empty($project['target_url']);
                });
            });
            if (!$allValid)
                return response()->json(['error' => true, 'message' => 'Please Fill Project Data']);
        }
        try {
            //$service = Services::findOrFail($validated['service_id']);
            $services = Services::getBySlugs(array_keys($order_summary));
			
            if (count($services) == 0) {
                return response()->json(['error' => true, 'message' => 'No services found']);
            }

            $amount = 0;
            $all_services = array();
			
            foreach ($services as $s) {
                $service_value = $order_summary[$s->slug] ? intval($order_summary[$s->slug]['value']) : 0;
                
				$amount+= $s->price * $service_value;
                array_push($all_services, array('id' => $s->id, 'slug' => $s->slug, 'price' => $s->price, 'qty' => $service_value, 'projects' => $order_summary[$s->slug] ? $order_summary[$s->slug]['projects'] : array()));
            }
            // // Add service price and transaction type
            $validated['amount'] = $amount;
            $validated['type'] = TxnType::SERVICE_PURCHASE;
            $validated['deposit_method'] = $request->payment_method;
            if ($validated['deposit_method'] === 'user_balance') {
                $validated['payment_source'] = 'user_balance';
                $validated['method_type'] = MethodType::SYSTEM;
                $this->processUserBalancePayment($validated, $all_services, $request->notes);
                return response()->json(['success' => true, 'message' => 'Order placed successfully']);
            }

            $redirecturl = $this->processOtherDepositMethods($validated, $all_services, $request->notes);
            $message = '';
            if($request->payment_method === 'paypal-usd'){
                $message = 'Redirecting to Paypal for payment';
            }else if($request->payment_method === 'payu-usd'){
                $message = 'Redirecting to PayU for payment';
            }
            return response()->json(['success' => true, 'message' => $message,'redirecturl'=>$redirecturl]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
            //return $this->handlePaymentError($e);
        }
    }

    public function myService()
    {
        // Task priority levels mapping

        $orders = Order::where('user_id', auth()->id())->latest()->paginate(10);

        return view('frontend.user.service.my_service', compact('orders'));
    }

    public function taskBoard($id)
    {
        $order = Order::with(['user', 'transaction', 'tasks'])->find($id);

        $paymentStatus = collect([
            'pending' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
        ])->get($order->transaction->status, 'primary');

        $orderStatus = collect([
            OrderStatus::PENDING => 'info',
            OrderStatus::WORKING => 'orange',
            OrderStatus::COMPLETED => 'success',
            OrderStatus::CANCELLED => 'danger',
        ])->get($order->status);

        // Task priority levels mapping
        $taskPriority = [
            'low' => 'primary',
            'medium' => 'warning',
            'high' => 'danger',
        ];

        // Task statuses and their respective properties
        $taskBoard = collect([
            [
                'label' => __('Pending Projects'),
                'tasks' => $order->tasks()->pending()->get(),
                'border' => 'indigo',
            ],
            [
                'label' => __('In Progress'),
                'tasks' => $order->tasks()->inProgress()->get(),
                'border' => 'orange',
            ],
            [
                'label' => __('Done'),
                'tasks' => $order->tasks()->completed()->get(),
                'border' => 'green',
            ],
        ]);

        return view('frontend.user.service.task_board', compact('order', 'paymentStatus', 'taskPriority', 'taskBoard', 'orderStatus'));
    }

    public function taskShow($id)
    {
        $taskPriority = [
            'low' => 'primary',
            'medium' => 'warning',
            'high' => 'danger',
        ];

        $taskStatus = [
            'pending' => 'secondary',
            'in_progress' => 'info',
            'completed' => 'success',
        ];
        $task = Task::findOrFail($id);

        return view('frontend.user.service.task_show', compact('task', 'taskPriority', 'taskStatus'))->render();
    }

    public function taskFile($id)
    {
        $task = Task::findOrFail($id);

        $path = 'assets/' . $task->attachment;

        if (! file_exists($path)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        return response()->download($path);
    }

    private function processUserBalancePayment(array $validated, $services, $notes)
    {

        return DB::transaction(function () use ($services, $validated, $notes) {
            $transaction = $this->paymentService->processPayment($validated);
            $this->createOrder(auth()->id(), $services, $transaction->id, $notes);
            Transaction::completeTransaction($transaction->txid);
            // notifyEvs('success', __('Service purchased successfully.'));
            // return redirect()->route('user.transaction');
        });
    }

    private function processOtherDepositMethods(array $validated, $services, $notes)
    {
        [$transaction, $gatewayInjection] = $this->paymentService->processPayment($validated);

        $this->createOrder(auth()->id(), $services, $transaction->id, $notes);

        if ($transaction->method_type === MethodType::MANUAL) {
            $admins = Admin::permission('service-notification')->get();
            Notification::send($admins, new AdminServiceActionNotify($transaction));
        }

        return $gatewayInjection;
    }

    private function createOrder($userId, $services, $transactionId, $notes)
    {
		$nextId = Order::max('id') + 1;
        $order = Order::create([
            'user_id' => $userId,
            //'service_id' => $serviceId,
            'transaction_id' => $transactionId,
            'order_number' => '#' .date("ymd").$nextId,// rand(100000, 999999),
            'status' => OrderStatus::PENDING,
            'notes' => $notes
        ]);
        foreach ($services as $s) {
            if ($s['qty'] > 0) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $s['id'],
                    'service_slug' => $s['slug'],
                    'price' => $s['price'],
                    'qty' => $s['qty']
                ]);
                foreach (count($s['projects']) > 0 ? $s['projects'] : array() as $p) {
                    Task::create([
                        'order_id' => $order->id,
                        'order_item_id' => $orderItem->id,
                        'name' => $p["target_url"],
                        'description' => $p["anchor_text"],
                        'label' => $p["name"]
                    ]);
                }
            }
        }
    }

    private function handlePaymentError(Exception $e)
    {
        notifyEvs('error', $e->getMessage());

        return redirect()->back();
    }
}
