<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/email.php';

class Notification {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    public function createRenewalNotification($userId, $subscriptionId, $daysUntilRenewal) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO notifications (user_id, subscription_id, message)
                VALUES (?, ?, ?)
            ");
            
            $message = "Your subscription will renew in {$daysUntilRenewal} days.";
            $stmt->execute([$userId, $subscriptionId, $message]);
            
            return [true, "Notification created successfully"];
        } catch (PDOException $e) {
            return [false, "Failed to create notification: " . $e->getMessage()];
        }
    }

    public function sendRenewalEmail($user, $subscription) {
        try {
            $to = $user['email'];
            $subject = "Upcoming Subscription Renewal - {$subscription['name']}";
            
            $renewalDate = date('F j, Y', strtotime($subscription['renewal_date']));
            $cost = number_format($subscription['cost'], 2);
            
            // Create HTML email body
            $body = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; }
                        .header { color: #531001; }
                        .details { background: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0; }
                        .footer { margin-top: 20px; color: #666; }
                    </style>
                </head>
                <body>
                    <h2 class='header'>Upcoming Subscription Renewal</h2>
                    <p>Hello {$user['name']},</p>
                    <p>This is a reminder that your subscription to <strong>{$subscription['name']}</strong> will renew on <strong>{$renewalDate}</strong>.</p>
                    
                    <div class='details'>
                        <h3 style='margin-top: 0;'>Subscription Details</h3>
                        <p><strong>Name:</strong> {$subscription['name']}</p>
                        <p><strong>Category:</strong> {$subscription['category']}</p>
                        <p><strong>Cost:</strong> \${$cost}</p>
                        <p><strong>Renewal Date:</strong> {$renewalDate}</p>
                    </div>
                    
                    <p>To manage your subscription, please visit your <a href='http://localhost/Subscribly/public/dashboard'>Subscribly Dashboard</a>.</p>
                    <div class='footer'>
                        <p>Best regards,<br>The Subscribly Team</p>
                    </div>
                </body>
                </html>
            ";
            
            // Email headers
            $headers = [
                'MIME-Version: 1.0',
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . EMAIL_FROM_NAME . ' <' . EMAIL_FROM . '>',
                'Reply-To: ' . EMAIL_FROM
            ];
            
            // Send email
            if (mail($to, $subject, $body, implode("\r\n", $headers))) {
                return [true, "Email sent successfully"];
            } else {
                return [false, "Failed to send email"];
            }
        } catch (Exception $e) {
            return [false, "Failed to send email: " . $e->getMessage()];
        }
    }

    public function getUnreadNotifications($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT n.*, s.name as subscription_name
                FROM notifications n
                JOIN subscriptions s ON n.subscription_id = s.id
                WHERE n.user_id = ? AND n.sent_at IS NULL
                ORDER BY n.created_at DESC
            ");
            
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function markNotificationAsSent($notificationId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE notifications 
                SET sent_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            $stmt->execute([$notificationId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function cleanOldNotifications($days = 30) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM notifications 
                WHERE created_at < DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)
                AND sent_at IS NOT NULL
            ");
            
            $stmt->execute([$days]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
