<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $payment_method = $_POST['payment_method'] ?? 'cash';
    $cart = $_SESSION['cart'] ?? [];

    if (empty($cart)) {
        header("Location: ../cart.php");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Calculate Total
        $total = 0;
        $items = [];
        $ids = array_keys($cart);
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        
        while ($row = $stmt->fetch()) {
            $qty = $cart[$row['id']];
            $subtotal = $row['price'] * $qty;
            $total += $subtotal;
            $items[] = [
                'product_id' => $row['id'],
                'quantity' => $qty,
                'price' => $row['price']
            ];
        }

        // Handle Payment
        if ($payment_method == 'wallet') {
            $userStmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ? FOR UPDATE");
            $userStmt->execute([$user_id]);
            $user = $userStmt->fetch();
            
            if ($user['wallet_balance'] < $total) {
                // Not enough balance
                $pdo->rollBack();
                // Should ideally redirect with error message, simplified here
                header("Location: ../cart.php?error=insufficient_balance");
                exit;
            }
            
            // Deduct
            $new_balance = $user['wallet_balance'] - $total;
            $updateWallet = $pdo->prepare("UPDATE users SET wallet_balance = ? WHERE id = ?");
            $updateWallet->execute([$new_balance, $user_id]);

            // Log Transaction
            $txStmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, payment_method) VALUES (?, 'purchase', ?, 'Wallet')");
            $txStmt->execute([$user_id, $total]);
        }

        // Create Order
        $orderStmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'Completed')");
        $orderStmt->execute([$user_id, $total]);
        $order_id = $pdo->lastInsertId();

        // Create Order Items
        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $itemStmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
        }

        $pdo->commit();
        
        // Clear Cart
        unset($_SESSION['cart']);
        
        header("Location: ../dashboard.php?msg=order_success");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Checkout failed: " . $e->getMessage());
    }
}
?>
