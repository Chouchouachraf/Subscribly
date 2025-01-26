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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscription_id'])) {
    $subscriptionId = (int)$_POST['subscription_id'];
    $userId = $_SESSION['user_id'];
    
    try {
        $db = getDBConnection();
        
        // First verify that this subscription belongs to the current user
        $stmt = $db->prepare("SELECT id FROM subscriptions WHERE id = ? AND user_id = ?");
        $stmt->execute([$subscriptionId, $userId]);
        
        if ($stmt->fetch()) {
            // If subscription belongs to user, delete it
            $deleteStmt = $db->prepare("DELETE FROM subscriptions WHERE id = ?");
            $deleteStmt->execute([$subscriptionId]);
            
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Subscription deleted successfully!'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Unauthorized to delete this subscription.'
            ];
        }
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Error deleting subscription: ' . $e->getMessage()
        ];
    }
}

// Redirect back to dashboard
header('Location: dashboard.php');
exit();
