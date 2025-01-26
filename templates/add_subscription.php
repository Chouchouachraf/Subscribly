<?php
require_once '../includes/auth_check.php';
require_once '../includes/theme.php';
require_once '../includes/category_icons.php';

$pageTitle = 'Add Subscription';
$categories = getAllCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $category = sanitize_input($_POST['category'] ?? '');
    $amount = sanitize_input($_POST['amount'] ?? '');
    $billing_cycle = sanitize_input($_POST['billing_cycle'] ?? '');
    $next_payment = sanitize_input($_POST['next_payment'] ?? '');
    
    $errors = [];
    if (empty($name)) $errors[] = "Service name is required";
    if (empty($category)) $errors[] = "Category is required";
    if (empty($amount) || !is_numeric($amount)) $errors[] = "Valid amount is required";
    if (empty($billing_cycle)) $errors[] = "Billing cycle is required";
    if (empty($next_payment)) $errors[] = "Next payment date is required";
    
    if (empty($errors)) {
        $userId = $_SESSION['user_id'];
        $sql = "INSERT INTO subscriptions (user_id, name, category, amount, billing_cycle, next_payment) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $name, $category, $amount, $billing_cycle, $next_payment]);
            set_flash_message('success', 'Subscription added successfully!');
            redirect('/dashboard');
        } catch (PDOException $e) {
            set_flash_message('error', 'Error adding subscription. Please try again.');
        }
    } else {
        set_flash_message('error', implode('<br>', $errors));
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Subscribly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .category-card {
            transition: all 0.2s ease-in-out;
        }
        .category-card:hover {
            transform: translateY(-2px);
        }
        .category-card.selected {
            border-color: #4F46E5;
            background-color: #EEF2FF;
        }
    </style>
</head>
<body class="h-full bg-gray-50">
    <?php include '../includes/navigation.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Add New Subscription</h1>
            
            <?php 
            $flash = get_flash_message();
            if ($flash): 
            ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $flash['type'] === 'error' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700'; ?>">
                <?php echo $flash['message']; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label for="name" class="<?php echo getLabelClasses(); ?>">Service Name</label>
                    <input type="text" id="name" name="name" required
                           class="<?php echo getInputClasses(); ?>"
                           placeholder="Netflix, Spotify, etc.">
                </div>
                
                <div>
                    <label class="<?php echo getLabelClasses(); ?>">Category</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-2">
                        <?php foreach ($categories as $cat): ?>
                        <label class="category-card cursor-pointer rounded-lg border-2 border-gray-200 p-4 flex flex-col items-center">
                            <input type="radio" name="category" value="<?php echo htmlspecialchars($cat['id']); ?>" class="sr-only" required>
                            <div class="w-12 h-12 rounded-lg bg-gray-50 p-2 mb-2 <?php echo getCategoryColorClass($cat['id']); ?>">
                                <?php echo renderCategoryIcon($cat['id'], 'w-full h-full'); ?>
                            </div>
                            <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($cat['name']); ?></span>
                            <span class="text-xs text-gray-500 text-center mt-1"><?php echo htmlspecialchars($cat['description']); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div>
                    <label for="amount" class="<?php echo getLabelClasses(); ?>">Amount</label>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" step="0.01" id="amount" name="amount" required
                               class="<?php echo getInputClasses(); ?> pl-7"
                               placeholder="0.00">
                    </div>
                </div>
                
                <div>
                    <label for="billing_cycle" class="<?php echo getLabelClasses(); ?>">Billing Cycle</label>
                    <select id="billing_cycle" name="billing_cycle" required class="<?php echo getInputClasses(); ?>">
                        <option value="">Select billing cycle</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                
                <div>
                    <label for="next_payment" class="<?php echo getLabelClasses(); ?>">Next Payment Date</label>
                    <input type="date" id="next_payment" name="next_payment" required
                           class="<?php echo getInputClasses(); ?>">
                </div>
                
                <div class="flex justify-end space-x-4">
                    <a href="/Subscribly/public/dashboard" 
                       class="<?php echo getSecondaryButtonClasses(); ?>">
                        Cancel
                    </a>
                    <button type="submit" class="<?php echo getPrimaryButtonClasses(); ?>">
                        Add Subscription
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    document.querySelectorAll('input[name="category"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove selected class from all cards
            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('selected');
            });
            // Add selected class to the chosen card
            if (this.checked) {
                this.closest('.category-card').classList.add('selected');
            }
        });
    });
    </script>
</body>
</html>
