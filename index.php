<?php require_once 'includes/header.php'; ?>

<section class="hero">
    <div class="hero-blob"></div>
    <div class="hero-text">
        <h1>Farm Fresh Dairy,<br>Delivered Daily.</h1>
        <p>Experience the purity of organic milk, rich ghee, and fresh paneer sourced directly from local farms. Start your morning right with Dudhbazar.</p>
        <div style="display: flex; gap: 1rem;">
            <a href="products.php" class="btn btn-accent">Shop Groceries</a>
            <a href="register.php" class="btn btn-outline">Join Dudhbazar</a>
        </div>
    </div>
    <div class="hero-image">
        <div class="floating-badge" style="top: 10%; right: -5%;">
            <div class="badge-icon">🚚</div>
            <div style="font-size: 0.875rem;">Fast Delivery<br><span style="color: var(--text-muted); font-size: 0.75rem; font-weight: 500;">Within 2 hours</span></div>
        </div>
        <div class="floating-badge" style="bottom: 10%; left: -10%;">
            <div class="badge-icon" style="background: #FFF4E5; color: var(--promo);">✨</div>
            <div style="font-size: 0.875rem;">100% Organic<br><span style="color: var(--text-muted); font-size: 0.75rem; font-weight: 500;">Farm to table</span></div>
        </div>
        <img src="assets/img/hero.png" alt="Fresh Dairy Groceries">
    </div>
</section>

<section style="margin-top: 6rem; margin-bottom: 6rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem;">
        <div>
            <h2 class="section-title" style="margin-bottom: 0.5rem;">Featured Groceries</h2>
            <p style="color: var(--text-muted);">Freshly picked for your daily needs.</p>
        </div>
        <a href="products.php" class="btn btn-outline" style="padding: 0.5rem 1.5rem; font-size: 0.875rem;">View All</a>
    </div>
    
    <div class="product-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM products LIMIT 4");
        while ($product = $stmt->fetch()):
            $img_path = 'assets/img/milk.png';
            if($product['category'] == 'Ghee') $img_path = 'assets/img/ghee.png';
            if($product['category'] == 'Paneer' || $product['category'] == 'Yogurt') $img_path = 'assets/img/milk.png';
        ?>
        <div class="product-card">
            <?php if($product['category'] == 'Milk'): ?>
                <div class="promo-badge">-10%</div>
            <?php endif; ?>
            <a href="product_details.php?id=<?= $product['id'] ?>" class="product-img">
                <img src="<?= $img_path ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            </a>
            
            <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
            <a href="product_details.php?id=<?= $product['id'] ?>">
                <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
            </a>
            
            <div class="product-rating">
                ★★★★★ <span>(4.8)</span>
            </div>
            
            <div class="product-bottom">
                <div class="product-price">Rs. <?= number_format($product['price'], 2) ?></div>
                <form action="api/cart_action.php" method="POST" class="ajax-cart-form">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="action" value="add">
                    <button type="submit" class="btn-circle" title="Add to Cart">+</button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
