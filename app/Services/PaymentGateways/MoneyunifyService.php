<?php

namespace App\Services\PaymentGateways;

use App\Models\PaymentGateway;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoneyunifyService
{
    protected $authId;

    public function __construct()
    {
        $gateway = PaymentGateway::where('name', 'moneyunify')->firstOrFail();
        $settings = $gateway->settings ?? [];

        if (empty($settings['auth_id'])) {
            throw new \Exception('MoneyUnify auth_id is not configured.');
        }

        $this->authId = decrypt($settings['auth_id']);

        Log::info('MoneyUnify service initialized.');
    }

    public function initiatePayment(User $user, float $amount, string $currency, SubscriptionPlan $plan, string $phone): string 
    {

        // Convert +260 or 260 to local format
        $phone = preg_replace('/\D/', '', $phone);

// Convert 26097xxxxxxx → 097xxxxxxx
if (str_starts_with($phone, '260')) {
    $phone = '0' . substr($phone, 3);
}

// Validate Zambian mobile numbers
if (!preg_match('/^09\d{8}$/', $phone)) {
    throw new \Exception(
        'Please enter a valid Zambian mobile number (09XXXXXXXX).'
    );
}

        // Convert to ZMW
        $amountZMW = intval(round(convertCurrency($amount, $currency, 'ZMW')));

        Log::info('MoneyUnify payment request', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'phone' => $phone,
            'original_amount' => $amount,
            'original_currency' => $currency,
            'converted_amount' => $amountZMW,
            'converted_currency' => 'ZMW',
        ]);

        try {

            $response = Http::timeout(30)
                ->asForm()
                ->post(
                    'https://api.moneyunify.one/payments/request',
                    [
                        'from_payer' => $phone,
                        'amount' => $amountZMW,
                        'auth_id' => $this->authId,
                    ]
                );

        } catch (\Throwable $e) {

            Log::error('MoneyUnify HTTP request failed', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }

        $data = $response->json() ?? [];

        Log::info('MoneyUnify payment response', [
            'http_status' => $response->status(),
            'response' => $data,
        ]);

        if (!$response->successful() || empty($data['data']['transaction_id'])) {

            Log::error('MoneyUnify initialization failed', [
                'status' => $response->status(),
                'response' => $data,
            ]);

            throw new \Exception(
                $data['message'] ?? 'MoneyUnify initialization failed.'
            );
        }

        $transactionId = $data['data']['transaction_id'];

        Transaction::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gateway' => 'moneyunify',
            'amount' => $amount,
            'converted_amount' => $amountZMW,
            'currency' => $currency,
            'reference' => $transactionId,
            'status' => 'pending',
        ]);

        Log::info('MoneyUnify pending transaction created', [
            'reference' => $transactionId,
            'user_id' => $user->id,
        ]);

        return route('subscription.moneyunify.wait', [
            'transaction_id' => $transactionId,
        ]);
    }

    public function verifyTransaction(string $transactionId): array
    {
        Log::info('Verifying MoneyUnify transaction', [
            'transaction_id' => $transactionId,
        ]);

        try {

            $response = Http::timeout(30)
                ->asForm()
                ->post(
                    'https://api.moneyunify.one/payments/verify',
                    [
                        'transaction_id' => $transactionId,
                        'auth_id' => $this->authId,
                    ]
                );

        } catch (\Throwable $e) {

            Log::error('MoneyUnify verification request failed', [
                'transaction_id' => $transactionId,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }

        Log::info('MoneyUnify verification response', [
            'transaction_id' => $transactionId,
            'http_status' => $response->status(),
            'response' => $response->json(),
        ]);

        if (!$response->successful()) {

            Log::error('MoneyUnify verification failed', [
                'transaction_id' => $transactionId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Failed to verify MoneyUnify transaction.');
        }

        return $response->json();
    }
}