<!-- Mobile Hamburger -->
    <button @click="sidebarOpen = true"
            class="md:hidden fixed top-4 left-4 z-50 bg-indigo-600 text-white p-2 rounded-full shadow-lg">
        <i class="fa fa-bars text-lg"></i>
    </button>

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-cloak
         class="fixed inset-0 bg-black/50 backdrop-blur-sm md:hidden z-30">
    </div>

    <!-- Sidebar -->
    <aside 
        class="fixed inset-y-0 left-0 z-40 w-64 bg-white shadow-xl border-r border-gray-100
               transition-transform duration-300 ease-out
               flex flex-col
               md:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-6 border-b border-gray-100">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center text-orange-500 text-2xl font-bold">
                <img src="{{ asset('' . $global['site_logo']) }}" 
                    alt="{{$global['site_name']}}" 
                    class="h-12 w-auto object-contain">
            </a>

            <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-indigo-600">
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>

        <!-- Scrollable Menu -->
        <nav class="px-4 py-6 space-y-6 flex-1 overflow-y-auto">

            <!-- SECTION: Dashboard -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase px-2 mb-2 tracking-wider">Overview</p>

                <a href="{{ route('admin.dashboard') }}"
                   class="group flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition
                          {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                    <i class="fa fa-home mr-3 text-gray-400 group-hover:text-indigo-600"></i>
                    Dashboard
                </a>
            </div>

            <!-- Users -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase px-2 mb-2 tracking-wider">Users</p>

                @include('layouts.admin.group', [
                    'icon' => 'fa-user',
                    'label' => 'User Management',
                    'open' => request()->routeIs('admin.labels.*', 'admin.users.*'),
                    'items' => [
                        ['route' => 'admin.users.all', 'label' => 'All Users'],
                        ['route' => 'admin.labels.all',     'label' => 'Labels'],
                        ['route' => 'admin.labels.artists', 'label' => 'Artists'],
                        
                    ]
                ])
            </div>
        <!-- Billing & Subscriptions -->
<div>
    <p class="text-xs font-semibold text-gray-400 uppercase px-2 mb-2 tracking-wider">
        Billing & Subscriptions
    </p>

    @include('layouts.admin.group', [
        'icon' => 'fa-wallet',
        'label' => 'Subscription Management',
        'open' => request()->routeIs('admin.pricing.*') || request()->routeIs('admin.subscriptions.*'),
        'items' => [
            ['route' => 'admin.pricing.plans', 'label' => 'All Plans', 'icon' => 'fa-list'],
            ['route' => 'admin.pricing.create','label' => 'Create New', 'icon' => 'fa-plus'],


            ['route' => 'admin.subscriptions.active', 'label' => 'Active Subscribers', 'icon' => 'fa-users'],
            ['route' => 'admin.subscriptions.history','label' => 'Payment History', 'icon' => 'fa-history'],
        ]
    ])
</div>


            <!-- Releases -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase px-2 mb-2 tracking-wider">Music Distribution</p>

                @include('layouts.admin.group', [
                    'icon' => 'fa-compact-disc',
                    'label' => 'Music Releases',
                    'open' => request()->routeIs('admin.releases.*'),
                    'items' => [
                        ['route' => 'admin.releases.all',      'label' => 'All Releases'],
                        ['route' => 'admin.releases.pending',  'label' => 'Pending'],
                        ['route' => 'admin.releases.approved', 'label' => 'Approved'],
                        ['route' => 'admin.releases.rejected',   'label' => 'Rejected'],
                    ]
                ])
            </div>

            <!-- Royalty -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase px-2 mb-2 tracking-wider">Money</p>

                @include('layouts.admin.group', [
                    'icon' => 'fa-dollar-sign',
                    'label' => 'Royalties & Payments',
                    'open' => request()->routeIs('admin.royalties.*') || request()->routeIs('admin.withdrawals.*'),
                    'items' => [
                        ['route' => 'admin.royalties.reports',          'label' => 'Royalty Overview'],
                        ['route' => 'admin.royalties.reports.streams', 'label' => 'Stream Reports'],
                        ['route' => 'admin.royalties.reports.earnings','label' => 'Earnings Approval'],
                        ['route' => 'admin.withdrawals.pending',         'label' => 'Withdrawal Requests'],
                        ['route' => 'admin.withdrawals.history','label' => 'Withdrawal History'],
                    ]
                ])
            </div>

           <!-- Analytics -->
<div>
    <p class="text-xs font-semibold text-gray-400 uppercase px-2 mb-2 tracking-wider">Analytics</p>

    @include('layouts.admin.group', [
        'icon' => 'fa-chart-line',
        'label' => 'Analytics',
        'open' => request()->routeIs('admin.analytics.*'),
        'items' => [
            ['route' => 'admin.analytics.dashboard',   'label' => 'Dashboard'],
            ['route' => 'admin.analytics.manual.form', 'label' => 'Manual Stream Entry'],
        ]
    ])
</div>


            <!-- Homepage -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase px-2 mb-2 tracking-wider">Website</p>

                <a href="{{ route('admin.homepage.index') }}"
                   class="group flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition
                          {{ request()->routeIs('admin.homepage.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                    <i class="fa fa-pen-square mr-3 text-gray-400 group-hover:text-indigo-600"></i>
                    Homepage Content
                </a>
            </div>

            <!-- Settings -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase px-2 mb-2 tracking-wider">Settings</p>
@include('layouts.admin.group', [
    'icon' => 'fa-cog',
    'label' => 'Settings',
    'open' => request()->routeIs('admin.settings.*'),
    'items' => [
        ['route' => 'admin.settings.general',     'label' => 'General'],
        ['route' => 'admin.settings.payment',     'label' => 'Payment'],
        ['route' => 'admin.settings.currencies',  'label' => 'Currencies'],

        // CMS Pages
        ['route' => 'admin.settings.terms',        'label' => 'Terms & Conditions'],
        ['route' => 'admin.settings.privacy',      'label' => 'Privacy Policy'],
        ['route' => 'admin.settings.cookies',      'label' => 'Cookie Policy'],
        ['route' => 'admin.settings.refund',       'label' => 'Refund Policy'],
    ]
])

            </div>

            <!-- Logout -->
            <form method="POST" action="{{ route('admin.logout') }}">
    @csrf
    <button type="submit"
        class="flex items-center px-4 py-2 rounded-lg text-red-600 hover:bg-red-50 transition font-medium w-full">
        <i class="fa fa-sign-out-alt mr-3"></i> Logout
    </button>
</form>

        </nav>
    </aside>
</div>
