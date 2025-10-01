<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - IFMMS-ZAR')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="bg-gray-900 text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out" id="sidebar">
            <div class="flex items-center space-x-2 px-4">
                <i class="fas fa-shield-alt text-2xl text-blue-400"></i>
                <span class="text-xl font-bold">IFMMS-ZAR</span>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.incidents.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.incidents.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Incidents</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.users.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>

                <!-- Vehicle Management Dropdown -->
                <div class="relative">
                    <button onclick="toggleVehicleMenu()" class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('maintenance.vehicles.*') ? 'bg-gray-800' : '' }}">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-car"></i>
                            <span>Vehicle Management</span>
                        </div>
                        <i class="fas fa-chevron-down text-sm transition-transform" id="vehicleChevron"></i>
                    </button>
                    <div id="vehicleDropdown" class="hidden ml-4 mt-2 space-y-1">
                        <a href="{{ route('maintenance.vehicles.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-800 transition text-sm {{ request()->routeIs('maintenance.vehicles.index') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-list text-xs"></i>
                            <span>Vehicle List</span>
                        </a>
                        <a href="{{ route('maintenance.vehicles.create') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-800 transition text-sm {{ request()->routeIs('maintenance.vehicles.create') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-plus-circle text-xs"></i>
                            <span>Add Vehicle</span>
                        </a>
                        <a href="{{ route('maintenance.vehicles.assignments') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-800 transition text-sm {{ request()->routeIs('maintenance.vehicles.assignments') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-user-tag text-xs"></i>
                            <span>Assignments</span>
                        </a>
                        <a href="{{ route('maintenance.vehicles.maintenance') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-800 transition text-sm {{ request()->routeIs('maintenance.vehicles.maintenance') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-wrench text-xs"></i>
                            <span>Maintenance</span>
                        </a>
                        <a href="{{ route('maintenance.vehicles.reports') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-800 transition text-sm {{ request()->routeIs('maintenance.vehicles.reports') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-chart-bar text-xs"></i>
                            <span>Reports</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.incidents.export') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition">
                    <i class="fas fa-download"></i>
                    <span>Export Reports</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button class="text-gray-500 focus:outline-none md:hidden" id="sidebarToggle">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800 ml-4">@yield('page-title', 'Admin Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">Welcome, {{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });

        // Improved Vehicle dropdown management
        let isDropdownAnimating = false;
        
        function toggleVehicleMenu() {
            if (isDropdownAnimating) return;
            
            const dropdown = document.getElementById('vehicleDropdown');
            const chevron = document.getElementById('vehicleChevron');
            const button = event.currentTarget;
            
            if (!dropdown || !chevron) return;
            
            isDropdownAnimating = true;
            
            // Toggle classes
            const isHidden = dropdown.classList.contains('hidden');
            
            if (isHidden) {
                // Opening dropdown
                dropdown.classList.remove('hidden');
                dropdown.style.maxHeight = '0px';
                dropdown.style.overflow = 'hidden';
                dropdown.style.transition = 'max-height 0.3s ease-out';
                
                // Force reflow
                dropdown.offsetHeight;
                
                // Animate open
                dropdown.style.maxHeight = dropdown.scrollHeight + 'px';
                chevron.classList.add('rotate-180');
                
                localStorage.setItem('vehicleDropdownState', 'open');
                
                setTimeout(() => {
                    dropdown.style.maxHeight = '';
                    dropdown.style.overflow = '';
                    dropdown.style.transition = '';
                    isDropdownAnimating = false;
                }, 300);
            } else {
                // Closing dropdown
                dropdown.style.maxHeight = dropdown.scrollHeight + 'px';
                dropdown.style.overflow = 'hidden';
                dropdown.style.transition = 'max-height 0.3s ease-out';
                
                // Force reflow
                dropdown.offsetHeight;
                
                // Animate close
                dropdown.style.maxHeight = '0px';
                chevron.classList.remove('rotate-180');
                
                localStorage.setItem('vehicleDropdownState', 'closed');
                
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                    dropdown.style.maxHeight = '';
                    dropdown.style.overflow = '';
                    dropdown.style.transition = '';
                    isDropdownAnimating = false;
                }, 300);
            }
        }

        // Initialize dropdown state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const dropdown = document.getElementById('vehicleDropdown');
            const chevron = document.getElementById('vehicleChevron');
            
            if (!dropdown || !chevron) return;
            
            // Check if we're on a vehicle-related page
            const currentPath = window.location.pathname;
            const isVehiclePage = currentPath.includes('/vehicles') || 
                                  currentPath.includes('/maintenance');
            
            // Get saved state
            const savedState = localStorage.getItem('vehicleDropdownState');
            
            // Determine if dropdown should be open
            let shouldBeOpen = false;
            
            if (savedState === 'open') {
                shouldBeOpen = true;
            } else if (savedState === 'closed') {
                shouldBeOpen = false;
            } else if (isVehiclePage) {
                // No saved state but on vehicle page
                shouldBeOpen = true;
                localStorage.setItem('vehicleDropdownState', 'open');
            }
            
            // Apply state
            if (shouldBeOpen) {
                dropdown.classList.remove('hidden');
                chevron.classList.add('rotate-180');
            } else {
                dropdown.classList.add('hidden');
                chevron.classList.remove('rotate-180');
            }
        });
        
        // Prevent dropdown from closing when clicking inside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('vehicleDropdown');
            if (dropdown && dropdown.contains(e.target)) {
                e.stopPropagation();
            }
        });
    </script>
</body>
</html>
