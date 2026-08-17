@extends('layouts.advertiser')

@section('title', 'Edit Company Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900 font-outfit">Edit Company Profile</h2>
            <p class="text-xs text-slate-500 mt-0.5">Update your registered advertiser business credentials.</p>
        </div>
        <a href="{{ route('advertiser.profile') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">
            Cancel
        </a>
    </div>

    <form action="{{ route('advertiser.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-[32px] p-6 sm:p-8 space-y-8 shadow-sm">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-100 text-rose-700 p-4 rounded-xl text-xs space-y-1">
                <div class="font-bold mb-2">Please fix the following errors:</div>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Logo upload -->
        <div class="flex items-center gap-6 pb-6 border-b border-slate-100">
            <div class="w-16 h-16 rounded-2xl overflow-hidden border border-slate-200 bg-slate-50 flex items-center justify-center text-2xl shadow-inner shrink-0 relative group">
                @if($profile->logo_path)
                    <img src="{{ Storage::url($profile->logo_path) }}" class="w-full h-full object-cover">
                @else
                    <i class="bi bi-building text-slate-400"></i>
                @endif
            </div>
            <div class="flex-grow">
                <label class="block text-xs font-bold text-slate-700 mb-1">Company Logo</label>
                <input type="file" name="logo" accept="image/*" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors">
                <p class="text-[10px] text-slate-400 mt-1">Max 2MB. Square image recommended.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Company Name *</label>
                <input type="text" name="company_name" value="{{ old('company_name', $profile->company_name) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Industry Sector</label>
                <select name="industry_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    <option value="">Select Industry</option>
                    @foreach($industries as $industry)
                        <option value="{{ $industry->id }}" {{ old('industry_id', $profile->industry_id) == $industry->id ? 'selected' : '' }}>
                            {{ $industry->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Contact Person *</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $profile->contact_person) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Phone Number *</label>
                <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Email Address *</label>
                <input type="email" name="email" value="{{ old('email', $profile->email) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Website URL</label>
                <input type="url" name="website" value="{{ old('website', $profile->website) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
            </div>
            <div class="space-y-1.5 col-span-2 sm:col-span-1">
                <label class="block text-xs font-bold text-slate-700">GST Number</label>
                <input type="text" name="gst_number" value="{{ old('gst_number', $profile->gst_number) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors uppercase">
            </div>
            
            <div class="col-span-2 pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-900 mb-4 text-sm">Address Details</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-1.5 col-span-2 sm:col-span-1">
                        <label class="block text-xs font-bold text-slate-700">Address Line 1 *</label>
                        <input type="text" name="address_line_1" value="{{ old('address_line_1', $profile->address_line_1) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    </div>
                    <div class="space-y-1.5 col-span-2 sm:col-span-1">
                        <label class="block text-xs font-bold text-slate-700">Address Line 2</label>
                        <input type="text" name="address_line_2" value="{{ old('address_line_2', $profile->address_line_2) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">City *</label>
                        <input type="text" name="city" value="{{ old('city', $profile->city) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">State *</label>
                        <input type="text" name="state" value="{{ old('state', $profile->state) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Postal Code *</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Country *</label>
                        <input type="text" name="country" value="{{ old('country', $profile->country) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                    </div>
                </div>
            </div>

            <div class="space-y-1.5 col-span-2 pt-4 border-t border-slate-100">
                <label class="block text-xs font-bold text-slate-700">Additional Notes</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">{{ old('notes', $profile->notes) }}</textarea>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-colors shadow-sm">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
