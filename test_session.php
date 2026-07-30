<?php
// Test session addition
session_start();
$product_id = 1;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]++;
} else {
    $_SESSION['cart'][$product_id] = 1;
}

echo "Cart contents:\n";
print_r($_SESSION['cart']);
echo "\nTotal items: " . array_sum($_SESSION['cart']) . "\n";
?>
