@extends('admin.layouts.app', [
    'title' => 'FAQ Categories | Hyper Adz Admin'
])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">FAQ Categories</h1>
            <p class="text-sm text-slate-550 dark:text-slate-400 mt-1">Manage the categories used to group frequently asked questions on the public website.</p>
        </div>
        <a href="{{ route('admin.faqs.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-800 dark:text-white rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5">
            <i class="bi bi-question-circle"></i> View All FAQs
        </a>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 dark:bg-emerald-950/10 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-100 dark:bg-red-950/10 dark:border-red-900/30 text-red-600 dark:text-red-400 rounded-2xl text-xs font-semibold flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left: Add Category Form -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm p-6 space-y-4 h-fit">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Add FAQ Category</h3>
            
            <form method="POST" action="{{ route('admin.faq-categories.store') }}" class="space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-550 dark:text-slate-400">Category Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. General" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-550 dark:text-slate-400">Description</label>
                    <textarea name="description" placeholder="Short description..." rows="3" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all"></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-550 dark:text-slate-400">Display Order</label>
                    <input type="number" name="display_order" value="0" min="0" class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                </div>

                <button type="submit" class="w-full py-2 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10 flex items-center justify-center gap-1.5">
                    <i class="bi bi-plus-lg"></i> Add Category
                </button>
            </form>
        </div>

        <!-- Right: FAQ Categories List -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden md:col-span-2">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left" id="categories-table">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-850/20">
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider w-10">Sort</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Category Details</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider w-24">FAQs Count</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider w-28">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right w-28">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-750" id="sortable-categories">
                        @forelse($categories as $cat)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all text-xs text-slate-700 dark:text-slate-350" data-id="{{ $cat->id }}">
                                <!-- Drag Handle or Sort Number -->
                                <td class="px-6 py-4 text-center cursor-move text-slate-400 handle">
                                    <i class="bi bi-grip-vertical text-lg"></i>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.faq-categories.update', $cat->id) }}" id="update-form-{{ $cat->id }}" class="space-y-2">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <input type="text" name="name" value="{{ old('name', $cat->name) }}" required class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-805 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                                        </div>
                                        <div>
                                            <input type="text" name="description" value="{{ old('description', $cat->description) }}" placeholder="No description" class="w-full px-2.5 py-1 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg text-xxs text-slate-550 dark:text-slate-400 focus:outline-none focus:border-[#1155CC] transition-all">
                                        </div>
                                        <div class="flex items-center gap-1 text-xxs">
                                            <span class="text-slate-450">Order:</span>
                                            <input type="number" name="display_order" value="{{ $cat->display_order }}" min="0" class="w-16 px-1.5 py-0.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded text-center focus:outline-none">
                                        </div>
                                    </form>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-500 dark:text-slate-400">
                                    {{ $cat->faqs_count }}
                                </td>

                                <td class="px-6 py-4">
                                    <select name="status" form="update-form-{{ $cat->id }}" onchange="document.getElementById('update-form-{{ $cat->id }}').submit();" class="px-2 py-1 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-lg text-xxs font-bold text-slate-650 dark:text-slate-350 focus:outline-none">
                                        <option value="active" {{ $cat->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $cat->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="submit" form="update-form-{{ $cat->id }}" class="w-8 h-8 rounded-lg bg-blue-50/50 hover:bg-blue-100/50 text-[#1155CC] flex items-center justify-center transition-all border border-blue-100/20" title="Save Changes">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <form action="{{ route('admin.faq-categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category? All FAQs in it must be deleted first.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50/50 hover:bg-rose-100/50 text-rose-600 flex items-center justify-center transition-all border border-rose-100/20" title="Delete Category" {{ $cat->faqs_count > 0 ? 'disabled style=opacity:0.4' : '' }}>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <i class="bi bi-tags text-2xl block mb-2 opacity-35"></i>
                                    <span>No FAQ categories found.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('sortable-categories');
        if (el) {
            Sortable.create(el, {
                handle: '.handle',
                animation: 150,
                onEnd: function () {
                    const order = [];
                    el.querySelectorAll('tr').forEach(row => {
                        order.push(row.dataset.id);
                    });

                    fetch('{{ route("admin.faq-categories.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update order inputs in table
                            el.querySelectorAll('tr').forEach((row, index) => {
                                const orderInput = row.querySelector('input[name="display_order"]');
                                if (orderInput) {
                                    orderInput.value = index + 1;
                                }
                            });
                        }
                    });
                }
            });
        }
    });
</script>
@endpush
