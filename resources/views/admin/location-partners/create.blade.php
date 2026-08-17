@extends('admin.layouts.app', [
    'title' => 'Add Location Partner | Hyper Adz Admin'
])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.location-partners.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 flex items-center justify-center transition-all">
            <i class="bi bi-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Add Location Partner</h1>
            <p class="text-xs text-slate-500 mt-0.5">Register a new venue location partner profile manually.</p>
        </div>
    </div>

    <!-- Error Block -->
    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-100 dark:bg-red-950/10 dark:border-red-900/30 text-red-655 dark:text-red-400 rounded-2xl text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5 mb-1 text-sm">
                <i class="bi bi-exclamation-triangle-fill"></i> Please fix the validation errors:
            </div>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.location-partners.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Core Profile Info -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-750 pb-2 mb-4">Core Partner Profile</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Company Name -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Company / Brand Name <span class="text-red-500">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" required placeholder="e.g. Brookefields Retail Group" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Contact Person -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Contact Person Name <span class="text-red-500">*</span></label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}" required placeholder="e.g. Ramesh Kumar" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Designation -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Designation</label>
                        <input type="text" name="designation" value="{{ old('designation') }}" placeholder="e.g. Operations Manager" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Website -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Website URL</label>
                        <input type="text" name="website" value="{{ old('website') }}" placeholder="www.venue.com" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Phone -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Phone Number <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="e.g. +91 99000 00000" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Email -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="partner@venue.com" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 2: Settings & Logo -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-750 pb-2 mb-4">Branding & Corporate Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- GST -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">GST Number</label>
                        <input type="text" name="gst_number" value="{{ old('gst_number') }}" placeholder="GST" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Status -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>

                    <!-- Logo file -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Brand Logo</label>
                        <input type="file" name="logo" class="w-full text-xs text-slate-550 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-[#1155CC] hover:file:bg-slate-200 cursor-pointer">
                        <span class="block text-[10px] text-slate-400">JPG, PNG. Max 5MB.</span>
                    </div>
                </div>
            </div>

            <!-- Section 3: Address -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-750 pb-2 mb-4">Partner Corporate Address</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Address Line 1</label>
                        <input type="text" name="address_line_1" value="{{ old('address_line_1') }}" placeholder="Suite number, building details..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Address Line 2</label>
                        <input type="text" name="address_line_2" value="{{ old('address_line_2') }}" placeholder="Area, Landmark..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mt-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="Coimbatore" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">State</label>
                        <input type="text" name="state" value="{{ old('state') }}" placeholder="Tamil Nadu" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Country</label>
                        <input type="text" name="country" value="{{ old('country', 'India') }}" placeholder="India" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Postal Code</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" placeholder="641001" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Internal Administrative Notes</label>
                <textarea name="notes" rows="3" placeholder="Add details..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-750 pt-6 mt-8">
                <a href="{{ route('admin.location-partners.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-755 dark:bg-slate-700 dark:hover:bg-slate-650 dark:text-slate-250 rounded-xl text-sm font-semibold transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all shadow-md shadow-blue-500/10 flex items-center gap-2">
                    <i class="bi bi-check-lg"></i> Save Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
