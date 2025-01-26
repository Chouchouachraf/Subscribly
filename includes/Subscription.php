<?php
require_once __DIR__ . '/../config/database.php';

class Subscription {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    public function create($userId, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO subscriptions (user_id, name, category, cost, renewal_date)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId,
                $data['name'],
                $data['category'],
                $data['cost'],
                $data['renewal_date']
            ]);

            return [true, "Subscription added successfully"];
        } catch (PDOException $e) {
            return [false, "Failed to add subscription: " . $e->getMessage()];
        }
    }

    public function update($id, $userId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE subscriptions 
                SET name = ?, category = ?, cost = ?, renewal_date = ?
                WHERE id = ? AND user_id = ?
            ");
            
            $stmt->execute([
                $data['name'],
                $data['category'],
                $data['cost'],
                $data['renewal_date'],
                $id,
                $userId
            ]);

            return [true, "Subscription updated successfully"];
        } catch (PDOException $e) {
            return [false, "Failed to update subscription: " . $e->getMessage()];
        }
    }

    public function delete($id, $userId) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM subscriptions 
                WHERE id = ? AND user_id = ?
            ");
            
            $stmt->execute([$id, $userId]);
            return [true, "Subscription deleted successfully"];
        } catch (PDOException $e) {
            return [false, "Failed to delete subscription: " . $e->getMessage()];
        }
    }

    public function getById($id, $userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM subscriptions 
                WHERE id = ? AND user_id = ?
            ");
            
            $stmt->execute([$id, $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getAllByUser($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM subscriptions 
                WHERE user_id = ?
                ORDER BY renewal_date ASC
            ");
            
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getUpcomingRenewals($userId, $days = 7) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM subscriptions 
                WHERE user_id = ? 
                AND renewal_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                ORDER BY renewal_date ASC
            ");
            
            $stmt->execute([$userId, $days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getMonthlyTotal($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT SUM(cost) as total 
                FROM subscriptions 
                WHERE user_id = ?
            ");
            
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getSubscriptionsByCategory($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT category, COUNT(*) as count, SUM(cost) as total_cost
                FROM subscriptions 
                WHERE user_id = ?
                GROUP BY category
            ");
            
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
