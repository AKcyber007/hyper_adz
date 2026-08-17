@extends('admin.layouts.app', [
    'title' => 'Edit Blog Article | Hyper Adz Admin'
])

@push('styles')
<!-- Quill Rich Text Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 320px;
        font-size: 0.95rem;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.blogs.index') }}" class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-650 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-350 flex items-center justify-center transition-all" title="Back to Listing">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Article</h1>
            <p class="text-sm text-slate-555 dark:text-slate-400 mt-0.5">Modify the blog post content, scheduled release date, or feature status.</p>
        </div>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('admin.blogs.update', $blog->id) }}" id="blog-form" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Content Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Core Details Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-file-earmark-text text-[#1155CC]"></i> Article Body
                    </h3>

                    <!-- Title -->
                    <div class="space-y-1.5">
                        <label for="title" class="text-xs font-bold text-slate-500 dark:text-slate-400">Article Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $blog->title) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-[#1155CC]">
                        @error('title') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Short Description -->
                    <div class="space-y-1.5">
                        <label for="short_description" class="text-xs font-bold text-slate-500 dark:text-slate-400">Short Description <span class="text-rose-500">*</span></label>
                        <textarea name="short_description" id="short_description" rows="3" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-[#1155CC]">{{ old('short_description', $blog->short_description) }}</textarea>
                        @error('short_description') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Content (Rich Text Editor) -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Full Blog Content <span class="text-rose-500">*</span></label>
                        
                        <div class="bg-slate-50 dark:bg-slate-900 border border-slate-250 dark:border-slate-800 rounded-xl overflow-hidden">
                            <div id="editor-container"></div>
                        </div>

                        <!-- Hidden input to store editor contents -->
                        <input type="hidden" name="content" id="content-input" value="{{ old('content', $blog->content) }}">
                        @error('content') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- SEO Options Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-base border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-search text-[#1155CC]"></i> Search Engine Optimization (SEO)
                    </h3>

                    <!-- SEO Title -->
                    <div class="space-y-1.5">
                        <label for="seo_title" class="text-xs font-bold text-slate-500 dark:text-slate-400">SEO Meta Title</label>
                        <input type="text" name="seo_title" id="seo_title" value="{{ old('seo_title', $blog->seo_title) }}" placeholder="Default: Matches Article Title" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-[#1155CC]">
                        @error('seo_title') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- SEO Description -->
                    <div class="space-y-1.5">
                        <label for="seo_description" class="text-xs font-bold text-slate-500 dark:text-slate-400">SEO Meta Description</label>
                        <textarea name="seo_description" id="seo_description" rows="3" placeholder="Default: Matches Short Description" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-[#1155CC]">{{ old('seo_description', $blog->seo_description) }}</textarea>
                        @error('seo_description') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Meta Details & Image -->
            <div class="space-y-6">
                <!-- Settings & Meta Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-sliders text-[#1155CC]"></i> Settings
                    </h3>

                    <!-- Status -->
                    <div class="space-y-1.5">
                        <label for="status" class="text-xs font-bold text-slate-500 dark:text-slate-400">Status <span class="text-rose-500">*</span></label>
                        <select name="status" id="status" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC]">
                            <option value="draft" {{ old('status', $blog->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $blog->status) === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status', $blog->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Publish Date (Optional) -->
                    <div class="space-y-1.5">
                        <label for="publish_date" class="text-xs font-bold text-slate-500 dark:text-slate-400">Publish Date (Schedule)</label>
                        <input type="datetime-local" name="publish_date" id="publish_date" value="{{ old('publish_date', $blog->publish_date ? $blog->publish_date->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC]">
                        <span class="text-slate-400 text-xxs block mt-0.5">Leave blank to publish immediately.</span>
                        @error('publish_date') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Author Name -->
                    <div class="space-y-1.5">
                        <label for="author_name" class="text-xs font-bold text-slate-500 dark:text-slate-400">Author Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="author_name" id="author_name" value="{{ old('author_name', $blog->author_name) }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC]">
                        @error('author_name') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Slug (Optional) -->
                    <div class="space-y-1.5">
                        <label for="slug" class="text-xs font-bold text-slate-500 dark:text-slate-400">Custom URL Slug</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $blog->slug) }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:border-[#1155CC]">
                        @error('slug') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Featured Blog Option -->
                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $blog->is_featured) ? 'checked' : '' }} class="w-4 h-4 text-[#1155CC] bg-slate-50 border-slate-200 rounded focus:ring-[#1155CC]">
                        <label for="is_featured" class="text-xs font-bold text-slate-650 dark:text-slate-400 cursor-pointer">Feature This Article</label>
                        @error('is_featured') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Featured Image Upload -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 text-sm border-b border-slate-100 dark:border-slate-750 pb-3 flex items-center gap-2">
                        <i class="bi bi-image text-[#1155CC]"></i> Featured Image
                    </h3>

                    <div class="space-y-3">
                        <div class="w-full h-40 bg-slate-50 dark:bg-slate-900 border border-dashed border-slate-250 dark:border-slate-800 rounded-2xl flex flex-col items-center justify-center overflow-hidden position-relative" id="image-preview-container">
                            @if($blog->featured_image)
                                <img src="{{ asset('storage/' . $blog->featured_image) }}" id="featured-image-preview" class="w-full h-full object-cover" alt="Preview">
                                <i class="bi bi-cloud-arrow-up text-slate-400 text-3xl mb-1.5 d-none" id="preview-placeholder-icon"></i>
                                <span class="text-xxs text-slate-500 font-bold d-none" id="preview-placeholder-text">Upload JPG or PNG</span>
                            @else
                                <i class="bi bi-cloud-arrow-up text-slate-400 text-3xl mb-1.5" id="preview-placeholder-icon"></i>
                                <span class="text-xxs text-slate-500 font-bold" id="preview-placeholder-text">Upload JPG or PNG</span>
                                <img src="" id="featured-image-preview" class="w-full h-full object-cover d-none" alt="Preview">
                            @endif
                        </div>

                        <input type="file" name="featured_image" id="featured_image" accept="image/*" class="w-full text-xxs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xxs file:font-semibold file:bg-blue-50 file:text-[#1155CC] hover:file:bg-blue-100 cursor-pointer">
                        @error('featured_image') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Submit / Actions -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.blogs.index') }}" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-700 dark:text-white text-center rounded-xl text-xs font-bold transition-all border border-slate-200/50 dark:border-slate-600">
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
        // Image preview handler
        const fileInput = document.getElementById('featured_image');
        const previewImg = document.getElementById('featured-image-preview');
        const placeholderIcon = document.getElementById('preview-placeholder-icon');
        const placeholderText = document.getElementById('preview-placeholder-text');

        if (fileInput) {
            fileInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        previewImg.src = event.target.result;
                        previewImg.classList.remove('d-none');
                        if (placeholderIcon) placeholderIcon.classList.add('d-none');
                        if (placeholderText) placeholderText.classList.add('d-none');
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        // Initialize Quill with customized toolbar including custom image handler
        const quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['table'],
                        ['link', 'image', 'video'],
                        ['clean']
                    ],
                    handlers: {
                        image: function() {
                            selectLocalImage();
                        }
                    }
                }
            }
        });

        function selectLocalImage() {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.style.display = 'none';
            document.body.appendChild(input);
            input.click();

            input.onchange = () => {
                const file = input.files[0];
                if (file) {
                    uploadImageToServer(file);
                }
                document.body.removeChild(input);
            };
        }

        function uploadImageToServer(file) {
            const formData = new FormData();
            formData.append('image', file);

            fetch('{{ route("admin.blogs.upload-image") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.url) {
                    let range = quill.getSelection();
                    const index = range ? range.index : quill.getLength();
                    quill.insertEmbed(index, 'image', data.url);
                }
            })
            .catch(error => {
                console.error('Error uploading image:', error);
            });
        }

        // Sync old content
        const currentContent = document.getElementById('content-input').value;
        if (currentContent) {
            quill.root.innerHTML = currentContent;
        }

        // Form submit sync
        const form = document.getElementById('blog-form');
        form.addEventListener('submit', function () {
            document.getElementById('content-input').value = quill.root.innerHTML;
        });
    });
</script>
@endpush
