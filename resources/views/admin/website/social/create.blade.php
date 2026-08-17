@extends('admin.layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-semibold text-black dark:text-white">
        Add Social Media Link
    </h2>
    <a href="{{ route('admin.website.social-links.index') }}" class="inline-flex items-center justify-center rounded-md bg-gray-500 py-2 px-4 text-center font-medium text-white hover:bg-opacity-90">
        Back
    </a>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <form action="{{ route('admin.website.social-links.store') }}" method="POST">
        @csrf
        
        <div class="p-6.5">
            <div class="mb-4.5">
                <label class="mb-2.5 block text-black dark:text-white">
                    Platform Name <span class="text-meta-1">*</span>
                </label>
                <input type="text" name="platform" value="{{ old('platform') }}" placeholder="e.g., Facebook, Instagram, LinkedIn" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" required />
            </div>

            <div class="mb-4.5">
                <label class="mb-2.5 block text-black dark:text-white">
                    Profile URL
                </label>
                <input type="url" name="url" value="{{ old('url') }}" placeholder="https://facebook.com/yourpage" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
            </div>

            <div class="mb-6">
                <label class="mb-2.5 block text-black dark:text-white">
                    Status
                </label>
                <div class="relative z-20 bg-transparent dark:bg-form-input">
                    <select name="status" class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent py-3 px-5 outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="flex w-full justify-center rounded bg-primary p-3 font-medium text-gray bg-[#1155CC] text-white hover:bg-opacity-90 transition">
                Add Social Link
            </button>
        </div>
    </form>
</div>
@endsection
