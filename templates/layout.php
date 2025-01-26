<?php
require_once __DIR__ . '/../includes/Notification.php';
require_once __DIR__ . '/../includes/theme.php';
$pageTitle = isset($pageTitle) ? $pageTitle . ' - Subscribly' : 'Subscribly';

// Get notifications if user is logged in
$notifications = [];
if (isset($_SESSION['user_id'])) {
    $notificationModel = new Notification();
    $notifications = $notificationModel->getUnreadNotifications($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="/Subscribly/public/css/styles.css" rel="stylesheet">
    <link href="/Subscribly/public/css/app.css" rel="stylesheet">
    <?php echo getCommonStyles(); ?>
    
    <!-- Scripts -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script src="/Subscribly/public/js/app.js"></script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="h-full bg-pattern">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm" x-data="{ showNotifications: false, showMobileMenu: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <a href="/Subscribly/public/dashboard" class="flex-shrink-0 flex items-center">
                            <img class="h-8 w-auto" src="/Subscribly/public/images/logo.svg" alt="Subscribly">
                            <span class="ml-2 text-xl font-bold text-indigo-600">Subscribly</span>
                        </a>
                        
                        <!-- Desktop Navigation -->
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="/Subscribly/public/dashboard" 
                                   class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Dashboard
                                </a>
                                <a href="/Subscribly/public/add_subscription.php"
                                   class="<?php echo basename($_SERVER['PHP_SELF']) === 'add_subscription.php' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Add Subscription
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Right Navigation -->
                    <div class="flex items-center">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <!-- Notification Bell -->
                            <div class="relative ml-3" @click.away="showNotifications = false">
                                <button @click="showNotifications = !showNotifications"
                                        class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-full relative">
                                    <span class="sr-only">View notifications</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <?php if (!empty($notifications)): ?>
                                        <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                                    <?php endif; ?>
                                </button>
                                
                                <!-- Notification Dropdown -->
                                <div x-show="showNotifications"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="origin-top-right absolute right-0 mt-2 w-80 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none divide-y divide-gray-100"
                                     role="menu">
                                    <div class="px-4 py-3">
                                        <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                    </div>
                                    <div class="py-1">
                                        <?php if (empty($notifications)): ?>
                                            <div class="px-4 py-3 text-sm text-gray-700">
                                                No new notifications
                                            </div>
                                        <?php else: ?>
                                            <?php foreach ($notifications as $notification): ?>
                                                <div class="px-4 py-3 hover:bg-gray-50 transition-colors duration-150">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($notification['subscription_name']); ?>
                                                    </p>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        <?php echo htmlspecialchars($notification['message']); ?>
                                                    </p>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Profile Dropdown -->
                            <div class="ml-3 relative" x-data="{ open: false }">
                                <div>
                                    <button @click="open = !open" 
                                            class="flex items-center max-w-xs bg-white rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                                            id="user-menu-button">
                                        <span class="sr-only">Open user menu</span>
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-600 font-medium text-sm">
                                                <?php echo substr($_SESSION['user_name'], 0, 1); ?>
                                            </span>
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-700">
                                            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                        </span>
                                        <svg class="ml-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1"
                                     role="menu">
                                    <a href="/Subscribly/public/profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>
                                    <a href="/Subscribly/public/settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Settings</a>
                                    <a href="/Subscribly/public/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign out</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="/Subscribly/public/login.php" class="<?php echo getPrimaryButtonClasses(); ?> ml-3">
                                Sign in
                            </a>
                            <a href="/Subscribly/public/register.php" class="<?php echo getSecondaryButtonClasses(); ?> ml-3">
                                Sign up
                            </a>
                        <?php endif; ?>
                        
                        <!-- Mobile menu button -->
                        <button @click="showMobileMenu = !showMobileMenu" 
                                class="sm:hidden ml-3 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                            <span class="sr-only">Open main menu</span>
                            <svg class="h-6 w-6" x-show="!showMobileMenu" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg class="h-6 w-6" x-show="showMobileMenu" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div x-show="showMobileMenu" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/Subscribly/public/dashboard.php" 
                           class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'; ?>">
                            Dashboard
                        </a>
                        <a href="/Subscribly/public/add_subscription.php"
                           class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium <?php echo basename($_SERVER['PHP_SELF']) === 'add_subscription.php' ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'; ?>">
                            Add Subscription
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
        
        <!-- Page Content -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <?php echo $content ?? ''; ?>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">
                    &copy; <?php echo date('Y'); ?> Subscribly. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
    
    <!-- Toast Notifications Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-4"></div>
    
    <script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        tippy('[data-tooltip]', {
            animation: 'scale',
            placement: 'top'
        });
    });
    </script>
</body>
</html>
