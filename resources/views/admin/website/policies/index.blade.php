@extends('admin.layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-semibold text-black dark:text-white">
        Website Policies
    </h2>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="max-w-full overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="bg-gray-2 text-left dark:bg-meta-4">
                    <th class="py-4 px-4 font-medium text-black dark:text-white xl:pl-11">
                        Policy Name
                    </th>
                    <th class="py-4 px-4 font-medium text-black dark:text-white">
                        Type
                    </th>
                    <th class="py-4 px-4 font-medium text-black dark:text-white">
                        Status
                    </th>
                    <th class="py-4 px-4 font-medium text-black dark:text-white">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($policies as $policy)
                    <tr>
                        <td class="border-b border-[#eee] py-5 px-4 pl-9 dark:border-strokedark xl:pl-11">
                            <h5 class="font-medium text-black dark:text-white">{{ $policy->title }}</h5>
                        </td>
                        <td class="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                            <p class="text-black dark:text-white">{{ ucfirst($policy->type) }}</p>
                        </td>
                        <td class="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                            @if($policy->status === 'published')
                                <span class="inline-flex rounded-full bg-success bg-opacity-10 py-1 px-3 text-sm font-medium text-success">
                                    Published
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-warning bg-opacity-10 py-1 px-3 text-sm font-medium text-warning">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                            <div class="flex items-center space-x-3.5">
                                <a href="{{ route('admin.website.policies.edit', $policy->id) }}" class="inline-flex items-center justify-center rounded-md bg-[#1155CC] py-1 px-3 text-center text-xs font-medium text-white hover:bg-opacity-90">
                                    Edit Policy
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
