<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/email.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Subscription.php';
require_once __DIR__ . '/../includes/Notification.php';

// Initialize services
$subscriptionModel = new Subscription();
$notificationModel = new Notification();

try {
    // Get all subscriptions that are due for renewal in the next 7 days
    $db = getDBConnection();
    $stmt = $db->prepare("
        SELECT s.*, u.id as user_id, u.name as user_name, u.email
        FROM subscriptions s
        JOIN users u ON s.user_id = u.id
        WHERE s.renewal_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        AND NOT EXISTS (
            SELECT 1 FROM notifications n 
            WHERE n.subscription_id = s.id 
            AND n.created_at > DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        )
    ");
    
    $stmt->execute();
    $upcomingRenewals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($upcomingRenewals as $renewal) {
        // Calculate days until renewal
        $renewalDate = new DateTime($renewal['renewal_date']);
        $today = new DateTime();
        $daysUntil = $today->diff($renewalDate)->days;
        
        // Create notification
        $notificationModel->createRenewalNotification(
            $renewal['user_id'],
            $renewal['id'],
            $daysUntil
        );
        
        // Send email
        $user = [
            'id' => $renewal['user_id'],
            'name' => $renewal['user_name'],
            'email' => $renewal['email']
        ];
        
        $subscription = [
            'id' => $renewal['id'],
            'name' => $renewal['name'],
            'category' => $renewal['category'],
            'cost' => $renewal['cost'],
            'renewal_date' => $renewal['renewal_date']
        ];
        
        $notificationModel->sendRenewalEmail($user, $subscription);
    }
    
    // Clean up old notifications
    $notificationModel->cleanOldNotifications();
    
    echo "Notification process completed successfully.\n";
    echo "Processed " . count($upcomingRenewals) . " upcoming renewals.\n";
} catch (Exception $e) {
    echo "Error processing notifications: " . $e->getMessage() . "\n";
}
?>
