@extends('admin.layouts.app', [
    'title' => 'Blog Management | Hyper Adz Admin'
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <i class="bi bi-journal-text text-[#1155CC]"></i> Blog Control Center
            </h1>
            <p class="text-sm text-slate-550 dark:text-slate-400 mt-1">Create, edit, schedule, and feature blog articles on the public website.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('blog.index') }}" target="_blank" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 border border-slate-200/50 dark:border-slate-700">
                <i class="bi bi-eye"></i> Live Blog
            </a>
            <a href="{{ route('admin.blogs.create') }}" class="px-4 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all flex items-center gap-2 shadow-lg shadow-blue-500/10">
                <i class="bi bi-plus-lg"></i> Add Blog Post
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

    <!-- Status Filters & Search Grid -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
        <!-- Tabs -->
        <div class="flex items-center border-b border-slate-100 dark:border-slate-700 overflow-x-auto pb-px">
            @php
                $currentStatus = request('status');
            @endphp
            <a href="{{ route('admin.blogs.index') }}" class="px-4 py-2.5 border-b-2 text-xs font-bold uppercase tracking-wider transition-all {{ is_null($currentStatus) ? 'border-[#1155CC] text-[#1155CC]' : 'border-transparent text-slate-400 dark:text-slate-500 hover:text-slate-650' }}">
                All Posts
            </a>
            <a href="{{ route('admin.blogs.index', ['status' => 'published']) }}" class="px-4 py-2.5 border-b-2 text-xs font-bold uppercase tracking-wider transition-all {{ $currentStatus === 'published' ? 'border-[#1155CC] text-[#1155CC]' : 'border-transparent text-slate-400 dark:text-slate-500 hover:text-slate-650' }}">
                Published
            </a>
            <a href="{{ route('admin.blogs.index', ['status' => 'draft']) }}" class="px-4 py-2.5 border-b-2 text-xs font-bold uppercase tracking-wider transition-all {{ $currentStatus === 'draft' ? 'border-[#1155CC] text-[#1155CC]' : 'border-transparent text-slate-400 dark:text-slate-500 hover:text-slate-650' }}">
                Draft
            </a>
            <a href="{{ route('admin.blogs.index', ['status' => 'archived']) }}" class="px-4 py-2.5 border-b-2 text-xs font-bold uppercase tracking-wider transition-all {{ $currentStatus === 'archived' ? 'border-[#1155CC] text-[#1155CC]' : 'border-transparent text-slate-400 dark:text-slate-500 hover:text-slate-650' }}">
                Archived
            </a>
        </div>

        <form method="GET" action="{{ route('admin.blogs.index') }}" class="flex flex-wrap items-end gap-4">
            @if(request()->has('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <!-- Search input -->
            <div class="space-y-1.5 min-w-[280px] flex-1">
                <label class="text-[11px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider">Search Articles</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or author name..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC] transition-all">
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex gap-2">
                <button type="submit" class="py-2.5 px-5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-1.5 shadow-md shadow-blue-500/10">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.blogs.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 rounded-xl text-sm font-semibold transition-all flex items-center justify-center border border-slate-200/50 dark:border-slate-700" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Blogs List Table -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700/50 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-850/20">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider w-36">Author</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider w-40">Publish Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider w-24">Featured</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider w-28">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-750">
                    @forelse($blogs as $blog)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/5 transition-all text-xs text-slate-700 dark:text-slate-350">
                            <!-- Title & Image -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-900 border overflow-hidden shrink-0 flex items-center justify-center">
                                        @if($blog->featured_image)
                                            <img src="{{ asset('storage/' . $blog->featured_image) }}" class="w-full h-full object-cover" alt="">
                                        @else
                                            <i class="bi bi-image text-slate-400 text-lg"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 dark:text-slate-200 line-clamp-1" title="{{ $blog->title }}">{{ $blog->title }}</div>
                                        <div class="text-xxs text-slate-450 dark:text-slate-400 mt-0.5">Slug: {{ $blog->slug }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Author -->
                            <td class="px-6 py-4 font-semibold text-slate-550 dark:text-slate-400">
                                {{ $blog->author_name }}
                            </td>

                            <!-- Publish Date -->
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                @if($blog->publish_date)
                                    <div>{{ $blog->publish_date->format('M d, Y H:i') }}</div>
                                    @if($blog->publish_date->isFuture() && $blog->status === 'published')
                                        <span class="px-1.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded text-xxs font-bold uppercase tracking-wider">Scheduled</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">Immediate</span>
                                @endif
                            </td>

                            <!-- Featured (Single Star Toggle) -->
                            <td class="px-6 py-4">
                                <button type="button" onclick="toggleFaqFeature({{ $blog->id }}, this)" class="w-8 h-8 rounded-lg flex items-center justify-center transition-all {{ $blog->is_featured ? 'text-amber-500 bg-amber-50/50 hover:bg-amber-100' : 'text-slate-400 bg-slate-50/50 hover:bg-slate-150 dark:bg-slate-800' }}">
                                    <i class="bi {{ $blog->is_featured ? 'bi-star-fill' : 'bi-star' }} text-base"></i>
                                </button>
                            </td>

                            <!-- Status Toggle -->
                            <td class="px-6 py-4">
                                <button type="button" onclick="toggleFaqStatus({{ $blog->id }}, this)" class="px-2.5 py-1 rounded-full text-xxs font-bold transition-all border {{ $blog->status === 'published' ? 'bg-emerald-50 text-emerald-700 border-emerald-250 hover:bg-emerald-100' : ($blog->status === 'draft' ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' : 'bg-slate-50 text-slate-650 border-slate-250 hover:bg-slate-100 dark:bg-slate-750 dark:border-slate-700') }}">
                                    {{ ucfirst($blog->status) }}
                                </button>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition-all border border-slate-200/50 dark:bg-slate-800 dark:border-slate-700" title="Preview Article">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="w-8 h-8 rounded-lg bg-blue-50/50 hover:bg-blue-100/50 text-[#1155CC] flex items-center justify-center transition-all border border-blue-100/20" title="Edit Article">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50/50 hover:bg-rose-100/50 text-rose-600 flex items-center justify-center transition-all border border-rose-100/20" title="Delete Article">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-journal-text text-2xl block mb-2 opacity-35"></i>
                                <span>No blog articles found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($blogs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-750 bg-slate-50/30 dark:bg-slate-850/10">
                {{ $blogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleFaqStatus(id, button) {
        fetch(`/admin/blogs/${id}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'published') {
                button.textContent = 'Published';
                button.className = 'px-2.5 py-1 rounded-full text-xxs font-bold transition-all border bg-emerald-50 text-emerald-700 border-emerald-250 hover:bg-emerald-100';
            } else {
                button.textContent = 'Draft';
                button.className = 'px-2.5 py-1 rounded-full text-xxs font-bold transition-all border bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100';
            }
        });
    }

    function toggleFaqFeature(id, button) {
        fetch(`/admin/blogs/${id}/toggle-feature`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            // Reloader if featured to dynamically update status of other stars since only 1 can be featured
            window.location.reload();
        });
    }
</script>
@endpush
