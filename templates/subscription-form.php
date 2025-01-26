<?php
require_once '../includes/Auth.php';
require_once '../includes/Subscription.php';
require_once '../includes/utils.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    redirect('/login');
}

$subscription = null;
$isEdit = false;
$pageTitle = 'Add Subscription';

if (isset($_GET['id'])) {
    $subscriptionId = (int)$_GET['id'];
    $subscriptionModel = new Subscription();
    $subscription = $subscriptionModel->getById($subscriptionId, $_SESSION['user_id']);
    
    if ($subscription) {
        $isEdit = true;
        $pageTitle = 'Edit Subscription';
    } else {
        redirect('/dashboard');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => sanitize_input($_POST['name'] ?? ''),
        'category' => sanitize_input($_POST['category'] ?? ''),
        'cost' => (float)sanitize_input($_POST['cost'] ?? 0),
        'renewal_date' => sanitize_input($_POST['renewal_date'] ?? '')
    ];

    $errors = [];
    if (empty($data['name'])) $errors[] = "Name is required";
    if (empty($data['category'])) $errors[] = "Category is required";
    if ($data['cost'] <= 0) $errors[] = "Cost must be greater than 0";
    if (empty($data['renewal_date'])) $errors[] = "Renewal date is required";

    if (empty($errors)) {
        $subscriptionModel = new Subscription();
        
        if ($isEdit) {
            [$success, $message] = $subscriptionModel->update($subscriptionId, $_SESSION['user_id'], $data);
        } else {
            [$success, $message] = $subscriptionModel->create($_SESSION['user_id'], $data);
        }
        
        if ($success) {
            set_flash_message('success', $message);
            redirect('/dashboard');
        } else {
            set_flash_message('error', $message);
        }
    } else {
        set_flash_message('error', implode('<br>', $errors));
    }
}

$categories = [
    'Entertainment',
    'Utilities',
    'Software',
    'Gaming',
    'Music',
    'Fitness',
    'Shopping',
    'Other'
];

ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-accent mb-6"><?php echo $pageTitle; ?></h2>

        <?php 
        $flash = get_flash_message();
        if ($flash): 
        ?>
        <div class="mb-4 p-4 rounded <?php echo $flash['type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
            <?php echo $flash['message']; ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label for="name" class="form-label">Subscription Name</label>
                <input type="text" id="name" name="name" required
                    value="<?php echo $subscription ? htmlspecialchars($subscription['name']) : ''; ?>"
                    class="form-input">
            </div>

            <div>
                <label for="category" class="form-label">Category</label>
                <select id="category" name="category" required class="form-input">
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category; ?>" 
                            <?php echo $subscription && $subscription['category'] === $category ? 'selected' : ''; ?>>
                            <?php echo $category; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="cost" class="form-label">Monthly Cost</label>
                <div class="relative">
                    <span class="absolute left-3 top-3">$</span>
                    <input type="number" id="cost" name="cost" step="0.01" min="0" required
                        value="<?php echo $subscription ? htmlspecialchars($subscription['cost']) : ''; ?>"
                        class="form-input pl-7">
                </div>
            </div>

            <div>
                <label for="renewal_date" class="form-label">Next Renewal Date</label>
                <input type="date" id="renewal_date" name="renewal_date" required
                    value="<?php echo $subscription ? htmlspecialchars($subscription['renewal_date']) : ''; ?>"
                    class="form-input">
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/Subscribly/public/dashboard" class="btn btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <?php echo $isEdit ? 'Update' : 'Add'; ?> Subscription
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'layout.php';
?>
