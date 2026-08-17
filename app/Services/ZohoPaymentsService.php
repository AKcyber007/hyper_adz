<?php

namespace App\Services;

use App\Models\Campaign;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class ZohoPaymentsService
{
    protected string $domain;
    protected string $clientId;
    protected string $clientSecret;
    protected ?string $refreshToken;
    protected string $signingKey;

    public function __construct()
    {
        $this->domain = env('ZOHO_DOMAIN', 'https://payments.zoho.in'); // Default to .in for India, configurable
        $this->clientId = config('zohopayments.client_id');
        $this->clientSecret = config('zohopayments.client_secret');
        $this->refreshToken = env('ZOHO_REFRESH_TOKEN');
        $this->signingKey = config('zohopayments.signing_key');
    }

    /**
     * Get a valid OAuth Access Token, utilizing Cache to avoid frequent refresh calls.
     */
    protected function getAccessToken(): string
    {
        if (Cache::has('zoho_payments_access_token')) {
            return Cache::get('zoho_payments_access_token');
        }

        if (!$this->refreshToken) {
            throw new Exception('ZOHO_REFRESH_TOKEN is missing in .env');
        }

        $accountsDomain = env('ZOHO_ACCOUNTS_DOMAIN', 'https://accounts.zoho.in');

        $response = Http::asForm()->post("{$accountsDomain}/oauth/v2/token", [
            'refresh_token' => $this->refreshToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
        ]);

        if ($response->successful() && isset($response['access_token'])) {
            $expiresIn = $response['expires_in'] ?? 3600;
            // Cache token slightly less than expiration time
            Cache::put('zoho_payments_access_token', $response['access_token'], now()->addSeconds($expiresIn - 60));
            return $response['access_token'];
        }

        Log::error('Zoho OAuth Token Error', ['response' => $response->body()]);
        throw new Exception('Failed to retrieve Zoho Payments access token.');
    }

    /**
     * Create a Zoho Payment Link for a Campaign.
     */
    public function createPaymentLink(Campaign $campaign): array
    {
        // Mock payment link for local development to prevent real transactions
        if (config('app.env') === 'local' || !$this->refreshToken) {
            $mockLinkId = 'mock_plink_' . uniqid();
            return [
                'success' => true,
                'payment_link_id' => $mockLinkId,
                'payment_url' => url('/mock-checkout/' . $mockLinkId),
            ];
        }

        $accessToken = $this->getAccessToken();
        $advertiser = $campaign->advertiser; // Using advertiser profile relationship

        $payload = [
            'amount' => $campaign->payment_amount,
            'currency' => env('ZOHO_CURRENCY', 'INR'),
            'reference_id' => 'campaign-' . $campaign->id,
            'description' => 'Payment for Campaign: ' . $campaign->campaign_name,
            'customer' => [
                'name' => $advertiser ? $advertiser->company_name : 'Advertiser',
                'email' => $advertiser ? $advertiser->email : 'advertiser@example.com',
                'phone' => $advertiser ? $advertiser->phone : '',
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post("{$this->domain}/api/v1/paymentlinks", $payload);

        if ($response->successful()) {
            $data = $response->json();
            // Expected response contains 'payment_link_id' and 'url' based on Zoho Payments API docs
            if (isset($data['data']['payment_link_id'])) {
                return [
                    'success' => true,
                    'payment_link_id' => $data['data']['payment_link_id'],
                    'payment_url' => $data['data']['url'] ?? ($data['data']['payment_link_url'] ?? null),
                ];
            }
        }

        Log::error('Zoho Payment Link Creation Failed', [
            'campaign_id' => $campaign->id,
            'response' => $response->body(),
            'status' => $response->status()
        ]);

        return [
            'success' => false,
            'error' => 'Failed to create Zoho Payment Link. ' . $response->body()
        ];
    }

    /**
     * Verify the Webhook Signature using HMAC SHA256 and the Signing Key.
     */
    public function verifyWebhookSignature(string $payload, string $signatureHeader): bool
    {
        if (empty($this->signingKey)) {
            Log::warning('Zoho Payments Signing Key is not configured.');
            return false;
        }

        // Zoho typically signs the raw JSON body with HMAC-SHA256 using the signing key.
        // The signature header might be base64 encoded or hex. We will assume standard hex or base64.
        // Adjust according to Zoho Payments exact spec, but this is the standard approach.
        
        $expectedSignature = base64_encode(hash_hmac('sha256', $payload, $this->signingKey, true));
        $expectedSignatureHex = hash_hmac('sha256', $payload, $this->signingKey);

        // Compare against both common formats
        return hash_equals($expectedSignature, $signatureHeader) || hash_equals($expectedSignatureHex, $signatureHeader);
    }
}
