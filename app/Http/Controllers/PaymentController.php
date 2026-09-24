<?php

namespace App\Http\Controllers;

use App\Services\PaymentGateways\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;


use App\Models\SubscriptionPlan;
use App\Models\PaymentGateway;
use App\Models\Currency;
use App\Models\User;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;



class PaymentController extends Controller
{
    public function index()
{
    $user = Auth::user();
    $paymentGateways = PaymentGateway::where('enabled', true)->get();
    $paymentMethods = $paymentGateways->map(function ($gateway) {
    return [
        'name' => $gateway->name,
        'label' => $gateway->display_name,
        'note' => $gateway->note,
    ];
});
    // Get current subscription plan based on user role
    $currentPlan = SubscriptionPlan::where('role', $user->role)->first();
    $requireSubscription = (bool) Setting::getValue('require_subscription', 0);

    // Allow upgrade from artist to label only
    $upgradePlan = null;
if ($user->role === 'artist') {
    $upgradePlan = SubscriptionPlan::where('role', 'label')->first();
}

    // Fetch user's deposits/payment history
    $deposits = Transaction::where('user_id', $user->id)
                       ->orderBy('created_at', 'desc')
                       ->paginate(5);


    return view('user.subscription', [
        'user' => $user,
        'paymentMethods' => $paymentMethods,
        'currentPlan' => $currentPlan,
        'upgradePlan' => $upgradePlan,
        'deposits' => $deposits,
        'requireSubscription' => $requireSubscription,
    ]);
}

public function manualInstructions(Request $request)
{
    // Check if manual gateway is enabled
    $manualGateway = PaymentGateway::where('name', 'manual')->first();

    if (!$manualGateway || !$manualGateway->enabled) {
        return redirect()->route('payment.index')
            ->with('error', 'Manual payment is currently disabled.');
    }

    $user = auth()->user();

    // Load the pending transaction created in process() (POST). The page is
    // read-only — refreshing it never creates a new transaction, and the
    // amount/currency come from the stored record, never from the URL.
    $reference = $request->reference ?? $request->query('reference');

    $transaction = Transaction::where('reference', $reference)
        ->where('gateway', 'manual')
        ->where('user_id', $user->id)
        ->first();

    if (!$transaction) {
        return redirect()->route('payment.index')
            ->with('error', 'Manual payment reference not found.');
    }

    $plan = $transaction->subscription_plan;
    $manual = $manualGateway->settings;

    return view('user.payments.manual-instructions', [
        'plan'      => $plan,
        'amount'    => $transaction->amount,
        'currency'  => $transaction->currency,
        'reference' => $transaction->reference,
        'manual'    => (object) $manual,
        'user'      => $user,
    ]);
}

    
  public function process(Request $request)
{
    $request->validate([
        'payment_method' => 'required|string|exists:payment_gateways,name,enabled,1',
        'subscription_plan_id' => 'required|exists:subscription_plans,id',
    ]);

    $user = Auth::user();
    $plan = SubscriptionPlan::findOrFail($request->subscription_plan_id);
    $paymentMethod = $request->payment_method;

    // Convert currency FIRST (needed for manual + all gateways)
    $currency = \App\Services\CurrencyConverter::convert();

    // Guard against missing/zero conversion rates (would zero out or break
    // the price). Falls back to 1:1 (the USD base rate).
    $conversionRate = (float) ($currency->conversion_rate ?? 0);
    if ($conversionRate <= 0) {
        $conversionRate = 1.0;
    }

    $convertedAmount = $plan->price * $conversionRate;

    // Handle manual payment BEFORE gateway logic. The amount/currency are
    // always computed server-side from the plan — never trusted from the
    // request — and a pending transaction is created once via this POST.
    if ($paymentMethod === 'manual') {
        $reference = 'MAN-' . strtoupper(uniqid());

        Transaction::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gateway' => 'manual',
            'amount' => $convertedAmount,
            'currency' => $currency->code,
            'original_amount' => $plan->price,
            'original_currency' => 'USD',
            'reference' => $reference,
            'status' => 'pending',
        ]);

