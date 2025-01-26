<?php
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/Auth.php';

// Check if user is logged in
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: /Subscribly/public/login');
    exit();
}

// Get user's subscriptions
$db = getDBConnection();
$stmt = $db->prepare("
    SELECT * FROM subscriptions 
    WHERE user_id = ? 
    ORDER BY renewal_date ASC
");
$stmt->execute([$_SESSION['user_id']]);
$subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total monthly cost
$totalMonthlyCost = 0;
foreach ($subscriptions as $sub) {
    $monthlyCost = $sub['cost'];
    switch ($sub['billing_cycle']) {
        case 'yearly':
            $monthlyCost = $sub['cost'] / 12;
            break;
        case 'quarterly':
            $monthlyCost = $sub['cost'] / 3;
            break;
    }
    $totalMonthlyCost += $monthlyCost;
}

// Get upcoming renewals (next 30 days)
$upcomingRenewals = array_filter($subscriptions, function($sub) {
    $renewalDate = strtotime($sub['renewal_date']);
    $thirtyDaysFromNow = strtotime('+30 days');
    return $renewalDate <= $thirtyDaysFromNow && $renewalDate >= strtotime('today');
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Subscribly</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php require_once '../includes/navigation.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="mb-6">
                <?php if ($_SESSION['flash_message']['type'] === 'success'): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['flash_message']['message']); ?></span>
                    </div>
                <?php endif; ?>
                <?php unset($_SESSION['flash_message']); ?>
            </div>
        <?php endif; ?>

        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Your Subscriptions</h1>
            <a href="add_subscription.php" 
               class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Add New Subscription
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-sm font-medium text-gray-500">Total Subscriptions</h3>
                <p class="text-2xl font-semibold text-gray-900"><?php echo count($subscriptions); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-sm font-medium text-gray-500">Monthly Cost</h3>
                <p class="text-2xl font-semibold text-gray-900">$<?php echo number_format($totalMonthlyCost, 2); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-sm font-medium text-gray-500">Yearly Cost</h3>
                <p class="text-2xl font-semibold text-gray-900">$<?php echo number_format($totalMonthlyCost * 12, 2); ?></p>
            </div>
        </div>

        <!-- Upcoming Renewals -->
        <?php if (!empty($upcomingRenewals)): ?>
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Renewals</h2>
                <div class="space-y-4">
                    <?php foreach ($upcomingRenewals as $sub): ?>
                        <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($sub['name']); ?></h3>
                                <p class="text-sm text-gray-500">Renews on <?php echo date('M j, Y', strtotime($sub['renewal_date'])); ?></p>
                            </div>
                            <span class="text-lg font-medium text-gray-900">$<?php echo number_format($sub['cost'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($subscriptions)): ?>
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <h2 class="text-xl font-medium text-gray-900 mb-4">No subscriptions yet!</h2>
                <p class="text-gray-500 mb-6">Start tracking your subscriptions by adding your first one.</p>
                <a href="add_subscription.php" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Add Your First Subscription
                </a>
            </div>
        <?php else: ?>
            <!-- Subscriptions Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($subscriptions as $sub): ?>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($sub['name']); ?></h3>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($sub['category']); ?></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <?php echo ucfirst($sub['billing_cycle']); ?>
                                </span>
                                <form method="POST" action="delete_subscription.php" class="inline" onsubmit="return confirm('Are you sure you want to delete this subscription?');">
                                    <input type="hidden" name="subscription_id" value="<?php echo $sub['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <?php if ($sub['description']): ?>
                            <p class="text-sm text-gray-600 mb-4"><?php echo htmlspecialchars($sub['description']); ?></p>
                        <?php endif; ?>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-500">
                                    Next renewal
                                    <p class="text-gray-900 font-medium"><?php echo date('M j, Y', strtotime($sub['renewal_date'])); ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm text-gray-500">Cost</span>
                                    <p class="text-lg font-medium text-gray-900">$<?php echo number_format($sub['cost'], 2); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
