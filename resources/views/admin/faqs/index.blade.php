@extends('admin.layouts.app', [
    'title' => 'FAQ Management | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <i class="bi bi-question-circle-fill text-[#1155CC]"></i> FAQ Management
            </h1>
            <p class="text-sm text-slate-550 dark:text-slate-400 mt-1">Manage frequently asked questions, answers, and display orders for the public website.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.faq-categories.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 border border-slate-200/50 dark:border-slate-700">
                <i class="bi bi-tags-fill"></i> FAQ Categories
            </a>
            <a href="{{ route('admin.faqs.create') }}" class="px-4 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all flex items-center gap-2 shadow-lg shadow-blue-500/10">
                <i class="bi bi-plus-lg"></i> Add FAQ
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 dark:bg-emerald-950/10 dark:border-emerald-900/30 text-emerald-600 dark:text-emerald-450 rounded-2xl flex items-center gap-3 text-sm">
            <i class="bi bi-check-circle-fill text-lg shrink-0"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-100 dark:bg-red-950/10 dark:border-red-900/30 text-red-600 dark:text-red-450 rounded-2xl flex items-center gap-3 text-sm">
            <i class="bi bi-exclamation-triangle-fill text-lg shrink-0"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Filters Panel -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm">
        <form method="GET" action="{{ route('admin.faqs.index') }}" class="flex flex-wrap items-end gap-4">
            <!-- Category -->
            <div class="space-y-1.5 min-w-[200px]">
                <label class="text-[11px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider">Filter By Category</label>
                <select name="category" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex gap-2">
                <button type="submit" class="py-2.5 px-5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-1.5 shadow-md shadow-blue-500/10">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                @if(request()->has('category'))
                    <a href="{{ route('admin.faqs.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 rounded-xl text-sm font-semibold transition-all flex items-center justify-center border border-slate-200/50 dark:border-slate-700" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- FAQ Table -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-850/20">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider w-10">Sort</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Question</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider w-40">Category</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider w-24">Order</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider w-28">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-750" id="sortable-faqs">
                    @forelse($faqs as $faq)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all text-xs text-slate-700 dark:text-slate-350" data-id="{{ $faq->id }}">
                            <td class="px-6 py-4 text-center cursor-move text-slate-400 handle">
                                <i class="bi bi-grip-vertical text-lg"></i>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-200">
                                <div class="line-clamp-2" title="{{ $faq->question }}">{{ $faq->question }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-slate-100 dark:bg-slate-700 text-slate-650 dark:text-slate-300 rounded-md text-xxs font-bold uppercase tracking-wider">
                                    {{ $faq->category->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="display-order-text">{{ $faq->display_order }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <button type="button" onclick="toggleFaqStatus({{ $faq->id }}, this)" class="px-2.5 py-1 rounded-full text-xxs font-bold transition-all border {{ $faq->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-250 hover:bg-emerald-100' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100 dark:bg-slate-750 dark:border-slate-700 dark:text-slate-405' }}">
                                    {{ ucfirst($faq->status) }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="w-8 h-8 rounded-lg bg-blue-50/50 hover:bg-blue-100/50 text-[#1155CC] flex items-center justify-center transition-all border border-blue-100/20" title="Edit FAQ">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this FAQ?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50/50 hover:bg-rose-100/50 text-rose-600 flex items-center justify-center transition-all border border-rose-100/20" title="Delete FAQ">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-question-circle text-2xl block mb-2 opacity-35"></i>
                                <span>No FAQs found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($faqs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-750 bg-slate-50/30 dark:bg-slate-850/10">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    function toggleFaqStatus(id, button) {
        fetch(`/admin/faqs/${id}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'active') {
                button.textContent = 'Active';
                button.className = 'px-2.5 py-1 rounded-full text-xxs font-bold transition-all border bg-emerald-50 text-emerald-700 border-emerald-250 hover:bg-emerald-100';
            } else {
                button.textContent = 'Inactive';
                button.className = 'px-2.5 py-1 rounded-full text-xxs font-bold transition-all border bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100 dark:bg-slate-750 dark:border-slate-700 dark:text-slate-400';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('sortable-faqs');
        if (el) {
            Sortable.create(el, {
                handle: '.handle',
                animation: 150,
                onEnd: function () {
                    const order = [];
                    el.querySelectorAll('tr').forEach(row => {
                        order.push(row.dataset.id);
                    });

                    fetch('{{ route("admin.faqs.reorder") }}', {
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
                            el.querySelectorAll('tr').forEach((row, index) => {
                                const orderText = row.querySelector('.display-order-text');
                                if (orderText) {
                                    orderText.textContent = index + 1;
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
