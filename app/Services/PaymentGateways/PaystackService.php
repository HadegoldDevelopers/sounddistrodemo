<?php

namespace App\Services\PaymentGateways;

use App\Models\PaymentGateway;
use App\Models\SubscriptionPlan;
use App\Models\User;

use App\Services\CurrencyConverter;
use Illuminate\Support\Facades\Http;

class PaystackService
{
   const SUPPORTED_CURRENCIES = ['NGN'];

    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = $this->resolveSecretKey();
    }

    protected function resolveSecretKey()
    {
        $gateway = PaymentGateway::where('name', 'paystack')->firstOrFail();
        $settings = $gateway->settings ?? [];

        if (empty($settings['secret_key'])) {
            throw new \Exception('Paystack secret key is not configured.');
        }

        return decrypt($settings['secret_key']);
    }
    
  public function initiatePayment(User $user, float $amount, string $currency, SubscriptionPlan $plan): string
    {
        $currency = strtoupper($currency);
        $gatewayCurrency = in_array($currency, self::SUPPORTED_CURRENCIES) ? $currency : 'NGN';

       $gatewayAmount = $gatewayCurrency === $currency
    ? $amount
    : convertCurrency($amount, $currency, 'NGN');

        $amountInMinorUnit = (int) round($gatewayAmount * 100);

        $response = Http::withToken($this->secretKey)->post(
            'https://api.paystack.co/transaction/initialize',
            [
                'email' => $user->email,
                'amount' => $amountInMinorUnit,
                'currency' => $gatewayCurrency,
                'callback_url' => route('payment.callback', ['gateway' => 'paystack']),
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'original_amount' => $amount,
                    'original_currency' => $currency,
                ],
            ]
        );

        $data = $response->json();

        if (! $response->successful() || empty($data['data']['authorization_url'])) {
            throw new \Exception($data['message'] ?? 'Paystack initialization failed.');
        }

        return $data['data']['authorization_url'];
    }


  public function verifyTransaction(string $reference): array
  {
    $response = Http::withToken($this->secretKey)
      ->get("https://api.paystack.co/transaction/verify/{$reference}");

    if (!$response->successful()) {
      Log::error('Paystack verification failed', [
        'reference' => $reference,
        'response' => $response->body(),
      ]);
      throw new \Exception('Failed to verify Paystack transaction.');
    }

    $data = $response->json();

    if (($data['data']['status'] ?? null) !== 'success') {
      Log::warning('Paystack transaction not successful', [
        'reference' => $reference,
        'status' => $data['data']['status'] ?? 'undefined',
      ]);
      throw new \Exception('Transaction not successful.');
    }

    return $data['data'];
  }
}
