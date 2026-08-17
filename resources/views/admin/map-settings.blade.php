@extends('admin.layouts.app', [
    'title' => 'Map Settings | Hyper Adz Admin'
])

@section('content')
<div class="max-w-4xl space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <i class="bi bi-geo-fill text-[#1155CC]"></i> Map Settings
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Configure default OpenStreetMap visualizations and coordinate parameters for the advertising network.</p>
        </div>
    </div>

    <!-- Main Content Panel -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 border border-slate-100 dark:border-slate-700/50 shadow-sm space-y-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/20 text-[#1155CC] flex items-center justify-center">
                <i class="bi bi-map-fill text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Map Configuration Foundation</h3>
                <p class="text-sm text-slate-400">These settings will initialize and control Leaflet.js behavior on the network page in the next phase.</p>
            </div>
        </div>

        <!-- Configuration Settings Form Mockup -->
        <div class="border-t border-slate-100 dark:border-slate-800 pt-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Latitude Center -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Default Center Latitude</label>
                    <input type="text" value="11.0168" disabled class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-500 cursor-not-allowed">
                    <span class="block text-xs text-slate-400">Default latitude for centering Coimbatore (Tamil Nadu, India).</span>
                </div>

                <!-- Longitude Center -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Default Center Longitude</label>
                    <input type="text" value="76.9558" disabled class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-500 cursor-not-allowed">
                    <span class="block text-xs text-slate-400">Default longitude for centering Coimbatore (Tamil Nadu, India).</span>
                </div>

                <!-- Default Zoom -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Default Zoom Level</label>
                    <input type="number" value="12" disabled class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-500 cursor-not-allowed">
                    <span class="block text-xs text-slate-400">Default zoom level for map initialization (1 to 20).</span>
                </div>

                <!-- Map Tile Server Provider -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Tile Server URL</label>
                    <input type="text" value="https://tile.openstreetmap.org/{z}/{x}/{y}.png" disabled class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-500 cursor-not-allowed">
                    <span class="block text-xs text-slate-400">Standard OpenStreetMap tile server endpoint.</span>
                </div>
            </div>

            <!-- Future controls notice -->
            <div class="p-4 bg-blue-50/50 dark:bg-blue-950/10 border border-blue-100/50 dark:border-blue-900/30 rounded-2xl flex gap-3">
                <i class="bi bi-info-circle-fill text-[#1155CC] text-lg mt-0.5"></i>
                <div class="text-sm text-slate-600 dark:text-slate-400">
                    <span class="font-bold text-slate-850 dark:text-slate-200 block mb-0.5">Phase 3B Locations Integration</span>
                    When locations CRUD is implemented, admins will be able to customize these settings dynamically from this panel, toggle cluster views, and filter visibility options.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
