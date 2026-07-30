<?php
session_start();
require_once 'db.php';

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dudhbazar - Fresh Dairy Delivered</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="index.php" class="logo">🥛 Dudh<span>bazar</span></a>
            
            <nav class="nav-links">
                <a href="index.php" class="nav-link">Home</a>
                <a href="products.php" class="nav-link">Products</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                <?php endif; ?>
            </nav>

            <div class="nav-actions">
                <a href="cart.php" class="btn btn-outline" id="cart-link" style="padding: 0.5rem 1.25rem; font-size: 0.875rem;">🛒 Cart (<span id="cart-count"><?= $cart_count ?></span>)</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="btn btn-outline" style="padding: 0.5rem 1.25rem; font-size: 0.875rem;">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link" style="margin-right: 0.5rem;">Login</a>
                    <a href="register.php" class="btn btn-primary" style="padding: 0.5rem 1.25rem; font-size: 0.875rem;">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main>
