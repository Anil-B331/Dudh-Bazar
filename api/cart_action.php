<?php
session_start();
file_put_contents(__DIR__.'/debug.log', date('Y-m-d H:i:s') . " - POST Data: " . json_encode($_POST) . " - Session ID: " . session_id() . " - Cookies: " . json_encode($_COOKIE) . "\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['product_id'] ?? 0;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if ($action == 'add' && $product_id) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]++;
        } else {
            $_SESSION['cart'][$product_id] = 1;
        }
    } elseif ($action == 'remove' && $product_id) {
        unset($_SESSION['cart'][$product_id]);
    }

    if (!empty($_POST['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
        $total_items = array_sum($_SESSION['cart']);
        echo json_encode(['status' => 'success', 'cart_count' => $total_items]);
        exit;
    }

    header("Location: ../cart.php");
    exit;
}
?>
