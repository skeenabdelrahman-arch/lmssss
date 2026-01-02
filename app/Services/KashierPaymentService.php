<?php

namespace App\Services;

class KashierPaymentService
{
    private $merchantId;
    private $apiKey;
    private $secretKey;
    private $mode;
    private $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('services.kashier.merchant_id');
        $this->apiKey = config('services.kashier.api_key');
        $this->secretKey = config('services.kashier.secret_key');
        $this->mode = config('services.kashier.mode', 'test');
        $this->baseUrl = 'https://checkout.kashier.io';
        
        // Use secret_key for hash generation, fallback to api_key for backward compatibility
        if (empty($this->secretKey) && !empty($this->apiKey)) {
            $this->secretKey = $this->apiKey;
        }
    }

    /**
     * Generate Kashier Order Hash
     * Based on official Kashier PaymentUI Demo
     */
    public function generateOrderHash($orderId, $amount, $currency, $customerReference = null)
    {
        $mid = $this->merchantId; // Should be in format MID-XXX-XXX
        $secret = $this->secretKey; // Use Secret Key for hash generation
        
        // Ensure amount has exactly 2 decimal places (no thousands separator)
        $amount = number_format((float)$amount, 2, '.', '');
        
        // Build path: /?payment=MID.orderId.amount.currency
        $path = "/?payment=" . $mid . "." . $orderId . "." . $amount . "." . $currency;
        
        // Add CustomerReference if provided
        if (!empty($customerReference)) {
            $path .= "." . $customerReference;
        }
        
        // Generate hash using HMAC SHA256 with Secret Key
        return hash_hmac('sha256', $path, $secret, false);
    }

    /**
     * Generate Payment URL for Kashier
     */
    public function generatePaymentUrl($orderId, $amount, $currency, $customerReference = null, $additionalParams = [])
    {
        // Validate merchant ID format
        if (!preg_match('/^MID-[0-9]+-[0-9]+$/', $this->merchantId)) {
            throw new \Exception('Invalid merchant ID format. Should be MID-XXX-XXX');
        }

        // Ensure amount has exactly 2 decimal places
        $amount = number_format((float)$amount, 2, '.', '');
        
        // Build payment path
        $path = "/?payment=" . $this->merchantId . "." . $orderId . "." . $amount . "." . $currency;
        if (!empty($customerReference)) {
            $path .= "." . $customerReference;
        }
        
        // Generate hash
        $hash = $this->generateOrderHash($orderId, $amount, $currency, $customerReference);
        
        // Build payment URL
        $paymentUrl = $this->baseUrl . $path . '&hash=' . $hash;
        
        // Add additional parameters
        if (!empty($additionalParams)) {
            $queryString = http_build_query($additionalParams);
            $paymentUrl .= '&' . $queryString;
        }
        
        return $paymentUrl;
    }

    /**
     * Generate iFrame HTML for embedded payment
     */
    public function generateIframeHtml($orderId, $amount, $currency, $customerReference = null, $additionalParams = [])
    {
        // Ensure amount has exactly 2 decimal places
        $amount = number_format((float)$amount, 2, '.', '');
        
        // Generate hash
        $hash = $this->generateOrderHash($orderId, $amount, $currency, $customerReference);
        
        // Build data attributes
        $dataAttributes = [
            'data-amount' => $amount,
            'data-description' => $additionalParams['description'] ?? 'Payment',
            'data-mode' => $this->mode,
            'data-hash' => $hash,
            'data-currency' => $currency,
            'data-orderId' => $orderId,
            'data-allowedMethods' => $additionalParams['allowedMethods'] ?? 'card',
            'data-merchantId' => $this->merchantId,
            'data-store' => $additionalParams['store'] ?? config('app.name', 'Store'),
            'data-type' => 'external',
            'data-display' => $additionalParams['display'] ?? 'ar',
        ];
        
        // Add redirect URL if provided
        if (isset($additionalParams['merchantRedirect'])) {
            $dataAttributes['data-merchantRedirect'] = $additionalParams['merchantRedirect'];
        }
        
        // Build script tag
        $attributes = '';
        foreach ($dataAttributes as $key => $value) {
            $attributes .= $key . '="' . htmlspecialchars($value, ENT_QUOTES) . '" ';
        }
        
        return '<script id="kashier-iFrame" src="https://checkout.kashier.io/kashier-checkout.js" ' . trim($attributes) . '></script>';
    }

    /**
     * Verify payment signature from callback
     */
    public function verifySignature($orderId, $paymentStatus, $paymentId, $signature)
    {
        // Build the signature string: orderId + paymentStatus + paymentId
        $signatureString = $orderId . $paymentStatus . $paymentId;
        
        // Generate expected signature using Secret Key
        $expectedSignature = hash_hmac('sha256', $signatureString, $this->secretKey, false);
        
        // Compare signatures
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify payment from callback request
     */
    public function verifyPayment($request)
    {
        $orderId = $request->input('orderId');
        $paymentStatus = $request->input('paymentStatus');
        $paymentId = $request->input('paymentId');
        $signature = $request->input('signature');

        if (empty($orderId) || empty($paymentStatus)) {
            return [
                'success' => false,
                'message' => 'Missing required parameters'
            ];
        }

        // Verify signature if provided
        if ($signature) {
            $isValid = $this->verifySignature($orderId, $paymentStatus, $paymentId ?? '', $signature);
            if (!$isValid) {
                \Log::warning('Invalid payment signature', [
                    'orderId' => $orderId,
                    'paymentStatus' => $paymentStatus
                ]);
            }
        }

        // Check payment status
        $isSuccess = in_array(strtoupper($paymentStatus), ['SUCCESS', 'PAID']);

        return [
            'success' => $isSuccess,
            'payment_id' => $paymentId ?? $orderId,
            'orderId' => $orderId,
            'paymentStatus' => $paymentStatus,
            'message' => $isSuccess ? 'Payment successful' : 'Payment failed or pending'
        ];
    }
}