        return redirect()->route('payment.manual', ['reference' => $reference])
            ->with('success', 'Manual payment initiated. Follow instructions to complete.');
    }

    // Build gateway service class
    $serviceClass = "App\\Services\\PaymentGateways\\" . ucfirst($paymentMethod) . "Service";

    if (!class_exists($serviceClass)) {
        return back()->withErrors(['payment_method' => 'Unsupported payment method.']);
    }

    // MoneyUnify requires phone number
    if ($paymentMethod === 'moneyunify' && empty($request->moneyunify_phone)) {
        return back()->withErrors([
            'payment_method' => 'Please enter your mobile money number.'
        ]);
    }

    $service = app($serviceClass);

    try {
        if ($paymentMethod === 'moneyunify') {
            $paymentUrl = $service->initiatePayment(
                $user,
                $convertedAmount,
                $currency->code,
                $plan,
                $request->moneyunify_phone
            );
        } else {
            $paymentUrl = $service->initiatePayment(
                $user,
                $convertedAmount,
                $currency->code,
                $plan
            );
        }

    } catch (\Exception $e) {
        return back()->withErrors(['payment_method' => $e->getMessage()]);
    }

    return redirect($paymentUrl)->with('success', 'Redirecting to payment gateway...');
}


    
   public function handleCallback(Request $request, string $gateway)
{
    switch (strtolower($gateway)) {

        case 'paystack':
            return $this->handlePaystackCallback($request);

        case 'nowpayment':
            return $this->handleNowPaymentCallback($request);

        case 'paypal':
            return $this->handlePaypalCallback($request);

        default:
            return response()->json(['error' => 'Unsupported gateway'], 400);
    }
}

public function coinpaymentsIpn(Request $request)
{
    Log::info('CoinPayments IPN', $request->all());

    // 1. Verify the CoinPayments HMAC signature using the IPN secret.
    $service = app(\App\Services\PaymentGateways\CoinpaymentsService::class);
    $ipnSecret = $service->ipnSecret();

    if (empty($ipnSecret)) {
        Log::warning('CoinPayments IPN rejected: IPN secret not configured.');
        return response('IPN secret not configured', 503);
    }

    $signature = $request->header('Hmac');
    $expected = hash_hmac('sha512', $request->getContent(), $ipnSecret);

    if (empty($signature) || !hash_equals($expected, $signature)) {
        Log::warning('CoinPayments IPN rejected: invalid HMAC signature.');
        return response('Invalid signature', 400);
    }

    // 2. Verify the callback merchant id matches our configured merchant id.
    $callbackMerchant = (string) ($request->input('merchant') ?? '');
    $configuredMerchant = (string) $service->merchantId();

    if ($configuredMerchant === '' || $callbackMerchant === '' || !hash_equals($configuredMerchant, $callbackMerchant)) {
        Log::warning('CoinPayments IPN rejected: merchant id mismatch.', [
            'callback_merchant' => $callbackMerchant,
        ]);
        return response('Invalid merchant', 400);
    }

    // 2. Required fields.
    $txnId = $request->input('txn_id');
    $status = (int) $request->input('status');

    if (!$txnId) {
        return response('missing txn', 400);
    }

    // 3. Resolve the previously stored transaction. Never trust user/plan
    //    values sent in the callback — they come from our own record.
    $transaction = Transaction::where('reference', $txnId)
        ->where('gateway', 'coinpayments')
        ->first();

    if (!$transaction) {
        Log::warning('CoinPayments transaction not found for IPN', ['txn_id' => $txnId]);
        return response('not found', 404);
    }

    // Idempotency guard — never process an already-paid transaction again.
    if ($transaction->status === 'paid') {
        return response('already processed', 200);
    }

    // 4. CoinPayments status >= 100 (or 2) means the payment completed.
    if ($status >= 100 || $status === 2) {

        $user = $transaction->user;
        $plan = $transaction->plan;

        if (!$user || !$plan) {
            Log::error('CoinPayments IPN: invalid user or plan on stored transaction', [
                'transaction_id' => $transaction->id,
            ]);
            return response('invalid data', 400);
        }

        // Activate subscription
        if ($plan->role === 'label') {
            $user->role = 'label';
        }

        $user->update([
            'is_sub' => 1,
            'sub_expires_at' => $plan->expiryDate(),
        ]);

        $transaction->update([
            'status' => 'paid',
        ]);

        return response('ok', 200);
    }

    return response('ignored', 200);
}

