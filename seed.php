<?php
require_once 'includes/db.php';

try {
    echo "Creating database dudhbazar if not exists...<br>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS dudhbazar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE dudhbazar");

    echo "Creating users table...<br>";
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        wallet_balance DECIMAL(10,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "Creating products table...<br>";
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        category ENUM('Milk', 'Ghee', 'Paneer', 'Yogurt') NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        stock INT DEFAULT 100,
        farm_source VARCHAR(255),
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "Creating orders table...<br>";
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('Pending', 'Completed', 'Cancelled') DEFAULT 'Completed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    echo "Creating order_items table...<br>";
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    echo "Creating subscriptions table...<br>";
    $pdo->exec("CREATE TABLE IF NOT EXISTS subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        status ENUM('active', 'paused', 'cancelled') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    echo "Creating transactions table...<br>";
    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        type ENUM('topup', 'deduction', 'purchase') NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method ENUM('eSewa', 'Fonepay', 'Wallet') NOT NULL,
        reference_id VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Seed Data
    echo "Checking if products exist...<br>";
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    if ($stmt->fetchColumn() == 0) {
        echo "Seeding products...<br>";
        $products = [
            ['Fresh Cow Milk (1L)', 'Daily fresh, organic cow milk sourced directly from local farms.', 'Milk', 90.00, 500, 'Green Valley Dairy', 'assets/img/milk.jpg'],
            ['Pure Buffalo Ghee (500g)', 'Traditional granular ghee with authentic aroma and taste.', 'Ghee', 650.00, 100, 'Himalayan Pastures', 'assets/img/ghee.jpg'],
            ['Soft Malai Paneer (250g)', 'Rich and creamy paneer perfect for curries.', 'Paneer', 180.00, 200, 'Green Valley Dairy', 'assets/img/paneer.jpg'],
            ['Probiotic Yogurt (500g)', 'Thick, set curd rich in probiotics for healthy digestion.', 'Yogurt', 80.00, 300, 'Sunrise Farms', 'assets/img/yogurt.jpg']
        ];

        $insertProduct = $pdo->prepare("INSERT INTO products (name, description, category, price, stock, farm_source, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($products as $p) {
            $insertProduct->execute($p);
        }
        echo "Products seeded successfully!<br>";
    } else {
        echo "Products already exist. Skipping seed.<br>";
    }

    echo "Database setup complete! You can now start using Dudhbazar.";

} catch(PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>
