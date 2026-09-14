<header class="w-full bg-white shadow-md flex items-center justify-between px-6 py-4">
            
            <!-- Hamburger for Mobile -->
            <button @click="sidebarOpen = true" class="md:hidden text-gray-600 dark:text-gray-300">
                <i class="fa fa-bars text-lg"></i>
            </button>

            <!-- Logo / Title -->
            <h1 class="text-xl font-bold text-indigo-700 dark:text-indigo-400">Admin Panel</h1>

            <!-- Profile Dropdown -->
            <div class="relative" x-data="{ profileOpen: false }">
                <button @click="profileOpen = !profileOpen" class="flex items-center space-x-2 focus:outline-none">
                  <img src="{{ asset('' . $global['site_logo']) }}" 
         alt="{{$global['site_name']}}" 
          class="w-8 h-8 rounded-full">
                    <span class="hidden md:inline">{{ auth()->user()->name }}</span>
                    <i class="fa fa-chevron-down text-sm"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="profileOpen" @click.away="profileOpen = false" x-cloak
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 shadow-lg rounded-md py-2 z-50">
                    <a href="{{ route('admin.profile.edit') }}" 
                       class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Profile
                    </a>
                    <a href="{{ route('admin.settings.general') }}" 
                       class="block px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Settings
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-600 dark:text-red-400">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>