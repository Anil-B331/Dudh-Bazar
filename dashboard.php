<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch subscriptions
$subStmt = $pdo->prepare("SELECT s.*, p.name as product_name FROM subscriptions s JOIN products p ON s.product_id = p.id WHERE s.user_id = ? ORDER BY s.created_at DESC");
$subStmt->execute([$user_id]);
$subscriptions = $subStmt->fetchAll();

// Fetch transactions
$txStmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$txStmt->execute([$user_id]);
$transactions = $txStmt->fetchAll();

// Fetch orders
$orderStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$orderStmt->execute([$user_id]);
$orders = $orderStmt->fetchAll();

$message = $_GET['msg'] ?? '';
?>

<div class="dashboard-grid" style="margin-top: 2rem;">
    <!-- Left Column: Wallet -->
    <div>
        <?php if($message == 'topup_success'): ?>
            <div class="alert alert-success">Wallet top-up successful!</div>
        <?php endif; ?>
        
        <div class="dashboard-card" style="margin-bottom: 2rem; background: linear-gradient(135deg, #10B981 0%, #047857 100%); color: white;">
            <h3 style="font-weight: 500; font-size: 1.1rem; opacity: 0.9;">Digital Wallet Balance</h3>
            <div class="wallet-balance" style="color: white; margin: 0.5rem 0;">Rs. <?= number_format($user['wallet_balance'], 2) ?></div>
            <p style="font-size: 0.875rem; opacity: 0.8; margin-bottom: 1.5rem;">Use this balance for daily milk subscriptions and fast checkout.</p>
            
            <form action="api/wallet_topup.php" method="POST">
                <label style="display: block; font-size: 0.875rem; margin-bottom: 0.5rem;">Top-up Amount (Rs.)</label>
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                    <button type="button" class="btn btn-outline" style="border-color: white; color: white;" onclick="document.getElementById('topup_amount').value=500">500</button>
                    <button type="button" class="btn btn-outline" style="border-color: white; color: white;" onclick="document.getElementById('topup_amount').value=1000">1000</button>
                    <button type="button" class="btn btn-outline" style="border-color: white; color: white;" onclick="document.getElementById('topup_amount').value=2000">2000</button>
                </div>
                <input type="number" id="topup_amount" name="amount" class="form-control" placeholder="Custom Amount" required min="100" style="margin-bottom: 1rem; border: none;">
                
                <label style="display: block; font-size: 0.875rem; margin-bottom: 0.5rem;">Payment Method</label>
                <select name="method" class="form-control" style="margin-bottom: 1rem; border: none;">
                    <option value="eSewa">eSewa (Mock)</option>
                    <option value="Fonepay">Fonepay (Mock)</option>
                </select>
                <button type="submit" class="btn" style="background: white; color: #047857; width: 100%;">Top Up Wallet</button>
            </form>
        </div>

        <div class="dashboard-card">
            <h3 style="margin-bottom: 1rem;">Recent Transactions</h3>
            <?php if(empty($transactions)): ?>
                <p style="color: var(--text-muted);">No transactions yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($transactions as $tx): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($tx['created_at'])) ?></td>
                                <td>
                                    <?php if($tx['type'] == 'topup'): ?>
                                        <span class="badge badge-active">Top Up</span>
                                    <?php else: ?>
                                        <span class="badge" style="background: #FEE2E2; color: #991B1B;">Deduction</span>
                                    <?php endif; ?>
                                </td>
                                <td>Rs. <?= number_format($tx['amount'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Subscriptions & Orders -->
    <div>
        <div class="dashboard-card" style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3>Active Subscriptions</h3>
                <a href="products.php" class="btn btn-outline" style="font-size: 0.875rem; padding: 0.25rem 1rem;">+ New</a>
            </div>
            
            <?php if(empty($subscriptions)): ?>
                <div style="text-align: center; padding: 2rem 0; color: var(--text-muted);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">📅</div>
                    <p>No active subscriptions. Get fresh milk delivered daily!</p>
                </div>
            <?php else: ?>
                <div class="product-grid" style="grid-template-columns: 1fr;">
                    <?php foreach($subscriptions as $sub): ?>
                        <div class="product-card" style="flex-direction: row; align-items: center; padding: 1rem;">
                            <div style="flex: 1;">
                                <h4 style="margin-bottom: 0.25rem; font-size: 1.1rem;"><?= htmlspecialchars($sub['product_name']) ?> (Qty: <?= $sub['quantity'] ?>)</h4>
                                <p style="color: var(--text-muted); font-size: 0.875rem;">Daily Delivery</p>
                            </div>
                            <div style="margin-right: 1.5rem;">
                                <?php if($sub['status'] == 'active'): ?>
                                    <span class="badge badge-active">Active</span>
                                <?php elseif($sub['status'] == 'paused'): ?>
                                    <span class="badge badge-paused">Vacation Mode</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #F3F4F6; color: #374151;">Cancelled</span>
                                <?php endif; ?>
                            </div>
                            <form action="api/subscription_action.php" method="POST">
                                <input type="hidden" name="sub_id" value="<?= $sub['id'] ?>">
                                <?php if($sub['status'] == 'active'): ?>
                                    <input type="hidden" name="action" value="pause">
                                    <button type="submit" class="btn btn-outline" style="padding: 0.25rem 1rem; border-color: #F59E0B; color: #D97706;">Pause</button>
                                <?php elseif($sub['status'] == 'paused'): ?>
                                    <input type="hidden" name="action" value="resume">
                                    <button type="submit" class="btn btn-outline" style="padding: 0.25rem 1rem; border-color: #10B981; color: #10B981;">Resume</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="dashboard-card">
            <h3 style="margin-bottom: 1.5rem;">Order History</h3>
            <?php if(empty($orders)): ?>
                <p style="color: var(--text-muted);">No orders yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                            <tr>
                                <td>#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                <td><span class="badge badge-active"><?= htmlspecialchars($order['status']) ?></span></td>
                                <td>Rs. <?= number_format($order['total_amount'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