private function handlePaystackCallback(Request $request)
{
    $reference = $request->input('reference') ?? $request->input('trxref');
    if (!$reference) {
        return redirect()->route('user.dashboard')->with('error', 'No reference provided.');
    }

    try {
        $paystackService = app(PaystackService::class);
        $transaction = $paystackService->verifyTransaction($reference);
        $metadata = $transaction['metadata'] ?? [];

        // Fetch user and plan
        $user = User::find($metadata['user_id'] ?? 0);
        $plan = SubscriptionPlan::find($metadata['plan_id'] ?? 0);

        if (!$user || !$plan) {
            throw new \Exception('Invalid user or plan.');
        }

        // Determine amounts
        $originalAmount = $metadata['original_amount'] ?? $transaction['amount'] / 100;
        $originalCurrency = $metadata['original_currency'] ?? $transaction['currency'] ?? 'NGN';

        $gatewayAmount = $transaction['amount'] / 100;
        $gatewayCurrency = $transaction['currency'] ?? 'NGN';
        
        if ($plan->role === 'label') { $user->role = 'label'; }
        
        // Update subscription
        $user->update([
            'is_sub' => 1,
            'sub_expires_at' => $plan->expiryDate(),
        ]);

        // Record payment
        Transaction::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gateway' => 'paystack',
            'amount' => $gatewayAmount,
            'currency' => $gatewayCurrency,
            'original_amount' => $originalAmount,
            'original_currency' => $originalCurrency,
            'reference' => $transaction['reference'] ?? $reference,
            'status' => 'paid',
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Payment confirmed!');
    } catch (\Exception $e) {
        logger()->error('Paystack callback failed', ['message' => $e->getMessage(), 'reference' => $reference]);
        return redirect()->route('user.dashboard')->with('error', 'Payment verification failed.');
    }
}

private function handlePaypalCallback(Request $request)
{
    $token = $request->query('orderId')
        ?? $request->query('token')
        ?? $request->query('paymentId');

    Log::info('PayPal Callback HIT', [
        'query' => $request->query(),
        'token_used' => $token,
    ]);

    if (!$token) {
        return redirect()->route('user.dashboard')
            ->with('error', 'Invalid PayPal callback.');
    }

    $paypal = app(\App\Services\PaymentGateways\PaypalService::class);
    $capture = $paypal->captureOrder($token);

    Log::info('PayPal Capture Response', $capture);

    if (($capture['status'] ?? null) !== 'COMPLETED') {
        return redirect()->route('user.dashboard')
            ->with('error', 'Payment not completed.');
    }

    // Find the pending transaction created before redirecting to PayPal
    $transaction = Transaction::where('reference', $token)
        ->where('gateway', 'paypal')
        ->first();

    if (!$transaction) {
        Log::error('Pending PayPal transaction not found', [
            'reference' => $token,
        ]);

        return redirect()->route('user.dashboard')
            ->with('error', 'Transaction not found.');
    }
if ($transaction->status === 'paid') {
    return redirect()->route('user.dashboard')
        ->with('success', 'Payment already confirmed.');
}
    $user = User::find($transaction->user_id);
    $plan = SubscriptionPlan::find($transaction->plan_id);

    if (!$user || !$plan) {
        Log::error('Invalid user or plan', [
            'user_id' => $transaction->user_id,
            'plan_id' => $transaction->plan_id,
        ]);

        return redirect()->route('user.dashboard')
            ->with('error', 'Invalid user or plan.');
    }

    if ($plan->role === 'label') {
        $user->role = 'label';
    }

    $user->update([
        'role' => $user->role,
        'is_sub' => 1,
        'sub_expires_at' => $plan->expiryDate(),
    ]);

    $captureData = $capture['purchase_units'][0]['payments']['captures'][0];

    $transaction->update([
        'amount' => $captureData['amount']['value'],
        'currency' => $captureData['amount']['currency_code'],
        'status' => 'paid',
    ]);

    Log::info('PayPal payment completed', [
        'reference' => $token,
        'user_id' => $user->id,
    ]);

    return redirect()->route('user.dashboard')
        ->with('success', 'Payment confirmed!');
}

