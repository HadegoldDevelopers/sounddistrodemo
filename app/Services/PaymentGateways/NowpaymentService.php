<?php

namespace App\Services\PaymentGateways;

use App\Models\PaymentGateway;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NowpaymentService
{
  protected $apiKey;

  public function __construct()
  {
    $gateway = PaymentGateway::where('name', 'nowpayment')->firstOrFail();

    /** @var array $settings */
    $settings = is_array($gateway->settings) ? $gateway->settings : [];


    if (empty($settings['api_key'])) {
      throw new \Exception('API key for NowPayment is not set.');
    }

    $this->apiKey = decrypt($settings['api_key']);
  }

  /**
   * Create the invoice, persist a pending transaction (with a unique
   * order reference) and return the NOWPayments invoice URL.
   */
  public function initiatePayment($user, $amount, $currency, $plan)
  {
    // NOWPayments posts IPN notifications to this endpoint (POST).
    $callbackUrl = route('nowpayments.webhook');

    // Unique order id so every payment maps to exactly one transaction.
    $orderId = 'NP-' . \Illuminate\Support\Str::uuid();

    $response = Http::withHeaders([
      'x-api-key' => $this->apiKey,
    ])->post('https://api.nowpayments.io/v1/invoice', [
      'price_amount' => $amount,
      'price_currency' => $currency,
      'pay_currency' => 'btc',
      'is_fixed_rate'     => true,
      'ipn_callback_url' => $callbackUrl,
      'order_id' => $orderId,
      'order_description' => $plan->description ?? 'Subscription Payment',
    ]);

    if ($response->successful() && isset($response['invoice_url'])) {

      // Persist the pending transaction BEFORE redirecting the buyer so
      // incoming callbacks have a local record to match against.
      Transaction::create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'gateway' => 'nowpayment',
        'amount' => $amount,
        'currency' => $currency,
        'original_amount' => $plan->price,
        'original_currency' => 'USD',
        'reference' => $orderId,
        'status' => 'pending',
      ]);

      return $response['invoice_url'];
    }

    Log::error('NowPayments error', [
      'status' => $response->status(),
      'body' => $response->body(),
    ]);

    throw new \Exception('NowPayment payment initialization failed.');
  }
}