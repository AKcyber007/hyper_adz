@extends('admin.layouts.app', [
    'title' => 'Edit Advertiser | Hyper Adz Admin'
])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.advertisers.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 flex items-center justify-center transition-all">
            <i class="bi bi-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Advertiser: {{ $advertiser->advertiser_code }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Modify advertiser information, branding assets, and status details.</p>
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
        <form method="POST" action="{{ route('admin.advertisers.update', $advertiser->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Core Company Info -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-750 pb-2 mb-4">Core Corporate Profile</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Company Name -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Company / Brand Name <span class="text-red-500">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name', $advertiser->company_name) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Contact Person -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Primary Contact Person <span class="text-red-500">*</span></label>
                        <input type="text" name="contact_person" value="{{ old('contact_person', $advertiser->contact_person) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Phone -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Phone Number <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" value="{{ old('phone', $advertiser->phone) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Email -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $advertiser->email) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Industry Category -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Industry Sector <span class="text-red-500">*</span></label>
                        <select name="industry_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                            @foreach($industries as $ind)
                                <option value="{{ $ind->id }}" {{ old('industry_id', $advertiser->industry_id) == $ind->id ? 'selected' : '' }}>{{ $ind->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Website -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Website URL</label>
                        <input type="text" name="website" value="{{ old('website', $advertiser->website) }}" placeholder="www.company.com" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 2: Taxation & Logo -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-750 pb-2 mb-4">Branding & Corporate Settings</h3>
                
                @if($advertiser->logo_path)
                    <div class="flex items-center gap-4 p-4 bg-slate-55/40 dark:bg-slate-900/30 border border-slate-200/60 dark:border-slate-850 rounded-2xl mb-4 w-fit">
                        <img src="{{ Storage::url($advertiser->logo_path) }}" class="w-16 h-16 object-cover rounded-xl border">
                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-600 dark:text-slate-450">Active Brand Logo</span>
                            <label class="flex items-center gap-1.5 text-xs text-red-500 cursor-pointer hover:text-red-750 font-semibold">
                                <input type="checkbox" name="delete_logo" value="1" class="rounded text-red-500 focus:ring-red-500">
                                <span>Delete Logo</span>
                            </label>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- GST -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">GST Number</label>
                        <input type="text" name="gst_number" value="{{ old('gst_number', $advertiser->gst_number) }}" placeholder="GST" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>

                    <!-- Status -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                            <option value="pending" {{ old('status', $advertiser->status) === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                            <option value="active" {{ old('status', $advertiser->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $advertiser->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status', $advertiser->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>

                    <!-- Logo file -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Replace Company Logo</label>
                        <input type="file" name="logo" class="w-full text-xs text-slate-550 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-[#1155CC] hover:file:bg-slate-200 cursor-pointer">
                        <span class="block text-[10px] text-slate-450">JPG, PNG. Max 5MB.</span>
                    </div>
                </div>
            </div>

            <!-- Section 3: Address details -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-750 pb-2 mb-4">Corporate Office Address</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Address Line 1</label>
                        <input type="text" name="address_line_1" value="{{ old('address_line_1', $advertiser->address_line_1) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Address Line 2</label>
                        <input type="text" name="address_line_2" value="{{ old('address_line_2', $advertiser->address_line_2) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mt-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">City</label>
                        <input type="text" name="city" value="{{ old('city', $advertiser->city) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">State</label>
                        <input type="text" name="state" value="{{ old('state', $advertiser->state) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Country</label>
                        <input type="text" name="country" value="{{ old('country', $advertiser->country) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Postal Code</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $advertiser->postal_code) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-550 dark:text-slate-455">Internal Notes (Remarks)</label>
                <textarea name="notes" rows="3" placeholder="..." class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">{{ old('notes', $advertiser->notes) }}</textarea>
            </div>

            <!-- Submit actions -->
            <div class="flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-750 pt-6 mt-8">
                <a href="{{ route('admin.advertisers.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-755 dark:bg-slate-700 dark:hover:bg-slate-650 dark:text-slate-250 rounded-xl text-sm font-semibold transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all shadow-md shadow-blue-500/10 flex items-center gap-2">
                    <i class="bi bi-check-lg"></i> Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
