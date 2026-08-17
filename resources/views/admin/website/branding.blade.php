@extends('admin.layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-semibold text-black dark:text-white">
        Branding & Logos
    </h2>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
        <h3 class="font-medium text-black dark:text-white">
            Manage Website Assets
        </h3>
    </div>
    <form action="{{ route('admin.website.branding.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="p-6.5">
            <!-- Header Logo -->
            <div class="mb-6 flex flex-col gap-5 sm:flex-row">
                <div class="w-full sm:w-1/2">
                    <label class="mb-3 block text-black dark:text-white">
                        Header Logo
                    </label>
                    <input type="file" name="logo" class="w-full rounded-md border border-stroke p-3 outline-none transition file:mr-4 file:rounded file:border-[0.5px] file:border-stroke file:bg-[#EEEEEE] file:py-1 file:px-2.5 file:text-sm file:font-medium focus:border-primary file:focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:file:border-strokedark dark:file:bg-white/30 dark:file:text-white" />
                    <p class="mt-2 text-xs text-gray-500">Recommended size: 250x50. Max size: 5MB.</p>
                </div>
                <div class="w-full sm:w-1/2 flex items-center justify-center border border-dashed border-stroke p-4 rounded bg-gray-50 dark:bg-meta-4 dark:border-strokedark">
                    @if(isset($branding) && $branding->logo_path)
                        <img src="{{ asset('storage/' . $branding->logo_path) }}" alt="Header Logo" class="max-h-16 object-contain">
                    @else
                        <span class="text-sm text-gray-400">No logo uploaded</span>
                    @endif
                </div>
            </div>

            <!-- Footer Logo -->
            <div class="mb-6 flex flex-col gap-5 sm:flex-row">
                <div class="w-full sm:w-1/2">
                    <label class="mb-3 block text-black dark:text-white">
                        Footer Logo
                    </label>
                    <input type="file" name="footer_logo" class="w-full rounded-md border border-stroke p-3 outline-none transition file:mr-4 file:rounded file:border-[0.5px] file:border-stroke file:bg-[#EEEEEE] file:py-1 file:px-2.5 file:text-sm file:font-medium focus:border-primary file:focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:file:border-strokedark dark:file:bg-white/30 dark:file:text-white" />
                    <p class="mt-2 text-xs text-gray-500">Recommended size: 250x50 (Light text for dark backgrounds). Max size: 5MB.</p>
                </div>
                <div class="w-full sm:w-1/2 flex items-center justify-center border border-dashed border-stroke p-4 rounded bg-gray-800 dark:bg-meta-4 dark:border-strokedark">
                    @if(isset($branding) && $branding->footer_logo_path)
                        <img src="{{ asset('storage/' . $branding->footer_logo_path) }}" alt="Footer Logo" class="max-h-16 object-contain">
                    @else
                        <span class="text-sm text-gray-400">No footer logo uploaded</span>
                    @endif
                </div>
            </div>

            <!-- Favicon -->
            <div class="mb-6 flex flex-col gap-5 sm:flex-row">
                <div class="w-full sm:w-1/2">
                    <label class="mb-3 block text-black dark:text-white">
                        Favicon
                    </label>
                    <input type="file" name="favicon" class="w-full rounded-md border border-stroke p-3 outline-none transition file:mr-4 file:rounded file:border-[0.5px] file:border-stroke file:bg-[#EEEEEE] file:py-1 file:px-2.5 file:text-sm file:font-medium focus:border-primary file:focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:file:border-strokedark dark:file:bg-white/30 dark:file:text-white" />
                    <p class="mt-2 text-xs text-gray-500">Recommended size: 32x32 or 64x64. Max size: 2MB.</p>
                </div>
                <div class="w-full sm:w-1/2 flex items-center justify-center border border-dashed border-stroke p-4 rounded bg-gray-50 dark:bg-meta-4 dark:border-strokedark">
                    @if(isset($branding) && $branding->favicon_path)
                        <img src="{{ asset('storage/' . $branding->favicon_path) }}" alt="Favicon" class="w-8 h-8 object-contain">
                    @else
                        <span class="text-sm text-gray-400">No favicon uploaded</span>
                    @endif
                </div>
            </div>

            <button type="submit" class="flex w-full justify-center rounded bg-primary p-3 font-medium text-gray bg-[#1155CC] text-white hover:bg-opacity-90 transition">
                Save Assets
            </button>
        </div>
    </form>
</div>
@endsection
