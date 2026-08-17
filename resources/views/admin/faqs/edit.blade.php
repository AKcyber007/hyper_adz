@extends('admin.layouts.app', [
    'title' => 'Edit FAQ | Hyper Adz Admin'
])

@push('styles')
<!-- Quill Rich Text Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 200px;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.faqs.index') }}" class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-650 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 flex items-center justify-center transition-all" title="Back to Listing">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit FAQ</h1>
            <p class="text-sm text-slate-550 dark:text-slate-400 mt-0.5">Modify the selected question and answer resource.</p>
        </div>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('admin.faqs.update', $faq->id) }}" id="faq-form" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Form Fields -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Details Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-info-circle text-[#1155CC]"></i> Basic Details
                    </h3>

                    <!-- Question -->
                    <div class="space-y-1.5">
                        <label for="question" class="text-xs font-bold text-slate-500 dark:text-slate-400">Question <span class="text-rose-500">*</span></label>
                        <input type="text" name="question" id="question" value="{{ old('question', $faq->question) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-[#1155CC]">
                        @error('question') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Answer (Rich Text Editor) -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Answer <span class="text-rose-500">*</span></label>
                        
                        <div class="bg-slate-50 dark:bg-slate-900 border border-slate-250 dark:border-slate-800 rounded-xl overflow-hidden">
                            <!-- Quill Toolbar and Container -->
                            <div id="editor-container"></div>
                        </div>

                        <!-- Hidden input to store editor contents -->
                        <input type="hidden" name="answer" id="answer-input" value="{{ old('answer', $faq->answer) }}">
                        @error('answer') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Meta & Status Options -->
            <div class="space-y-6">
                <!-- Taxonomy & Sorting -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-sliders text-[#1155CC]"></i> Settings
                    </h3>

                    <!-- Category -->
                    <div class="space-y-1.5">
                        <label for="faq_category_id" class="text-xs font-bold text-slate-500 dark:text-slate-400">Category <span class="text-rose-500">*</span></label>
                        <select name="faq_category_id" id="faq_category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC]">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('faq_category_id', $faq->faq_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('faq_category_id') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Display Order -->
                    <div class="space-y-1.5">
                        <label for="display_order" class="text-xs font-bold text-slate-500 dark:text-slate-400">Display Order</label>
                        <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $faq->display_order) }}" min="0" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC]">
                        @error('display_order') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status -->
                    <div class="space-y-1.5">
                        <label for="status" class="text-xs font-bold text-slate-500 dark:text-slate-400">Status <span class="text-rose-500">*</span></label>
                        <select name="status" id="status" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC]">
                            <option value="active" {{ old('status', $faq->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $faq->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Submit / Form Actions -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.faqs.index') }}" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-700 dark:text-white text-center rounded-xl text-xs font-bold transition-all border border-slate-200/50 dark:border-slate-600">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 py-2.5 bg-[#1155CC] hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-blue-500/10">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Quill Rich Text Editor JS -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Quill
        const quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'clean']
                ]
            }
        });

        // Set initial content if any old/current input exists
        const currentContent = document.getElementById('answer-input').value;
        if (currentContent) {
            quill.root.innerHTML = currentContent;
        }

        // On submit, sync editor contents to hidden input
        const form = document.getElementById('faq-form');
        form.addEventListener('submit', function () {
            const html = quill.root.innerHTML;
            document.getElementById('answer-input').value = html;
        });
    });
</script>
@endpush
