@extends('admin.layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-semibold text-black dark:text-white">
        Company Information
    </h2>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
        <h3 class="font-medium text-black dark:text-white">
            Update Company Information
        </h3>
    </div>
    <form action="{{ route('admin.website.content.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="p-6.5">
            <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black dark:text-white">
                        Company Name
                    </label>
                    <input type="text" name="company_name" value="{{ old('company_name', $settings->company_name ?? '') }}" placeholder="Enter company name" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
                </div>
                
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black dark:text-white">
                        GST Number
                    </label>
                    <input type="text" name="gst_number" value="{{ old('gst_number', $settings->gst_number ?? '') }}" placeholder="Enter GST number" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
                </div>
            </div>

            <div class="mb-6">
                <label class="mb-2.5 block text-black dark:text-white">
                    Company Description
                </label>
                <textarea rows="4" name="company_description" placeholder="Type company description" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">{{ old('company_description', $settings->company_description ?? '') }}</textarea>
            </div>
            
            <div class="mb-6">
                <label class="mb-2.5 block text-black dark:text-white">
                    Office Address
                </label>
                <textarea rows="3" name="address" placeholder="Type office address" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">{{ old('address', $settings->address ?? '') }}</textarea>
            </div>

            <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black dark:text-white">
                        Primary Email
                    </label>
                    <input type="email" name="primary_email" value="{{ old('primary_email', $settings->primary_email ?? '') }}" placeholder="Enter primary email address" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
                </div>
                
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black dark:text-white">
                        Secondary Email
                    </label>
                    <input type="email" name="secondary_email" value="{{ old('secondary_email', $settings->secondary_email ?? '') }}" placeholder="Enter secondary email address" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
                </div>
            </div>
            
            <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black dark:text-white">
                        Phone Number
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $settings->phone ?? '') }}" placeholder="Enter phone number" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
                </div>
                
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black dark:text-white">
                        WhatsApp Number
                    </label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $settings->whatsapp ?? '') }}" placeholder="Enter WhatsApp number" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
                </div>
            </div>

            <div class="mb-6">
                <label class="mb-2.5 block text-black dark:text-white">
                    Business Hours
                </label>
                <input type="text" name="business_hours" value="{{ old('business_hours', $settings->business_hours ?? '') }}" placeholder="e.g., Mon - Fri: 9:00 AM - 6:00 PM" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
            </div>

            <button type="submit" class="flex w-full justify-center rounded bg-primary p-3 font-medium text-gray bg-[#1155CC] text-white hover:bg-opacity-90 transition">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
