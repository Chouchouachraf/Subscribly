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

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getDBConnection();
        
        // Validate input
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $cost = floatval($_POST['cost'] ?? 0);
        $category = trim($_POST['category'] ?? '');
        $renewalDate = $_POST['renewal_date'] ?? '';
        $billingCycle = $_POST['billing_cycle'] ?? '';

        // Basic validation
        $errors = [];
        if (empty($name)) $errors[] = "Subscription name is required";
        if ($cost <= 0) $errors[] = "Cost must be greater than 0";
        if (empty($category)) $errors[] = "Category is required";
        if (empty($renewalDate)) $errors[] = "Renewal date is required";
        if (empty($billingCycle)) $errors[] = "Billing cycle is required";

        if (empty($errors)) {
            // Prepare the SQL statement
            $stmt = $db->prepare("
                INSERT INTO subscriptions (user_id, name, description, cost, category, renewal_date, billing_cycle)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            // Execute the statement
            $stmt->execute([
                $_SESSION['user_id'],
                $name,
                $description,
                $cost,
                $category,
                $renewalDate,
                $billingCycle
            ]);
            
            // Set success message and redirect to dashboard
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Subscription added successfully!'
            ];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = implode('<br>', $errors);
        }
        
    } catch (PDOException $e) {
        $error = "Error adding subscription: " . $e->getMessage();
    }
}

// Get categories for dropdown
$categories = [
    'Entertainment',
    'Software',
    'Music',
    'Gaming',
    'Productivity',
    'Education',
    'Utilities',
    'Other'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Subscription - Subscribly</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php require_once '../includes/navigation.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Add New Subscription</h1>
                    <a href="dashboard.php" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                        Back to Dashboard
                    </a>
                </div>
                
                <?php if ($message): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Subscription Name</label>
                        <input type="text" id="name" name="name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g., Netflix, Spotify, Adobe Creative Cloud">
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Optional: Add notes about this subscription"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="cost" class="block text-sm font-medium text-gray-700">Cost</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" step="0.01" id="cost" name="cost" required
                                       class="pl-7 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="0.00">
                            </div>
                        </div>
                        
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select id="category" name="category" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="renewal_date" class="block text-sm font-medium text-gray-700">Next Renewal Date</label>
                            <input type="date" id="renewal_date" name="renewal_date" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label for="billing_cycle" class="block text-sm font-medium text-gray-700">Billing Cycle</label>
                            <select id="billing_cycle" name="billing_cycle" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select billing cycle</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end pt-4">
                        <button type="submit"
                                class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Add Subscription
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
