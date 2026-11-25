<?php

namespace Database\Seeders;

use App\Models\DepositMethod;
use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PayuGatewaySeeder extends Seeder
{
    /**
     * Seed the PayU payment gateway and a matching deposit method.
     *
     * Run with: php artisan db:seed --class=PayuGatewaySeeder
     *
     * NOTE: the credentials below are placeholders — update them from the
     * admin panel (Payment Gateways > PayU > Edit) before going live:
     *   - merchant_key / salt   -> classic keys, used for hash verification.
     *   - client_id / client_secret -> OAuth credentials for the Payment
     *     Links API (Dashboard > Payment Links > API Keys).
     *   - merchant_id -> your PayU "mid", required by the Payment Links API.
     */
    public function run(): void
    {
        $gateway = PaymentGateway::updateOrCreate(
            ['code' => 'payu'],
            [
                'logo' => 'general/static/gateway/payu.png',
                'name' => 'PayU',
                'currencies' => ['INR', 'USD'],
                'credentials' => [
                    'merchant_key' => 'your-payu-merchant-key',
                    'salt' => 'your-payu-salt',
                    'client_id' => 'your-payu-client-id',
                    'client_secret' => 'your-payu-client-secret',
                    'merchant_id' => 'your-payu-merchant-id',
                    'mode' => 'sandbox', // 'sandbox' or 'live'
                ],
                'is_withdraw' => null,
                'status' => 1,
            ]
        );

        DepositMethod::updateOrCreate(
            ['method_code' => 'payu-inr'],
            [
                'payment_gateway_id' => $gateway->id,
                'logo' => 'general/static/gateway/payu.png',
                'name' => 'PayU INR',
                'type' => 'auto',
                'currency' => 'INR',
                'currency_symbol' => '₹',
                'min_deposit' => 10,
                'max_deposit' => 100000,
                'conversion_rate_live' => 0,
                'conversion_rate' => 1,
                'charge_type' => 'percent',
                'charge' => 2,
                'fields' => null,
                'receive_payment_details' => null,
                'status' => 1,
            ]
        );

        PaymentGateway::flushCache($gateway);
    }
}
