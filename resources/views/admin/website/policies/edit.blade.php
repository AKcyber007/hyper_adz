@extends('admin.layouts.app')

@push('styles')
<!-- Quill Rich Text Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 400px;
        font-size: 0.95rem;
    }
</style>
@endpush

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-semibold text-black dark:text-white">
        Edit {{ $policy->title }}
    </h2>
    <a href="{{ route('admin.website.policies.index') }}" class="inline-flex items-center justify-center rounded-md bg-gray-500 py-2 px-4 text-center font-medium text-white hover:bg-opacity-90">
        Back
    </a>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <form action="{{ route('admin.website.policies.update', $policy->id) }}" method="POST" id="policy-form">
        @csrf
        @method('PUT')
        
        <div class="p-6.5">
            <div class="mb-4.5">
                <label class="mb-2.5 block text-black dark:text-white">
                    Policy Title <span class="text-meta-1">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $policy->title) }}" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" required />
            </div>

            <div class="mb-6">
                <label class="mb-2.5 block text-black dark:text-white">
                    Status
                </label>
                <div class="relative z-20 bg-transparent dark:bg-form-input">
                    <select name="status" class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent py-3 px-5 outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        <option value="draft" {{ old('status', $policy->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $policy->status) === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="mb-2.5 block text-black dark:text-white">
                    Policy Content <span class="text-meta-1">*</span>
                </label>
                <div class="bg-slate-50 dark:bg-slate-900 border border-slate-250 dark:border-slate-800 rounded-xl overflow-hidden">
                    <div id="editor-container"></div>
                </div>

                <input type="hidden" name="content" id="content-input" value="{{ old('content', $policy->content) }}">
            </div>

            <button type="submit" class="flex w-full justify-center rounded bg-primary p-3 font-medium text-gray bg-[#1155CC] text-white hover:bg-opacity-90 transition">
                Update Policy
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Quill Rich Text Editor JS -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        // Sync old content
        const oldContent = document.getElementById('content-input').value;
        if (oldContent) {
            quill.root.innerHTML = oldContent;
        }

        // Form submit sync
        const form = document.getElementById('policy-form');
        form.addEventListener('submit', function () {
            document.getElementById('content-input').value = quill.root.innerHTML;
        });
    });
</script>
@endpush
