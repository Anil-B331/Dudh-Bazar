<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action == 'create') {
            $product_id = $_POST['product_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;

            if ($product_id) {
                // Check if sub already exists
                $check = $pdo->prepare("SELECT id FROM subscriptions WHERE user_id = ? AND product_id = ?");
                $check->execute([$user_id, $product_id]);
                if ($check->fetch()) {
                    // Update existing
                    $stmt = $pdo->prepare("UPDATE subscriptions SET quantity = quantity + ?, status = 'active' WHERE user_id = ? AND product_id = ?");
                    $stmt->execute([$quantity, $user_id, $product_id]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, product_id, quantity, status) VALUES (?, ?, ?, 'active')");
                    $stmt->execute([$user_id, $product_id, $quantity]);
                }
                header("Location: ../dashboard.php?msg=sub_created");
                exit;
            }
        } elseif ($action == 'pause' || $action == 'resume') {
            $sub_id = $_POST['sub_id'] ?? 0;
            $status = ($action == 'pause') ? 'paused' : 'active';
            
            if ($sub_id) {
                $stmt = $pdo->prepare("UPDATE subscriptions SET status = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$status, $sub_id, $user_id]);
                header("Location: ../dashboard.php?msg=sub_updated");
                exit;
            }
        }
    } catch (Exception $e) {
        die("Subscription action failed: " . $e->getMessage());
    }
}
?>
