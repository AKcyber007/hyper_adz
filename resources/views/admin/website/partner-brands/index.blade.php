@extends('admin.layouts.app', ['title' => 'Partner Brands Management'])

@section('content')
<div class="px-6 py-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Partner Brands</h1>
            <p class="text-slate-500 text-sm mt-1">Manage the brand logos displayed on the public website carousel.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-200">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-200">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Upload Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-8">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Upload New Brand Logo</h2>
        <form action="{{ route('admin.website.partner-brands.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-4 items-end">
            @csrf
            <div class="w-full sm:w-1/2">
                <label for="image" class="block text-sm font-semibold text-slate-700 mb-2">Select Image File (PNG, JPG, SVG, WebP)</label>
                <input type="file" name="image" id="image" accept="image/png, image/jpeg, image/svg+xml, image/webp" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl cursor-pointer p-1" required>
            </div>
            <div>
                <button type="submit" class="bg-[#1155CC] text-white px-6 py-2.5 rounded-xl font-medium text-sm hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/20">
                    <i class="bi bi-upload mr-2"></i> Upload Logo
                </button>
            </div>
        </form>
    </div>

    <!-- Logos Grid -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Currently Displayed Logos ({{ count($brands) }})</h2>
        
        @if(count($brands) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($brands as $brand)
                    <div class="border border-slate-200 rounded-xl p-4 flex flex-col items-center justify-between group hover:border-blue-300 transition-colors h-40">
                        <!-- Preview -->
                        <div class="h-20 w-full flex items-center justify-center mb-3">
                            <img src="{{ $brand['url'] }}" alt="{{ $brand['name'] }}" class="max-h-full max-w-full object-contain" style="filter: grayscale(100%);">
                        </div>
                        
                        <!-- Actions -->
                        <div class="w-full flex justify-between items-center mt-auto pt-2 border-t border-slate-100">
                            <span class="text-xs text-slate-500 truncate mr-2" title="{{ $brand['name'] }}">{{ Str::limit($brand['name'], 15) }}</span>
                            <form action="{{ route('admin.website.partner-brands.destroy', $brand['name']) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this logo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-1 bg-red-50 rounded-lg transition-colors">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                <i class="bi bi-image text-4xl text-slate-300 mb-3 block"></i>
                <h3 class="text-slate-600 font-medium">No brand logos uploaded yet.</h3>
                <p class="text-sm text-slate-500 mt-1">Upload logos above to display them on the public website.</p>
            </div>
        @endif
    </div>
</div>
@endsection
