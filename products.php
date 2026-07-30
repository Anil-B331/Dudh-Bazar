<?php
require_once 'includes/header.php';

$category = $_GET['category'] ?? '';
$query = "SELECT * FROM products";
$params = [];
if ($category) {
    $query .= " WHERE category = ?";
    $params[] = $category;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 3rem; margin-bottom: 3rem;">
    <h1 class="section-title" style="margin-bottom: 0;">Our Groceries</h1>
    <div style="display: flex; gap: 0.75rem;">
        <a href="products.php" class="btn <?= !$category ? 'btn-primary' : 'btn-outline' ?>" style="padding: 0.5rem 1.25rem;">All</a>
        <a href="products.php?category=Milk" class="btn <?= $category == 'Milk' ? 'btn-primary' : 'btn-outline' ?>" style="padding: 0.5rem 1.25rem;">Milk</a>
        <a href="products.php?category=Ghee" class="btn <?= $category == 'Ghee' ? 'btn-primary' : 'btn-outline' ?>" style="padding: 0.5rem 1.25rem;">Ghee</a>
        <a href="products.php?category=Paneer" class="btn <?= $category == 'Paneer' ? 'btn-primary' : 'btn-outline' ?>" style="padding: 0.5rem 1.25rem;">Paneer</a>
        <a href="products.php?category=Yogurt" class="btn <?= $category == 'Yogurt' ? 'btn-primary' : 'btn-outline' ?>" style="padding: 0.5rem 1.25rem;">Yogurt</a>
    </div>
</div>

<div class="product-grid">
    <?php foreach($products as $product): ?>
    <div class="product-card">
        <?php if($product['category'] == 'Milk' || $product['category'] == 'Yogurt'): ?>
            <div class="promo-badge">-15%</div>
        <?php endif; ?>
        
        <a href="product_details.php?id=<?= $product['id'] ?>" class="product-img">
            <?php
            $img_path = 'assets/img/milk.png';
            if($product['category'] == 'Ghee') $img_path = 'assets/img/ghee.png';
            if($product['category'] == 'Paneer' || $product['category'] == 'Yogurt') $img_path = 'assets/img/milk.png';
            ?>
            <img src="<?= $img_path ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        </a>
        
        <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
        
        <a href="product_details.php?id=<?= $product['id'] ?>">
            <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
        </a>
        
        <div class="product-rating">
            ★★★★★ <span>(4.9)</span>
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
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
