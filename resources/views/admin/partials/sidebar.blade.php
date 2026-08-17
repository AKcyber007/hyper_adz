<aside class="fixed inset-y-0 left-0 w-64 bg-[#0A1628] text-slate-300 border-r border-slate-800 flex flex-col z-30">
    <!-- Brand Info -->
    <div class="h-16 flex items-center px-6 border-b border-slate-800">
        <a href="/" class="flex items-center gap-2.5">
            <!-- Icon -->
            <div class="w-8 h-8 rounded-lg bg-[#1155CC] flex items-center justify-center shadow-md shadow-blue-500/20">
                <i class="bi bi-cpu text-white text-lg"></i>
            </div>
            <div>
                <span class="font-bold text-white tracking-tight text-lg">Hyper Adz</span>
                <span class="block text-[10px] text-slate-500 font-semibold -mt-1">ADMIN PORTAL</span>
            </div>
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-6 overflow-y-auto space-y-6">
        <!-- Dashboard Section -->
        <div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i class="bi bi-grid-1x2-fill text-base"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 1. USER MANAGEMENT -->
        @if(auth()->user()->hasAnyPermission(['manage-advertisers', 'manage-location-partners', 'manage-users']))
        <div>
            <span class="px-4 text-[10px] font-bold text-slate-500 tracking-wider uppercase">User Management</span>
            <ul class="mt-2 space-y-1">
                <!-- Advertisers -->
                @can('manage-advertisers')
                <li class="space-y-1">
                    <div class="px-4 py-1.5 text-xs font-semibold text-slate-400 flex items-center gap-2">
                        <i class="bi bi-people-fill text-slate-500"></i>
                        <span>Advertisers</span>
                    </div>
                    <ul class="pl-6 space-y-1">
                        <li>
                            <a href="{{ route('admin.advertisers.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('admin.advertisers.index') && request('lead_type') !== 'advertiser' ? 'bg-[#1155CC]/20 text-white font-semibold' : 'hover:bg-slate-800/50 hover:text-slate-100 text-slate-400' }}">
                                <i class="bi bi-person-badge text-[10px]"></i>
                                <span>All Advertisers</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.leads.index', ['lead_type' => 'advertiser']) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('admin.leads.index') && request('lead_type') === 'advertiser' ? 'bg-[#1155CC]/20 text-white font-semibold' : 'hover:bg-slate-800/50 hover:text-slate-100 text-slate-400' }}">
                                <i class="bi bi-megaphone text-[10px]"></i>
                                <span>Advertiser Leads</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan

                <!-- Location Partners -->
                @can('manage-location-partners')
                <li class="space-y-1 mt-2">
                    <div class="px-4 py-1.5 text-xs font-semibold text-slate-400 flex items-center gap-2">
                        <i class="bi bi-building text-slate-500"></i>
                        <span>Location Partners</span>
                    </div>
                    <ul class="pl-6 space-y-1">
                        <li>
                            <a href="{{ route('admin.location-partners.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('admin.location-partners.index') && request('lead_type') !== 'location_partner' ? 'bg-[#1155CC]/20 text-white font-semibold' : 'hover:bg-slate-800/50 hover:text-slate-100 text-slate-400' }}">
                                <i class="bi bi-person-badge text-[10px]"></i>
                                <span>All Partners</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.leads.index', ['lead_type' => 'location_partner']) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('admin.leads.index') && request('lead_type') === 'location_partner' ? 'bg-[#1155CC]/20 text-white font-semibold' : 'hover:bg-slate-800/50 hover:text-slate-100 text-slate-400' }}">
                                <i class="bi bi-building-fill text-[10px]"></i>
                                <span>Partner Leads</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan

                <!-- Other Leads -->
                @can('manage-users')
                <li class="space-y-1 mt-2">
                    <div class="px-4 py-1.5 text-xs font-semibold text-slate-400 flex items-center gap-2">
                        <i class="bi bi-inbox text-slate-500"></i>
                        <span>Other Enquiries</span>
                    </div>
                    <ul class="pl-6 space-y-1">
                        <li>
                            <a href="{{ route('admin.leads.index', ['lead_type' => 'digital_signage']) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('admin.leads.index') && request('lead_type') === 'digital_signage' ? 'bg-[#1155CC]/20 text-white font-semibold' : 'hover:bg-slate-800/50 hover:text-slate-100 text-slate-400' }}">
                                <i class="bi bi-display text-[10px]"></i>
                                <span>Digital Signage Leads</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.leads.index', ['lead_type' => 'sales_partner']) }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('admin.leads.index') && request('lead_type') === 'sales_partner' ? 'bg-[#1155CC]/20 text-white font-semibold' : 'hover:bg-slate-800/50 hover:text-slate-100 text-slate-400' }}">
                                <i class="bi bi-briefcase text-[10px]"></i>
                                <span>Sales Partner Leads</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan
            </ul>
        </div>
        @endif

        <!-- 2. ADVERTISING MANAGEMENT -->
        @can('manage-campaigns')
        <div>
            <span class="px-4 text-[10px] font-bold text-slate-500 tracking-wider uppercase">Advertising Management</span>
                <li>
                    @php
                        $isAllRequests = request()->routeIs('admin.advertising.requests') && !request()->has('status');
                    @endphp
                    <a href="{{ route('admin.advertising.requests') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ $isAllRequests ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100 text-slate-400' }}">
                        <i class="bi bi-file-earmark-play-fill {{ $isAllRequests ? 'text-white' : 'text-slate-500' }}"></i>
                        <span>Advertising Requests</span>
                    </a>
                </li>

                <li>
                    @php
                        $isActiveCampaigns = request()->routeIs('admin.advertising.requests') && request('status') === 'Running';
                    @endphp
                    <a href="{{ route('admin.advertising.requests', ['status' => 'Running']) }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ $isActiveCampaigns ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100 text-slate-400' }}">
                        <i class="bi bi-play-circle-fill {{ $isActiveCampaigns ? 'text-white' : 'text-slate-500' }}"></i>
                        <span>Active Campaigns</span>
                    </a>
                </li>

            </ul>
        </div>
        @endcan

        <!-- 3. LOCATION MANAGEMENT -->
        @can('manage-locations')
        <div>
            <span class="px-4 text-[10px] font-bold text-slate-500 tracking-wider uppercase">Location Management</span>
            <ul class="mt-2 space-y-1">
                <li>
                    <a href="{{ route('admin.locations.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.locations.index') || request()->routeIs('admin.locations.create') || request()->routeIs('admin.locations.edit') ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i class="bi bi-geo-alt-fill text-base {{ request()->routeIs('admin.locations.index') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>All Locations</span>
                    </a>
                    
                    <a href="{{ route('admin.reviews.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.reviews.*') ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i class="bi bi-star-fill text-base {{ request()->routeIs('admin.reviews.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Location Reviews</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.locations.map') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.locations.map') ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i class="bi bi-map-fill text-base {{ request()->routeIs('admin.locations.map') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Locations Map</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.locations.categories') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.locations.categories') ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i class="bi bi-tags-fill text-base {{ request()->routeIs('admin.locations.categories') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Categories</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.locations.update-requests') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.locations.update-requests') ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100 text-slate-400' }}">
                        <i class="bi bi-patch-question-fill {{ request()->routeIs('admin.locations.update-requests') ? 'text-white' : 'text-slate-500' }}"></i>
                        <span>Location Updates</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.map-settings') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.map-settings') ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i class="bi bi-gear-fill text-base {{ request()->routeIs('admin.map-settings') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Map Settings</span>
                    </a>
                </li>
            </ul>
        </div>
        @endcan

        <!-- 4. WEBSITE MANAGEMENT -->
        @role('Admin')
        <div>
            <span class="px-4 text-[10px] font-bold text-slate-500 tracking-wider uppercase">Website Management</span>
            <ul class="mt-2 space-y-1">
                <li>
                    <a href="{{ route('admin.website.content.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.website.content.index') ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100 text-slate-400' }}">
                        <i class="bi bi-file-earmark-text-fill {{ request()->routeIs('admin.website.content.index') ? 'text-white' : 'text-slate-500' }}"></i>
                        <span>Company Info</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.website.branding.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.website.branding.index') ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100 text-slate-400' }}">
                        <i class="bi bi-image-fill {{ request()->routeIs('admin.website.branding.index') ? 'text-white' : 'text-slate-500' }}"></i>
                        <span>Branding & Logos</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.website.social-links.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.website.social-links.*') ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100 text-slate-400' }}">
                        <i class="bi bi-share-fill {{ request()->routeIs('admin.website.social-links.*') ? 'text-white' : 'text-slate-500' }}"></i>
                        <span>Social Media</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.website.policies.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.website.policies.*') ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100 text-slate-400' }}">
                        <i class="bi bi-shield-lock-fill {{ request()->routeIs('admin.website.policies.*') ? 'text-white' : 'text-slate-500' }}"></i>
                        <span>Policies</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.website.partner-brands.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.website.partner-brands.*') ? 'bg-[#1155CC] text-white shadow-lg shadow-blue-600/10' : 'hover:bg-slate-800 hover:text-slate-100 text-slate-400' }}">
                        <i class="bi bi-star-fill {{ request()->routeIs('admin.website.partner-brands.*') ? 'text-white' : 'text-slate-500' }}"></i>
                        <span>Partner Brands</span>
                    </a>
                </li>
                
                <li class="space-y-1">
                    <div class="px-4 py-1.5 text-xs font-semibold text-slate-400 flex items-center gap-2 mt-2">
                        <i class="bi bi-question-circle-fill text-slate-500"></i>
                        <span>FAQ Control</span>
                    </div>
                    <ul class="pl-6 space-y-1">
                        <li>
                            <a href="{{ route('admin.faqs.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('admin.faqs.index') || request()->routeIs('admin.faqs.create') || request()->routeIs('admin.faqs.edit') ? 'bg-[#1155CC]/20 text-white font-semibold' : 'hover:bg-slate-800/50 hover:text-slate-100 text-slate-400' }}">
                                <i class="bi bi-list-task text-[10px]"></i>
                                <span>All FAQs</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.faq-categories.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('admin.faq-categories.index') ? 'bg-[#1155CC]/20 text-white font-semibold' : 'hover:bg-slate-800/50 hover:text-slate-100 text-slate-400' }}">
                                <i class="bi bi-tags text-[10px]"></i>
                                <span>Categories</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="space-y-1">
                    <div class="px-4 py-1.5 text-xs font-semibold text-slate-400 flex items-center gap-2 mt-2">
                        <i class="bi bi-journal-text text-slate-500"></i>
                        <span>Blog Control</span>
                    </div>
                    <ul class="pl-6 space-y-1">
                        <li>
                            <a href="{{ route('admin.blogs.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('admin.blogs.index') || request()->routeIs('admin.blogs.create') || request()->routeIs('admin.blogs.edit') ? 'bg-[#1155CC]/20 text-white font-semibold' : 'hover:bg-slate-800/50 hover:text-slate-100 text-slate-400' }}">
                                <i class="bi bi-list-task text-[10px]"></i>
                                <span>All Articles</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        @endrole
    </nav>

    <!-- Sidebar Footer / Settings & Profile -->
    <div class="p-4 border-t border-slate-800 space-y-1">
        <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.profile') ? 'bg-[#1155CC] text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
            <i class="bi bi-person-fill text-base"></i>
            <span>Profile Settings</span>
        </a>
        @role('Admin')
        <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.settings') ? 'bg-[#1155CC] text-white' : 'hover:bg-slate-800 hover:text-slate-100' }}">
            <i class="bi bi-gear-fill text-base"></i>
            <span>System Settings</span>
        </a>
        @endrole
    </div>
</aside>
