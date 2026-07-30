<?php
require_once 'includes/header.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='alert alert-error'>Product not found.</div>";
    require_once 'includes/footer.php';
    exit;
}
?>

<div style="margin-top: 4rem; display: flex; gap: 4rem; background: var(--white); padding: 3rem; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; background: #E5E7EB; border-radius: 1.5rem; overflow: hidden; box-shadow: var(--glass-shadow);">
        <?php
        $img_path = 'assets/img/milk.png';
        if($product['category'] == 'Ghee') $img_path = 'assets/img/ghee.png';
        if($product['category'] == 'Paneer' || $product['category'] == 'Yogurt') $img_path = 'assets/img/milk.png';
        ?>
        <img src="<?= $img_path ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    
    <div style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
        <div class="product-category" style="margin-bottom: 1rem; font-size: 1rem;"><?= htmlspecialchars($product['category']) ?></div>
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem;"><?= htmlspecialchars($product['name']) ?></h1>
        <div class="product-price" style="font-size: 2rem; margin-bottom: 1.5rem;">Rs. <?= number_format($product['price'], 2) ?></div>
        <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 1.5rem; line-height: 1.8;">
            <?= nl2br(htmlspecialchars($product['description'])) ?>
        </p>
        <p style="margin-bottom: 2rem;"><strong>Farm Source:</strong> <?= htmlspecialchars($product['farm_source']) ?></p>

        <div style="display: flex; gap: 1rem;">
            <form action="api/cart_action.php" method="POST" class="ajax-cart-form" style="flex: 1;">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="action" value="add">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">Add to Cart (One-off)</button>
            </form>
            
            <?php if(isset($_SESSION['user_id'])): ?>
            <form action="api/subscription_action.php" method="POST" style="flex: 1;">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-outline" style="width: 100%; padding: 1rem; font-size: 1.1rem; border-color: var(--secondary); color: var(--secondary);">Subscribe Daily</button>
            </form>
            <?php else: ?>
            <a href="login.php" class="btn btn-outline" style="flex: 1; padding: 1rem; font-size: 1.1rem; border-color: var(--secondary); color: var(--secondary); text-align: center;">Login to Subscribe</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