public function handleNowPaymentCallback(Request $request)
{
    Log::info('NowPayments callback', $request->all());

    // Verify the HMAC-SHA512 signature NowPayments attaches to every IPN.
    $gateway = PaymentGateway::where('name', 'nowpayment')->first();
    $ipnSecret = $this->decryptGatewaySetting($gateway, 'ipn_secret');

    if (empty($ipnSecret)) {
        Log::error('NowPayments callback: no IPN secret configured');
        return response()->json(['error' => 'IPN secret not configured'], 503);
    }

    $signature = $request->header('x-nowpayments-sig');
    $expected = hash_hmac('sha512', $request->getContent(), $ipnSecret);

    if (empty($signature) || !hash_equals($expected, $signature)) {
        Log::warning('NowPayments callback: signature verification failed', [
            'order_id' => $request->input('order_id'),
        ]);
        return response()->json(['error' => 'Invalid signature'], 400);
    }

    $status = $request->input('payment_status');
    $orderId = $request->input('order_id');

    if (!in_array($status, ['confirmed', 'finished'])) {
        return response()->json(['message' => 'Payment not completed yet']);
    }

    $transaction = Transaction::where('reference', $orderId)
        ->where('gateway', 'nowpayment')
        ->first();

    if (!$transaction) {
        return response()->json(['error' => 'Transaction not found'], 404);
    }

    // Idempotency guard: never process an already-paid transaction again.
    if ($transaction->status === 'paid') {
        return response()->json(['message' => 'Payment already confirmed']);
    }

    $user = $transaction->user;
    $plan = $transaction->plan;

    if (!$user || !$plan) {
        return response()->json(['error' => 'Invalid user or plan'], 400);
    }

    // role update
    if ($plan->role === 'label') {
        $user->role = 'label';
        $user->save();
    }

    // activate subscription
    $user->update([
        'is_sub' => 1,
        'sub_expires_at' => $plan->expiryDate(),
    ]);

    // record payment
    $transaction->update([
        'status' => 'paid',
    ]);

    return response()->json(['message' => 'Payment confirmed']);
}

public function wait($transactionId)
{
    return view('user.moneyunify-wait', [
        'transactionId' => $transactionId
    ]);
}
        
    public function check($transactionId)
{
    $service = app(\App\Services\PaymentGateways\MoneyunifyService::class);
    $result = $service->verifyTransaction($transactionId);

    // Find the pending transaction
    $transaction = Transaction::where('reference', $transactionId)
        ->where('gateway', 'moneyunify')
        ->first();

    if (!$transaction) {
        return response()->json(['status' => 'failed']);
    }

    // Idempotency guard — never activate an already-processed payment again.
    if ($transaction->status === 'paid') {
        return response()->json(['status' => 'success']);
    }

    // If MoneyUnify says success
    if (($result['status'] ?? null) === 'success') {

        $user = $transaction->user;
        $plan = $transaction->plan;

        if (!$user || !$plan) {
            return response()->json(['status' => 'failed']);
        }

        // Activate subscription
        if ($plan->role === 'label') {
            $user->role = 'label';
        }

        $user->update([
            'is_sub' => 1,
            'sub_expires_at' => $plan->expiryDate(),
        ]);

        $transaction->update([
            'status' => 'paid'
        ]);
        return response()->json(['status' => 'success']);
    }
    
    if (($result['status'] ?? null) === 'failed') {
        $transaction->update(['status' => 'failed']);
        return response()->json(['status' => 'failed']);
    }
    
    return response()->json(['status' => 'pending']);
}

