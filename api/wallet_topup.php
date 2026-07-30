<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'] ?? 0;
    $method = $_POST['method'] ?? 'eSewa';

    if ($amount >= 100) {
        try {
            $pdo->beginTransaction();

            // Update Wallet
            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
            $stmt->execute([$amount, $user_id]);

            // Log Transaction
            $ref = 'TXN' . strtoupper(uniqid());
            $txStmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, payment_method, reference_id) VALUES (?, 'topup', ?, ?, ?)");
            $txStmt->execute([$user_id, $amount, $method, $ref]);

            // Structured Log to file
            $logMsg = json_encode([
                'timestamp' => date('Y-m-d H:i:s'),
                'event' => 'WALLET_TOPUP',
                'user_id' => $user_id,
                'amount' => $amount,
                'method' => $method,
                'ref' => $ref,
                'status' => 'SUCCESS'
            ]) . PHP_EOL;
            file_put_contents('../wallet_transactions.log', $logMsg, FILE_APPEND);

            $pdo->commit();
            header("Location: ../dashboard.php?msg=topup_success");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            die("Top-up failed: " . $e->getMessage());
        }
    } else {
        // Invalid amount
        header("Location: ../dashboard.php?error=invalid_amount");
        exit;
    }
}
?>
