<?php

namespace App\Http\Controllers;

use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PublicLeadController extends Controller
{
    protected LeadService $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    /**
     * Store a public lead enquiry.
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->has('phone')) {
            $request->merge([
                'phone' => \App\Models\User::normalizePhone($request->phone)
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:50',
            'email'        => 'required|email|max:255',
            'lead_type'    => 'required|string|in:contact,advertiser,location_partner,digital_signage,sales_partner',
            'company_name' => 'nullable|string|max:255',
            'message'      => 'nullable|string',
            'source'       => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        if (empty($data['source'])) {
            $data['source'] = 'website';
        }

        // Duplicate Protection (within 24 hours for the same email or phone and lead_type)
        $duplicate = \App\Models\Lead::where('lead_type', $data['lead_type'])
            ->where(function ($query) use ($data) {
                $query->where('email', $data['email'])
                      ->orWhere('phone', $data['phone']);
            })
            ->where('created_at', '>=', now()->subHours(24))
            ->first();

        if ($duplicate) {
            return response()->json([
                'success'   => true,
                'message'   => 'Thank you! Your enquiry has been received. Our team will contact you shortly.',
                'lead_code' => $duplicate->lead_code,
            ], 200);
        }

        try {
            $lead = $this->leadService->createLead($data);

            return response()->json([
                'success'   => true,
                'message'   => 'Thank you! Your enquiry has been received. Our team will contact you shortly.',
                'lead_code' => $lead->lead_code,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again later.'
            ], 500);
        }
    }
}
