<?php

namespace App\Services\PaymentGateways;

use App\Models\PaymentGateway;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class PaypalService
{
    protected $clientId;
    protected $secret;
    protected $baseUrl;
    protected $webhookId;

    public function __construct()
    {
        $gateway = PaymentGateway::where('name', 'paypal')->firstOrFail();
        $settings = $gateway->settings ?? [];

        $this->clientId = $settings['client_id'] ?? null;
        $this->secret = isset($settings['client_secret']) ? $this->decryptValue($settings['client_secret']) : null;
        $this->webhookId = $settings['webhook_id'] ?? null;

        $this->baseUrl = strtolower($gateway->mode) === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        Log::info('PayPalService initialized', [
            'mode' => $gateway->mode,
            'base_url' => $this->baseUrl
        ]);
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getWebhookId()
    {
        return $this->webhookId;
    }

    protected function decryptValue($value)
    {
        if (!is_string($value) || $value === '') {
            return null;
        }

        try {
            return decrypt($value);
        } catch (\Throwable $e) {
            return $value;
        }
    }

    /**
     * Verify an incoming webhook using PayPal's verification API.
     *
     * @param string $authAlgo       Paypal-Auth-Algo header
     * @param string $certUrl        Paypal-Cert-Url header
     * @param string $transmissionId Paypal-Transmission-Id header
     * @param string $transmissionSig Paypal-Transmission-Sig header
     * @param string $transmissionTime Paypal-Transmission-Time header
     * @param string $body           Raw webhook payload
     */
    public function verifyWebhook($authAlgo, $certUrl, $transmissionId, $transmissionSig, $transmissionTime, $body)
    {
        if (!$this->webhookId) {
            throw new \Exception('PayPal webhook ID is not configured.');
        }

        $event = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post("{$this->baseUrl}/v1/notification/verify-webhook-signature", [
                'auth_algo' => $authAlgo,
                'cert_url' => $certUrl,
                'transmission_id' => $transmissionId,
                'transmission_sig' => $transmissionSig,
                'transmission_time' => $transmissionTime,
                'webhook_id' => $this->webhookId,
                'webhook_event' => $event,
            ]);

        if (!$response->successful()) {
            Log::error('PayPal webhook verification request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        }

        return ($response['verification_status'] ?? null) === 'SUCCESS';
    }

    public function getAccessToken()
    {
        Log::debug('PayPal token requested');
        
        $response = Http::withBasicAuth($this->clientId, $this->secret)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials'
            ]);

        Log::info('PayPal Token Response', [
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        if (!$response->successful()) {
            Log::error('PayPal Token Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('Unable to get PayPal access token.');
        }

        return $response['access_token'];
    }

    public function createOrder($amountUSD, $plan, $user, $currency)
{
    Log::info('PayPal: Creating Order', [
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'amount' => $amountUSD,
        'currency' => $currency
    ]);

    $token = $this->getAccessToken();

    $orderData = [
        'intent' => 'CAPTURE',
        'purchase_units' => [[
            'amount' => [
                'currency_code' => $currency,
                'value' => number_format($amountUSD, 2, '.', ''),
            ],
        ]],
        'application_context' => [
            'return_url' => route('payment.callback', ['gateway' => 'paypal']),
            'cancel_url' => route('payment.cancel'),
        ],
    ];

    Log::debug('PayPal order payload sent');

    $response = Http::withToken($token)
        ->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
        ->post("{$this->baseUrl}/v2/checkout/orders", $orderData);

 Log::info('PayPal order created', [
    'order_id' => $response['id'] ?? null,
    'status' => $response->status()
]);

    if (!$response->successful()) {
        Log::error('PayPal Order Creation FAILED', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
        throw new \Exception('PayPal payment initialization failed.');
    }

    $orderId = $response['id'];
    
    Transaction::create([
    'user_id'           => $user->id,
    'plan_id'           => $plan->id,
    'gateway'           => 'paypal',
    'amount'            => $amountUSD,
    'currency'          => $currency,
    'original_amount'   => $amountUSD,
    'original_currency' => $currency,
    'reference'         => $orderId,
    'status'            => 'pending',
]);
  
    foreach ($response['links'] as $link) {
        if ($link['rel'] === 'approve') {
            Log::info('PayPal Approval Link Found', [
                'url' => $link['href']
            ]);
            return $link['href'];
        }
    }

    throw new \Exception('PayPal approval link not found.');
}
   public function captureOrder($orderId)
{
    Log::info('PayPal: Capturing Order', [
        'order_id' => $orderId
    ]);

    $token = $this->getAccessToken();

    $response = Http::withToken($token)
    ->withHeaders([
        'Accept' => 'application/json',
    ])
    ->withBody('{}', 'application/json')
    ->send(
        'POST',
        "{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture"
    );
    Log::info('PayPal capture completed', [
    'order_id' => $orderId,
    'status' => $response->status()
]);
if (!$response->successful()) {
    Log::error('PayPal capture failed', [
        'order_id' => $orderId,
        'status' => $response->status(),
        'body' => $response->body(),
    ]);

    throw new \Exception('Unable to capture PayPal payment.');
}
    return $response->json();
}


    public function initiatePayment($user, $amount, $currency, $plan)
    {
        Log::debug('PayPal initiating payment', [
    'user_id' => $user->id ]);

        $paypalSupported = [
            'USD','EUR','GBP','CAD','AUD','JPY','CHF','SEK','NOK','DKK',
            'PLN','CZK','MXN','ZAR','HKD','SGD'
        ];

        $paypalCurrency = strtoupper($currency);

        if (!in_array($paypalCurrency, $paypalSupported)) {
            Log::warning('PayPal Currency Not Supported — Falling Back to USD', [
                'original_currency' => $paypalCurrency
            ]);
            $paypalCurrency = 'USD';
        }

        return $this->createOrder($amount, $plan, $user, $paypalCurrency);
    }
}
