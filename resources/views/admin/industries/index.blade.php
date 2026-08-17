@extends('admin.layouts.app', [
    'title' => 'Industry Lookup | Hyper Adz Admin'
])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Marketing Industries</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Configure industry segments used to categorize advertiser brands and target ad campaigns.</p>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 dark:bg-emerald-950/10 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-100 dark:bg-red-950/10 dark:border-red-900/30 text-red-650 dark:text-red-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left: Add Industry Form -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4 h-fit">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Add Industry Lookup</h3>
            
            <form method="POST" action="{{ route('admin.industries.store') }}" class="space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-550 dark:text-slate-400">Industry Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Real Estate" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                </div>

                <button type="submit" class="w-full py-2 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10 flex items-center justify-center gap-1.5">
                    <i class="bi bi-plus-lg"></i> Add Industry
                </button>
            </form>
        </div>

        <!-- Right: Industries List -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden md:col-span-2">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-850/20">
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Industry Name</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-750">
                        @forelse($industries as $ind)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all text-xs text-slate-700 dark:text-slate-350">
                                <!-- Inline edit / view form -->
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.industries.update', $ind->id) }}" id="update-form-{{ $ind->id }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ old('name', $ind->name) }}" required class="px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-805 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                                    </form>
                                </td>

                                <td class="px-6 py-4">
                                    <select name="status" form="update-form-{{ $ind->id }}" onchange="document.getElementById('update-form-{{ $ind->id }}').submit();" class="px-2 py-1 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-lg text-xxs font-bold text-slate-650 focus:outline-none">
                                        <option value="active" {{ $ind->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $ind->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="submit" form="update-form-{{ $ind->id }}" class="w-8 h-8 rounded-lg bg-blue-50/50 hover:bg-blue-100/50 text-[#1155CC] flex items-center justify-center transition-all border border-blue-100/20" title="Save Changes">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <form action="{{ route('admin.industries.destroy', $ind->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this industry?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50/50 hover:bg-rose-100/50 text-rose-600 flex items-center justify-center transition-all border border-rose-100/20" title="Delete Lookup">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-400">
                                    <i class="bi bi-tags text-2xl block mb-2 opacity-35"></i>
                                    <span>No industries configured.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($industries->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-750 bg-slate-50/30 dark:bg-slate-850/10">
                    {{ $industries->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
