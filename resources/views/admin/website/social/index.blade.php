@extends('admin.layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-semibold text-black dark:text-white">
        Social Media Links
    </h2>
    <a href="{{ route('admin.website.social-links.create') }}" class="inline-flex items-center justify-center rounded-md bg-[#1155CC] py-2 px-4 text-center font-medium text-white hover:bg-opacity-90">
        Add Social Link
    </a>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="max-w-full overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="bg-gray-2 text-left dark:bg-meta-4">
                    <th class="py-4 px-4 font-medium text-black dark:text-white xl:pl-11">
                        Platform
                    </th>
                    <th class="py-4 px-4 font-medium text-black dark:text-white">
                        URL
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
                @forelse($links as $link)
                    <tr>
                        <td class="border-b border-[#eee] py-5 px-4 pl-9 dark:border-strokedark xl:pl-11">
                            <h5 class="font-medium text-black dark:text-white">{{ $link->platform }}</h5>
                        </td>
                        <td class="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                            <p class="text-black dark:text-white truncate max-w-[200px]">{{ $link->url }}</p>
                        </td>
                        <td class="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                            @if($link->status)
                                <span class="inline-flex rounded-full bg-success bg-opacity-10 py-1 px-3 text-sm font-medium text-success">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-warning bg-opacity-10 py-1 px-3 text-sm font-medium text-warning">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                            <div class="flex items-center space-x-3.5">
                                <a href="{{ route('admin.website.social-links.edit', $link->id) }}" class="hover:text-primary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.website.social-links.destroy', $link->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this link?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="hover:text-danger text-red-500">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-gray-500">
                            No social media links found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
