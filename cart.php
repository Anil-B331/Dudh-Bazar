<?php
require_once 'includes/header.php';

$cart = $_SESSION['cart'] ?? [];
$cart_products = [];
$total = 0;

if (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $cart_products = $stmt->fetchAll();
}
?>

<div style="margin-top: 2rem;">
    <h1 class="section-title">Your Cart</h1>

    <?php if (empty($cart_products)): ?>
        <div style="text-align: center; padding: 4rem 0;">
            <div style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;">🛒</div>
            <h2 style="margin-bottom: 1rem; color: var(--text-muted);">Your cart is empty</h2>
            <a href="products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cart_products as $item): 
                                $qty = $cart[$item['id']];
                                $subtotal = $item['price'] * $qty;
                                $total += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($item['name']) ?></strong>
                                </td>
                                <td>Rs. <?= number_format($item['price'], 2) ?></td>
                                <td><?= $qty ?></td>
                                <td>Rs. <?= number_format($subtotal, 2) ?></td>
                                <td>
                                    <form action="api/cart_action.php" method="POST">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                        <button type="submit" style="background: none; border: none; color: #EF4444; cursor: pointer; font-weight: bold;">Remove</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dashboard-card" style="height: fit-content;">
                <h3 style="margin-bottom: 1.5rem;">Order Summary</h3>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span>Subtotal</span>
                    <span>Rs. <?= number_format($total, 2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                    <span>Delivery Fee</span>
                    <span>Rs. 0.00</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 2rem; font-weight: 700; font-size: 1.25rem; border-top: 1px solid #E5E7EB; padding-top: 1rem;">
                    <span>Total</span>
                    <span style="color: var(--primary);">Rs. <?= number_format($total, 2) ?></span>
                </div>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <form action="api/checkout_action.php" method="POST">
                        <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;">Payment Method</label>
                        <select name="payment_method" class="form-control" style="margin-bottom: 1.5rem;">
                            <option value="wallet">Digital Wallet</option>
                            <option value="cash">Cash on Delivery</option>
                        </select>
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem;">Proceed to Checkout</button>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary" style="width: 100%; display: block; text-align: center; padding: 1rem;">Login to Checkout</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