public function paypalWebhook(Request $request)
{
    Log::info('PayPal Webhook', $request->all());

    $paypal = app(\App\Services\PaymentGateways\PaypalService::class);

    if (!$paypal->getWebhookId()) {
        Log::error('PayPal webhook: no webhook ID configured');
        return response()->json(['error' => 'Webhook not configured'], 503);
    }

    // Verify the webhook signature with PayPal before trusting it.
    $verified = $paypal->verifyWebhook(
        $request->header('Paypal-Auth-Algo', ''),
        $request->header('Paypal-Cert-Url', ''),
        $request->header('Paypal-Transmission-Id', ''),
        $request->header('Paypal-Transmission-Sig', ''),
        $request->header('Paypal-Transmission-Time', ''),
        $request->getContent()
    );

    if (!$verified) {
        Log::warning('PayPal webhook: signature verification failed', [
            'transmission_id' => $request->header('Paypal-Transmission-Id'),
        ]);
        return response()->json(['error' => 'Webhook signature verification failed'], 400);
    }

    if ($request->event_type !== 'CHECKOUT.ORDER.APPROVED') {
        return response()->json(['ignored' => true]);
    }

    $orderId = $request->resource['id'];

    $capture = $paypal->captureOrder($orderId);

    if (($capture['status'] ?? null) !== 'COMPLETED') {
        return response()->json([
            'error' => 'Capture failed'
        ], 400);
    }

    $transaction = Transaction::where('reference', $orderId)
        ->where('gateway', 'paypal')
        ->first();

    if (!$transaction) {
        Log::error('Webhook: transaction not found', [
            'reference' => $orderId,
        ]);

        return response()->json([
            'error' => 'Transaction not found'
        ], 404);
    }

    // Idempotency guard: never process an already-paid transaction again.
    if ($transaction->status === 'paid') {
        return response()->json([
            'status' => 'success',
            'message' => 'Payment already confirmed',
        ]);
    }

    $user = User::find($transaction->user_id);
    $plan = SubscriptionPlan::find($transaction->plan_id);

    if (!$user || !$plan) {
        return response()->json([
            'error' => 'Invalid user or plan'
        ], 404);
    }

    if ($plan->role === 'label') {
        $user->role = 'label';
    }

    $user->update([
        'role' => $user->role,
        'is_sub' => 1,
        'sub_expires_at' => $plan->expiryDate(),
    ]);

    $captureData = $capture['purchase_units'][0]['payments']['captures'][0];

    $transaction->update([
        'amount' => $captureData['amount']['value'],
        'currency' => $captureData['amount']['currency_code'],
        'status' => 'paid',
    ]);

    return response()->json([
        'status' => 'success'
    ]);
}

public function cancel()
{
    return redirect()->route('user.dashboard')
        ->with('error', 'Payment was cancelled.');
}


/**
 * Read a value from a gateway's settings array, decrypting it if it is
 * an encrypted (sensitive) value.
 */
private function decryptGatewaySetting($gateway, string $key)
{
    if (!$gateway) {
        return null;
    }

    $settings = is_array($gateway->settings) ? $gateway->settings : [];
    $value = $settings[$key] ?? null;

    if (!is_string($value) || $value === '') {
        return null;
    }

    try {
        return decrypt($value);
    } catch (\Throwable $e) {
        return $value;
    }
}


}
