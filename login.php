<?php
require_once 'includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>

<div class="form-container">
    <h2 class="section-title" style="margin-bottom: 1.5rem;">Welcome Back</h2>
    <?php if($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
    </form>
    <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
        Don't have an account? <a href="register.php" style="color: var(--primary); font-weight: 600;">Sign up</a>
    </p>
    
    <div style="margin-top: 2rem; border-top: 1px solid #E5E7EB; padding-top: 1.5rem;">
        <p style="text-align: center; margin-bottom: 1rem; color: var(--text-muted); font-size: 0.875rem;">Or continue with (Mock)</p>
        <button class="btn btn-outline" style="width: 100%; margin-bottom: 0.5rem; display: flex; justify-content: center; align-items: center; gap: 0.5rem;">
            Google
        </button>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
