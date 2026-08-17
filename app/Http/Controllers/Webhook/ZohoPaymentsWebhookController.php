<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Campaign;
use App\Models\User;
use App\Notifications\CampaignPaidNotification;
use App\Services\ZohoPaymentsService;

class ZohoPaymentsWebhookController extends Controller
{
    protected ZohoPaymentsService $zohoService;

    public function __construct(ZohoPaymentsService $zohoService)
    {
        $this->zohoService = $zohoService;
    }

    public function handle(Request $request)
    {
        // 1. Get raw payload and signature
        $payload = $request->getContent();
        $signature = $request->header('X-Zoho-Payments-Signature'); // Adjust header name based on Zoho docs

        if (!$signature) {
            Log::warning('Zoho Webhook rejected: Missing Signature Header.');
            return response()->json(['error' => 'Missing Signature'], 401);
        }

        // 2. Verify Signature
        if (!$this->zohoService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Zoho Webhook rejected: Invalid Signature.', ['payload' => $payload]);
            return response()->json(['error' => 'Invalid Signature'], 401);
        }

        // 3. Parse JSON
        $data = json_decode($payload, true);
        if (!$data) {
            return response()->json(['error' => 'Invalid JSON'], 400);
        }

        // Handle Event
        $eventType = $data['event_type'] ?? '';
        
        // Typical Zoho Payments success events: 'payment_link.completed' or 'payment.success'
        if (in_array($eventType, ['payment_link.completed', 'payment.success'])) {
            return $this->handlePaymentSuccess($data);
        }

        return response()->json(['status' => 'Event ignored']);
    }

    protected function handlePaymentSuccess(array $data)
    {
        $paymentData = $data['data'] ?? [];
        $paymentLinkId = $paymentData['payment_link_id'] ?? null;
        $referenceId = $paymentData['reference_id'] ?? null;
        $paymentId = $paymentData['payment_id'] ?? null;

        if (!$paymentLinkId && !$referenceId) {
            Log::error('Zoho Webhook Error: Missing identifiers in payload.', ['data' => $data]);
            return response()->json(['error' => 'Missing identifiers'], 400);
        }

        DB::beginTransaction();
        try {
            // 4. Find Campaign
            $query = Campaign::query();
            if ($paymentLinkId) {
                $query->where('zoho_payment_link_id', $paymentLinkId);
            } else {
                $campaignId = str_replace('campaign-', '', $referenceId);
                $query->where('id', $campaignId);
            }

            // Lock for update to prevent race conditions (idempotency)
            $campaign = $query->lockForUpdate()->first();

            if (!$campaign) {
                Log::error('Zoho Webhook Error: Campaign not found.', ['payment_link_id' => $paymentLinkId, 'reference_id' => $referenceId]);
                DB::rollBack();
                return response()->json(['error' => 'Campaign not found'], 404);
            }

            // 5. Idempotency Check
            if ($campaign->payment_status === 'Paid' || $campaign->zoho_payment_id === $paymentId) {
                Log::info('Zoho Webhook: Duplicate event ignored.', ['campaign_id' => $campaign->id]);
                DB::rollBack();
                return response()->json(['status' => 'Already processed']);
            }

            // 6. Verify Amount (Optional but recommended)
            $receivedAmount = $paymentData['amount'] ?? 0;
            if ((float)$receivedAmount < (float)$campaign->payment_amount) {
                Log::warning('Zoho Webhook Warning: Partial payment received.', ['campaign_id' => $campaign->id, 'expected' => $campaign->payment_amount, 'received' => $receivedAmount]);
                // Depending on business rules, we might not mark as Paid. 
                // For this implementation, we will proceed or flag it.
            }

            // 7. Update Campaign
            $campaign->update([
                'status' => 'Scheduled',
                'payment_status' => 'Paid',
                'zoho_payment_id' => $paymentId,
                'payment_paid_at' => now(),
                'payment_confirmed_at' => now(),
                'payment_confirmed_by' => null, // Automated
            ]);

            // 8. Record Activity Log
            $campaign->activityLogs()->create([
                'action' => 'Payment Received',
                'performed_by' => 'System (Zoho Payments)',
                'remarks' => 'Online payment verified successfully via Zoho webhook. Campaign is now scheduled.',
            ]);

            // 9. Notify Admin
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new CampaignPaidNotification($campaign));
            }

            DB::commit();
            return response()->json(['status' => 'Payment processed successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Zoho Webhook Processing Failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
