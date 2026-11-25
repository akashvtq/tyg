<?php

namespace App\Payment\Payu;

use App\Models\PaymentGateway;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

/**
 * Thin wrapper around PayU's REST APIs.
 *
 * PayU's classic "_payment" hosted-checkout endpoint (the one the old
 * PayuPaymentGateway posted an auto-submit HTML form to) is POST-only —
 * PayU does not accept it as a GET redirect, so there is no way to turn
 * that endpoint into a plain URL.
 *
 * PayU's "Payment Links" API (OneAPI) is the REST equivalent of PayPal's
 * "create payment -> get approval_url" flow: you call an authenticated
 * REST endpoint server-side and get back a real https:// URL you can
 * redirect the browser to directly, no intermediate form page needed.
 * That's what deposit() in PayuPaymentGateway now uses.
 *
 * The classic key/salt hash APIs are still used for verifying the
 * transaction status when PayU redirects the customer back.
 */
class PayuClient
{
    protected array $credentials;

    private Client $oneApiClient;

    private Client $accountsClient;

    private Client $legacyClient;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('payu');

        $live = ($this->credentials['mode'] ?? 'sandbox') === 'live';

        // Payment Links (create link) host.
        $this->oneApiClient = new Client([
            'base_uri' => $live ? 'https://oneapi.payu.in/' : 'https://uatoneapi.payu.in/',
        ]);

        // OAuth token host for the Payment Links API.
        $this->accountsClient = new Client([
            'base_uri' => $live ? 'https://accounts.payu.in/' : 'https://uat-accounts.payu.in/',
        ]);

        // Classic key/salt host, used only for verify_payment status checks.
        $this->legacyClient = new Client([
            'base_uri' => $live ? 'https://secure.payu.in/' : 'https://test.payu.in/',
        ]);
    }

    /**
     * Get (and cache) an OAuth access token for the Payment Links API.
     *
     * @throws \Exception
     */
    protected function getAccessToken(): string
    {
        if (empty($this->credentials['client_id']) || empty($this->credentials['client_secret'])) {
            throw new \Exception('PayU client_id / client_secret are not set.');
        }

        $mode = $this->credentials['mode'] ?? 'sandbox';
        $cacheKey = 'payu_access_token_'.md5($this->credentials['client_id'].'_'.$mode);

        return Cache::remember($cacheKey, 3300, function () {
            $response = $this->accountsClient->post('oauth/token', [
                'form_params' => [
                    'client_id' => $this->credentials['client_id'],
                    'client_secret' => $this->credentials['client_secret'],
                    'grant_type' => 'client_credentials',
                    'scope' => 'create_payment_links',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['access_token'])) {
                throw new \Exception($data['error_description'] ?? 'Unable to obtain PayU access token.');
            }

            return $data['access_token'];
        });
    }

    /**
     * Create a PayU Payment Link.
     *
     * result.paymentLink in the response is a ready-to-redirect-to URL —
     * PayU's equivalent of PayPal's "approval_url" link.
     *
     * @throws \Exception
     */
    public function createPaymentLink(array $payload): array
    {
        if (empty($this->credentials['merchant_id'])) {
            throw new \Exception('PayU merchant_id (mid) is not set.');
        }

        $response = $this->oneApiClient->post('payment-links', [
            'headers' => [
                'mid' => $this->credentials['merchant_id'],
                'Authorization' => 'Bearer '.$this->getAccessToken(),
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        return json_decode($response->getBody()->getContents(), true) ?? [];
    }

    /**
     * Look up a transaction's status directly from PayU using the classic
     * verify_payment web service. Used as a fallback in handleIPN() for
     * the case where the browser lands back on our success/failure URL
     * without the usual postback params (mihpayid/status/hash) attached.
     *
     * @throws \Exception
     */
    public function verifyPayment(string $txnid): array
    {
        $key = $this->credentials['merchant_key'];
        $salt = $this->credentials['salt'];

        $command = 'verify_payment';

        $hash = strtolower(hash('sha512', implode('|', [
            $key,
            $command,
            $txnid,
            $salt,
        ])));

        $response = $this->legacyClient->post('merchant/postservice', [
            'query' => [
                'form' => 2,
            ],
            'form_params' => [
                'key' => $key,
                'command' => $command,
                'var1' => $txnid,
                'hash' => $hash,
            ],
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        if (
            empty($result['transaction_details']) ||
            empty($result['transaction_details'][$txnid])
        ) {
            throw new \Exception('Unable to verify payment.');
        }

        return $result['transaction_details'][$txnid];
    }
}
