@extends('admin.layouts.app')

@section('title', 'Location Reviews Management')

@section('content')
<div class="px-6 py-8 mx-auto max-w-7xl">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Location Reviews</h1>
            <p class="text-sm text-slate-500 mt-1">Monitor and manage user reviews for locations.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 mb-6">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Location, User, or Content..." class="w-full text-sm p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Rating</label>
                <select name="rating" class="w-full text-sm p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Ratings</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end gap-2 lg:col-span-2">
                <button type="submit" class="flex-1 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-all">
                    Apply Filters
                </button>
                @if(request()->anyFilled(['search', 'rating']))
                    <a href="{{ route('admin.reviews.index') }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition-all border border-slate-200" title="Reset Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Reviews Table -->
    <div class="bg-white rounded-[24px] border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-bold text-xs uppercase tracking-wider">Reviewer</th>
                        <th class="px-6 py-4 font-bold text-xs uppercase tracking-wider">Location</th>
                        <th class="px-6 py-4 font-bold text-xs uppercase tracking-wider">Rating & Review</th>
                        <th class="px-6 py-4 font-bold text-xs uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 font-bold text-xs uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $review->user->name ?? 'Unknown User' }}</div>
                                <div class="text-xs text-slate-500">{{ $review->user->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $review->location->name ?? 'Deleted Location' }}</div>
                                <div class="text-[10px] text-slate-500 font-mono">{{ $review->location->location_code ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-normal min-w-[300px]">
                                <div class="text-amber-500 text-[10px] flex gap-0.5 mb-1">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                                <p class="text-xs text-slate-600 line-clamp-2" title="{{ $review->review }}">
                                    {{ $review->review }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-slate-700">{{ $review->created_at->format('M d, Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $review->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-700 flex items-center justify-center transition-colors" title="Delete Review">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i class="bi bi-star text-slate-400 text-xl"></i>
                                </div>
                                <p class="font-medium text-slate-900">No reviews found</p>
                                <p class="text-xs mt-1">Try adjusting your filters or search query.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reviews->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
