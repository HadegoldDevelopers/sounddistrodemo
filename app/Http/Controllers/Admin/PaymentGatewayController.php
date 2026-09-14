<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Services\AudioScannerService;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    // Show form
    public function edit()
    {
        $gateways = PaymentGateway::all();
        $scanner = app(AudioScannerService::class);
        $scannerEnabled = $scanner->enabled();
        $acrHost = env('ACR_HOST', 'https://identify-us-east.acrcloud.com');

        return view('admin.settings.paymentsettings', compact('gateways', 'scannerEnabled', 'acrHost'));
    }

    // Update settings
    public function update(Request $request)
    {
        $input = $request->input('gateways', []);

        foreach ($input as $name => $data) {

            $gateway = PaymentGateway::where('name', '=', $name, 'and')->first();
            if (!$gateway) continue;

            // Basic fields
            $gateway->enabled = isset($data['enabled']);
            $gateway->mode = $data['mode'] ?? 'sandbox';
            $gateway->note = $data['note'] ?? null;

            // Remove non-setting fields
            $settings = collect($data)->except(['enabled', 'mode', 'note'])->toArray();

            // Encrypt sensitive fields ONLY if not already encrypted
            foreach ($settings as $key => $value) {

                // Skip empty values
                if (empty($value)) {
                    continue;
                }

                // Detect sensitive fields
                $isSensitive =
                    str_contains($key, 'secret') ||
                    str_contains($key, 'key') ||
                    str_contains($key, 'token') ||
                    str_contains($key, 'auth') ||
                    str_contains($key, 'merchant');

                if ($isSensitive) {

                    // Detect if already encrypted (Laravel encrypted strings start with "eyJpdiI6")
                    $alreadyEncrypted = is_string($value) && str_starts_with($value, 'eyJpdiI6');

                    // Encrypt ONLY if not already encrypted
                    if (!$alreadyEncrypted) {
                        $settings[$key] = encrypt($value);
                    }
                }
            }

            // Save updated settings
            $gateway->settings = $settings;
            $gateway->save();
        }

        return redirect()->back()->with('success', 'Payment gateways updated successfully.');
    }
}
