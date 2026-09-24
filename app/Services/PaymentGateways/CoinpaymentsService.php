<?php

namespace App\Services\PaymentGateways;

use App\Models\PaymentGateway;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CoinpaymentsService
{
    protected string $publicKey;
    protected string $privateKey;
    protected string $merchantId;
    protected ?string $ipnSecret;
    protected string $apiUrl = 'https://www.coinpayments.net/api.php';

    public function __construct()
{
    $gateway = PaymentGateway::where('name', 'coinpayments')->firstOrFail();
    $settings = $gateway->settings ?? [];

    $this->publicKey = decrypt(
        $settings['public_key']
        ?? throw new \Exception('CoinPayments public key missing')
    );

    $this->privateKey = decrypt(
        $settings['private_key']
        ?? throw new \Exception('CoinPayments private key missing')
    );

    $this->merchantId = decrypt(
        $settings['merchant_id']
        ?? throw new \Exception('CoinPayments merchant ID missing')
    );

    // IPN secret is used to verify callback signatures. If it is not
    // configured, callbacks will be rejected (fail-closed).
    $this->ipnSecret = !empty($settings['ipn_secret'])
        ? (function () use ($settings) {
            try {
                return decrypt($settings['ipn_secret']);
            } catch (\Throwable $e) {
                return $settings['ipn_secret'];
            }
        })()
        : null;
}

    /**
     * The IPN secret used to verify CoinPayments callback signatures.
     */
    public function ipnSecret(): ?string
    {
        return $this->ipnSecret;
    }

    /**
     * The configured CoinPayments merchant/account id.
     */
    public function merchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * Core API request
     */
    protected function apiRequest(array $params): array
    {
        $params = array_merge($params, [
            'version' => 1,
            'key' => $this->publicKey,
            'format' => 'json',
        ]);

        $postData = http_build_query($params, '', '&');
        $hmac = hash_hmac('sha512', $postData, $this->privateKey);

        Log::info('CoinPayments Request', $params);

        $response = Http::withHeaders([
                'HMAC' => $hmac,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->asForm()
            ->timeout(30)
            ->connectTimeout(10)
            ->post($this->apiUrl, $params);

        if (!$response->successful()) {
            Log::error('CoinPayments HTTP Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('CoinPayments API request failed.');
        }

        $data = $response->json();

        Log::info('CoinPayments Response', $data);

        if (($data['error'] ?? null) !== 'ok') {
            throw new \Exception('CoinPayments API error: ' . ($data['error'] ?? 'Unknown error'));
        }

        return $data['result'];
    }

    /**
     * Create payment.
     *
     * Returns the CoinPayments checkout URL (a string) so the caller can
     * redirect the buyer directly to checkout.
     */
    public function initiatePayment($user, float $amount, string $currency, $plan): string
    {
        $params = [
            'cmd' => 'create_transaction',
            'amount' => $amount,
            'currency1' => strtoupper($currency),
            'currency2' => 'BTC', // you can make this configurable
            'buyer_email' => $user->email,
            'item_name' => $plan->name ?? 'Subscription Plan',
            'ipn_url' => route('coinpayments.ipn'),
            'custom' => json_encode([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]),
        ];

        $result = $this->apiRequest($params);

        // Save pending transaction BEFORE redirecting user
        Transaction::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gateway' => 'coinpayments',
            'amount' => $amount,
            'currency' => strtoupper($currency),
            'original_amount' => $plan->price,
            'original_currency' => 'USD',
            'reference' => $result['txn_id'] ?? null,
            'status' => 'pending',
        ]);

        // Extract the checkout URL. Prefer the one returned by the API,
        // otherwise build it from the transaction id + merchant id.
        if (!empty($result['checkout_url'])) {
            return $result['checkout_url'];
        }

        $txnId = $result['txn_id'] ?? '';

        return 'https://www.coinpayments.net/index.php?cmd=_pay&txn_id='
            . urlencode($txnId) . '&merchant=' . urlencode($this->merchantId)
            . '&item_name=' . urlencode($plan->name ?? 'Subscription')
            . '&currency1=' . urlencode(strtoupper($currency))
            . '&amountf=' . urlencode((string) $amount);
    }
}