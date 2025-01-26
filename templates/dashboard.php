<?php
require_once '../includes/Auth.php';
require_once '../includes/Subscription.php';
require_once '../includes/utils.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    redirect('/login');
}

$user = $auth->getCurrentUser();
$subscriptionModel = new Subscription();
$subscriptions = $subscriptionModel->getAllByUser($user['id']);
$monthlyTotal = $subscriptionModel->getMonthlyTotal($user['id']);
$upcomingRenewals = $subscriptionModel->getUpcomingRenewals($user['id']);
$subscriptionsByCategory = $subscriptionModel->getSubscriptionsByCategory($user['id']);

$pageTitle = 'Dashboard';

ob_start();
?>

<div x-data="{ activeTab: 'overview', showDeleteModal: false, deleteId: null }" class="animate-fade-in">
    <!-- Dashboard Header -->
    <div class="card mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h1>
        <p class="mt-1 text-gray-600">Manage your subscriptions and track your expenses</p>
    </div>

    <?php 
    $flash = get_flash_message();
    if ($flash): 
    ?>
    <div class="mb-6 p-4 rounded-lg <?php echo $flash['type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?> animate-slide-up">
        <?php echo $flash['message']; ?>
    </div>
    <?php endif; ?>

    <!-- Dashboard Navigation -->
    <div class="card mb-6 p-0">
        <nav class="flex border-b border-gray-200">
            <button @click="activeTab = 'overview'" 
                :class="{ 'nav-link-active': activeTab === 'overview' }"
                class="nav-link">
                Overview
            </button>
            <button @click="activeTab = 'subscriptions'"
                :class="{ 'nav-link-active': activeTab === 'subscriptions' }"
                class="nav-link">
                Subscriptions
            </button>
        </nav>
    </div>

    <!-- Dashboard Content -->
    <div class="card">
        <!-- Overview Tab -->
        <div x-show="activeTab === 'overview'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Monthly Spending -->
                <div class="card bg-primary bg-opacity-5 hover-lift">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-primary">Monthly Total</h3>
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo formatCurrency($monthlyTotal); ?></p>
                    <p class="text-sm text-gray-600">Total monthly subscriptions</p>
                </div>

                <!-- Active Subscriptions -->
                <div class="card bg-success bg-opacity-5 hover-lift">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-success">Active Subscriptions</h3>
                        <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo count($subscriptions); ?></p>
                    <p class="text-sm text-gray-600">Total active subscriptions</p>
                </div>

                <!-- Upcoming Renewals -->
                <div class="card bg-warning bg-opacity-5 hover-lift">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-warning">Upcoming Renewals</h3>
                        <svg class="w-8 h-8 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="mt-2 text-2xl font-bold text-gray-900"><?php echo count($upcomingRenewals); ?></p>
                    <p class="text-sm text-gray-600">Renewals in the next 7 days</p>
                </div>
            </div>

            <!-- Upcoming Renewals List -->
            <?php if (!empty($upcomingRenewals)): ?>
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Renewals</h3>
                <div class="space-y-4">
                    <?php foreach ($upcomingRenewals as $renewal): ?>
                    <div class="card hover-lift">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($renewal['name']); ?></h4>
                                <p class="text-sm text-gray-600">Renews on <?php echo formatDate($renewal['next_billing_date']); ?></p>
                            </div>
                            <span class="badge-warning"><?php echo formatCurrency($renewal['amount']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Subscriptions Tab -->
        <div x-show="activeTab === 'subscriptions'" class="space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Your Subscriptions</h3>
                <a href="/Subscribly/public/add_subscription.php" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Subscription
                </a>
            </div>

            <?php if (empty($subscriptions)): ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No subscriptions</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new subscription.</p>
                <div class="mt-6">
                    <a href="/Subscribly/public/add_subscription.php" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Subscription
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($subscriptions as $subscription): ?>
                <div class="card hover-lift">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($subscription['name']); ?></h4>
                            <p class="text-sm text-gray-600 mt-1"><?php echo formatCurrency($subscription['amount']); ?> / <?php echo $subscription['billing_cycle']; ?></p>
                            <div class="mt-2">
                                <span class="badge bg-<?php echo $subscription['category']; ?>-100 text-<?php echo $subscription['category']; ?>-800">
                                    <?php echo ucfirst($subscription['category']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="/Subscribly/public/edit_subscription.php?id=<?php echo $subscription['id']; ?>" 
                               class="btn-secondary p-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <button @click="deleteId = <?php echo $subscription['id']; ?>; showDeleteModal = true" 
                                    class="btn-danger p-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
         @keydown.escape.window="showDeleteModal = false">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-auto" @click.away="showDeleteModal = false">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Subscription</h3>
            <p class="text-gray-500">Are you sure you want to delete this subscription? This action cannot be undone.</p>
            <div class="mt-4 flex justify-end space-x-3">
                <button @click="showDeleteModal = false" class="btn-secondary">
                    Cancel
                </button>
                <a :href="'/Subscribly/public/delete_subscription.php?id=' + deleteId" class="btn-danger">
                    Delete
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'layout.php';
?>
