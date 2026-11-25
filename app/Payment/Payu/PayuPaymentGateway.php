<?php

namespace App\Payment\Payu;

use App\Facades\TransactionFacade as Transaction;
use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayuPaymentGateway implements PaymentGatewayInterface
{
    protected array $credentials;

    private PayuClient $client;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('payu');

        if (empty($this->credentials['merchant_key']) || empty($this->credentials['salt'])) {
            throw new \Exception('PayU credentials are not set.');
        }

        $this->client = new PayuClient;
    }

    /**
     * Create a PayU Payment Link and return its URL — exactly like the
     * PayPal gateway returns PayPal's "approval_url". The caller
     * (PaymentService/DepositController) just does redirect($url), no
     * intermediate auto-submit-form HTML page needed.
     *
     * @return string the URL to redirect the customer to
     *
     * @throws \Exception
     */
    public function deposit($amount, $currency, $txid): string
    {
        $paymentPayload = $this->buildPaymentLinkPayload($amount, $currency, $txid);

        $response = $this->client->createPaymentLink($paymentPayload);

        $paymentLink = $response['result']['paymentLink'] ?? null;

        if (! $paymentLink) {
            Log::error('PayU payment link creation failed', ['response' => $response, 'txid' => $txid]);

            throw new \Exception($response['message'] ?? 'Unable to create PayU payment link.');
        }

        return $paymentLink;
    }

    protected function buildPaymentLinkPayload($amount, $currency, $txid): array
    {
        $user = auth()->user();

        return [
            // invoiceNumber must be unique — our own txid already is.
            'invoiceNumber' => $txid,
            // Lets us look this transaction up by our own id later on.
            'transactionId' => $txid,
            'subAmount' => (float) number_format($amount, 2, '.', ''),
            'isAmountFilledByCustomer' => false,
            'isPartialPaymentAllowed' => false,
            'description' => setting('site_title') ?: 'Order Payment',
            'source' => 'API',
            'currency' => $currency,
            'customer' => [
                'name' => $user->name ?? 'Customer',
                'email' => $user->email ?? 'customer@example.com',
                'phone' => $user->phone ?? '9999999999',
            ],
            'successURL' => route('ipn.handle', ['gateway' => 'payu']),
            'failureURL' => route('status.cancel', ['txid' => $txid]),
        ];
    }

    public function handleIPN(Request $request): \Illuminate\Http\RedirectResponse
    {
        Log::info('PayU Callback', $request->all());

        $txnid = $request->input('txnid')
            ?: $request->input('transactionId')
            ?: $request->input('invoiceNumber');

        if (!$txnid) {

            notifyEvs('warning', __('Invalid payment response.'));

            return redirect()->back();
        }

        try {

            $payment = $this->client->verifyPayment($txnid);

            Log::info('PayU Verify Response', $payment);

            $status = strtolower($payment['status'] ?? '');

            if ($status === 'success') {

                Transaction::completeTransaction($txnid);

                notifyEvs('success', __('Payment Successful'));

                return redirect()->route('user.dashboard');
            }

            Log::warning('PayU Payment Failed', [
                'txnid' => $txnid,
                'status' => $status,
            ]);

            notifyEvs('warning', __('Payment Failed'));

            return redirect()->route('status.cancel', [
                'txid' => $txnid,
            ]);

        } catch (\Throwable $e) {

            Log::error('PayU Verify Exception', [
                'txnid' => $txnid,
                'message' => $e->getMessage(),
            ]);

            notifyEvs('warning', __('Unable to verify payment.'));

            return redirect()->route('status.cancel', [
                'txid' => $txnid,
            ]);
        }
    }
}
