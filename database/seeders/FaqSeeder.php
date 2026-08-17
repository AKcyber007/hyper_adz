<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // Assign manage-faqs permission to Admin role
        $permission = Permission::firstOrCreate(['name' => 'manage-faqs', 'guard_name' => 'web']);
        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'web')->first();
        if ($adminRole && !$adminRole->hasPermissionTo('manage-faqs')) {
            $adminRole->givePermissionTo($permission);
        }

        $categories = [
            [
                'name' => 'General',
                'slug' => 'general',
                'description' => 'General information about Hyper Adz platform.',
                'display_order' => 1,
                'faqs' => [
                    [
                        'question' => 'What is Hyper Adz?',
                        'answer' => '<p>Hyper Adz is an outdoor advertising platform that connects advertisers with premium advertising locations. Businesses can discover advertising locations, submit campaigns, upload creatives, and run advertising campaigns through a managed approval process.</p>',
                        'display_order' => 1,
                    ],
                    [
                        'question' => 'How does Hyper Adz work?',
                        'answer' => '<p>The process is simple:</p><ol><li>Submit Enquiry</li><li>Account Approval</li><li>Create Campaign</li><li>Upload Creative</li><li>Admin Review</li><li>Payment</li><li>Campaign Goes Live</li><li>Receive Performance Report</li></ol>',
                        'display_order' => 2,
                    ],
                    [
                        'question' => 'Who can use Hyper Adz?',
                        'answer' => '<p>Hyper Adz serves two primary user groups:</p><ul><li><strong>Advertisers</strong> looking to promote their products or services.</li><li><strong>Location Partners</strong> who own or manage locations suitable for advertising.</li></ul>',
                        'display_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Advertisers',
                'slug' => 'advertisers',
                'description' => 'Information for advertisers using the Hyper Adz platform.',
                'display_order' => 2,
                'faqs' => [
                    [
                        'question' => 'How do I become an advertiser?',
                        'answer' => '<p>Visit the <a href="/contact">Contact page</a> and click "Advertise With Us". Fill out the enquiry form and submit your details. Once approved by our team, you will receive access to the Advertiser Portal.</p>',
                        'display_order' => 1,
                    ],
                    [
                        'question' => 'Can I create an account myself?',
                        'answer' => '<p>No. Hyper Adz follows an approval-based onboarding process. All advertiser accounts are reviewed and approved by the admin team before access is granted.</p>',
                        'display_order' => 2,
                    ],
                    [
                        'question' => 'How do I start an advertising campaign?',
                        'answer' => '<p>After login:</p><ol><li>Select an available advertising location.</li><li>Choose your campaign dates.</li><li>Upload your advertising creative.</li><li>Submit for approval.</li></ol>',
                        'display_order' => 3,
                    ],
                    [
                        'question' => 'Can I select individual screens?',
                        'answer' => '<p>No. Hyper Adz operates on a location-based model. Advertisers select locations, not individual screens.</p>',
                        'display_order' => 4,
                    ],
                    [
                        'question' => 'Can I book a location that is under maintenance?',
                        'answer' => '<p>No. Locations marked as <strong>Inactive</strong> or <strong>Maintenance</strong> cannot be booked.</p>',
                        'display_order' => 5,
                    ],
                    [
                        'question' => 'How is campaign pricing calculated?',
                        'answer' => '<p>Pricing is based on:</p><p><strong>Location Daily Rate × Number of Campaign Days = Total Campaign Cost</strong></p>',
                        'display_order' => 6,
                    ],
                    [
                        'question' => 'Can I edit a campaign after submission?',
                        'answer' => '<p>Only if the campaign is returned for revision or rejected by the admin team.</p>',
                        'display_order' => 7,
                    ],
                    [
                        'question' => 'What happens if my campaign is rejected?',
                        'answer' => '<p>The admin will provide review comments explaining the required changes. You can update the campaign and resubmit it for approval.</p>',
                        'display_order' => 8,
                    ],
                    [
                        'question' => 'When do I make payment?',
                        'answer' => '<p>Payment is requested only after your campaign and creative have been approved.</p>',
                        'display_order' => 9,
                    ],
                    [
                        'question' => 'What happens if I don\'t complete payment?',
                        'answer' => '<p>Campaigns that remain unpaid before the required deadline may be automatically cancelled or rejected.</p>',
                        'display_order' => 10,
                    ],
                    [
                        'question' => 'Can I view my campaign status?',
                        'answer' => '<p>Yes. You can track the complete campaign lifecycle from your Advertiser Dashboard.</p>',
                        'display_order' => 11,
                    ],
                    [
                        'question' => 'What campaign statuses can I see?',
                        'answer' => '<ul><li>Submitted</li><li>Creative Review</li><li>Approved</li><li>Payment Pending</li><li>Payment Completed</li><li>Scheduled</li><li>Running</li><li>Completed</li><li>Report Uploaded</li><li>Rejected</li></ul>',
                        'display_order' => 12,
                    ],
                    [
                        'question' => 'Will I receive a campaign performance report?',
                        'answer' => '<p>Yes. After campaign completion, the admin team uploads the campaign report, which can be viewed and downloaded from the campaign page.</p>',
                        'display_order' => 13,
                    ],
                ],
            ],
            [
                'name' => 'Location Partners',
                'slug' => 'location-partners',
                'description' => 'Information for location partners joining the Hyper Adz network.',
                'display_order' => 3,
                'faqs' => [
                    [
                        'question' => 'How do I become a Location Partner?',
                        'answer' => '<p>Visit the <a href="/contact">Contact page</a> and click "Become a Location Partner". Complete the enquiry form and submit your information.</p>',
                        'display_order' => 1,
                    ],
                    [
                        'question' => 'Can I add locations immediately after registration?',
                        'answer' => '<p>No. Location Partners are approved by the admin team before gaining access to the partner portal.</p>',
                        'display_order' => 2,
                    ],
                    [
                        'question' => 'Can I add new advertising locations?',
                        'answer' => '<p>Yes. You can submit location requests through the Location Map section. All new locations require admin approval before becoming active.</p>',
                        'display_order' => 3,
                    ],
                    [
                        'question' => 'Can I modify my location details?',
                        'answer' => '<p>Yes. Location Partners can request:</p><ul><li>Price changes</li><li>Description updates</li><li>Photo updates</li><li>Status changes</li><li>New location additions</li></ul><p>All requests are reviewed by the admin team before implementation.</p>',
                        'display_order' => 4,
                    ],
                    [
                        'question' => 'Can I directly publish location changes?',
                        'answer' => '<p>No. Hyper Adz uses an approval-based workflow to ensure data quality and operational control.</p>',
                        'display_order' => 5,
                    ],
                    [
                        'question' => 'Can I view campaigns running at my locations?',
                        'answer' => '<p>Yes. The Location Partner Dashboard provides visibility into campaigns currently running or scheduled at your approved locations.</p>',
                        'display_order' => 6,
                    ],
                ],
            ],
            [
                'name' => 'Locations & Network',
                'slug' => 'locations-network',
                'description' => 'Questions about the advertising location network.',
                'display_order' => 4,
                'faqs' => [
                    [
                        'question' => 'How do locations appear on the Network page?',
                        'answer' => '<p>Only approved and active locations are displayed on the public Network page.</p>',
                        'display_order' => 1,
                    ],
                    [
                        'question' => 'Why can\'t I find a location on the Network page?',
                        'answer' => '<p>The location may be:</p><ul><li>Pending approval</li><li>Inactive</li><li>Under maintenance</li><li>Removed from public visibility</li></ul>',
                        'display_order' => 2,
                    ],
                    [
                        'question' => 'How are locations created?',
                        'answer' => '<p>Locations are added using an interactive map. The address and coordinates are automatically captured when a location is selected.</p>',
                        'display_order' => 3,
                    ],
                    [
                        'question' => 'Can locations have multiple photos?',
                        'answer' => '<p>Yes. Multiple photos can be uploaded to better showcase each advertising location.</p>',
                        'display_order' => 4,
                    ],
                ],
            ],
            [
                'name' => 'Login & OTP',
                'slug' => 'login-otp',
                'description' => 'Questions about login and OTP verification.',
                'display_order' => 5,
                'faqs' => [
                    [
                        'question' => 'How do I log in?',
                        'answer' => '<p>Hyper Adz uses OTP-based authentication. Enter your registered phone number or email, and a one-time verification code will be sent to you.</p>',
                        'display_order' => 1,
                    ],
                    [
                        'question' => 'Do I need a password?',
                        'answer' => '<p>No. Hyper Adz does not use traditional passwords. Login is performed using OTP verification.</p>',
                        'display_order' => 2,
                    ],
                    [
                        'question' => 'Can I log in using +91 and without +91?',
                        'answer' => '<p>Yes. The system automatically normalizes Indian phone numbers, so <strong>+919994206375</strong> and <strong>9994206375</strong> are treated as the same number.</p>',
                        'display_order' => 3,
                    ],
                    [
                        'question' => 'Can I be both an Advertiser and a Location Partner?',
                        'answer' => '<p>Yes. The same mobile number and email can be associated with both an Advertiser profile and a Location Partner profile.</p>',
                        'display_order' => 4,
                    ],
                ],
            ],
            [
                'name' => 'Payments',
                'slug' => 'payments',
                'description' => 'Payment-related questions for campaigns.',
                'display_order' => 6,
                'faqs' => [
                    [
                        'question' => 'When is payment required?',
                        'answer' => '<p>Payment is required only after campaign approval.</p>',
                        'display_order' => 1,
                    ],
                    [
                        'question' => 'Will I receive an invoice?',
                        'answer' => '<p>Yes. Payment records and invoices are maintained within the platform.</p>',
                        'display_order' => 2,
                    ],
                    [
                        'question' => 'Is my campaign scheduled immediately after payment?',
                        'answer' => '<p>Once payment is verified, the campaign moves to the <strong>Scheduled</strong> state and will automatically begin on the configured start date.</p>',
                        'display_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Campaign Reports',
                'slug' => 'campaign-reports',
                'description' => 'Questions about campaign reporting and analytics.',
                'display_order' => 7,
                'faqs' => [
                    [
                        'question' => 'How do I access campaign reports?',
                        'answer' => '<p>After campaign completion, the report becomes available within the campaign details page.</p>',
                        'display_order' => 1,
                    ],
                    [
                        'question' => 'Can I download reports?',
                        'answer' => '<p>Yes. Reports can be downloaded directly from the campaign page.</p>',
                        'display_order' => 2,
                    ],
                    [
                        'question' => 'What information is included in a report?',
                        'answer' => '<p>Depending on the campaign, reports may include:</p><ul><li>Campaign details</li><li>Campaign duration</li><li>Location information</li><li>Creative information</li><li>Campaign completion records</li><li>Supporting documentation and media provided by the admin team</li></ul>',
                        'display_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Support',
                'slug' => 'support',
                'description' => 'Contact and support information.',
                'display_order' => 8,
                'faqs' => [
                    [
                        'question' => 'How can I contact Hyper Adz?',
                        'answer' => '<p>You can use the <a href="/contact">Contact page</a> on the website to submit enquiries, support requests, or partnership applications.</p>',
                        'display_order' => 1,
                    ],
                    [
                        'question' => 'How long does approval take?',
                        'answer' => '<p>Approval times vary based on verification requirements. The Hyper Adz team reviews all advertiser and location partner applications before granting access.</p>',
                        'display_order' => 2,
                    ],
                    [
                        'question' => 'Who do I contact if I face an issue with my campaign?',
                        'answer' => '<p>You can contact the Hyper Adz support team through the <a href="/contact">Contact page</a> or through the support options available in your dashboard.</p>',
                        'display_order' => 3,
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $faqs = $categoryData['faqs'];
            unset($categoryData['faqs']);

            $category = FaqCategory::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            foreach ($faqs as $faqData) {
                Faq::updateOrCreate(
                    ['faq_category_id' => $category->id, 'question' => $faqData['question']],
                    array_merge($faqData, ['faq_category_id' => $category->id])
                );
            }
        }
    }
}
