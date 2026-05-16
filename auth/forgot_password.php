<?php
// auth/forgot_password.php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'reset') {
        $email = trim($_POST['email']);
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password !== $confirm_password) {
            $error = "Passwords do not match.";
            $step = 2;
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':email', $email);
            
            if ($stmt->execute()) {
                $success = "Your password has been successfully reset. You can now log in.";
                $step = 3;
            } else {
                $error = "Failed to update password. Please try again.";
                $step = 2;
            }
        }
    } else {
        $email = trim($_POST['email']);
        
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Email exists, proceed to reset step
            $step = 2;
        } else {
            $error = "No account found with that email address.";
            $step = 1;
        }
    }
} else {
    $step = 1;
}

include '../includes/header.php';
?>

<div class="container animate-fade-up">
    <div class="auth-wrapper" style="max-width: 600px; margin: 4rem auto;">
        <div class="auth-form-container" style="padding: 4rem;">
            <h2 style="font-size: 2.2rem; margin-bottom: 0.5rem; letter-spacing: -1px;">Password Recovery</h2>
            
            <?php if($step === 1): ?>
                <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 1.1rem;">Enter your registered email address and we'll help you reset your password securely.</p>
                
                <?php if($error): ?>
                    <div class="alert alert-error" style="background: var(--danger-bg); color: var(--danger); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border: 1px solid rgba(239, 68, 68, 0.3);">
                        <span style="margin-right: 10px; font-weight: bold;">!</span> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="you@example.com">
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;">Verify Email Account</button>
                </form>
                
            <?php elseif($step === 2): ?>
                <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 1.1rem;">Account verified. Please enter your new secure password below.</p>
                
                <?php if($error): ?>
                    <div class="alert alert-error" style="background: var(--danger-bg); color: var(--danger); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border: 1px solid rgba(239, 68, 68, 0.3);">
                        <span style="margin-right: 10px; font-weight: bold;">!</span> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="hidden" name="action" value="reset">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required placeholder="Create a strong password" minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required placeholder="Repeat your new password" minlength="6">
                    </div>
                    
                    <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem; background: var(--success); border: none;">Update Password</button>
                </form>
                
            <?php elseif($step === 3): ?>
                <div class="alert alert-success" style="background: var(--success-bg); color: var(--success); padding: 2rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border: 1px solid rgba(16, 185, 129, 0.3); text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
                    <h3 style="margin-bottom: 1rem; color: var(--success-dark);">Success!</h3>
                    <p style="font-size: 1.1rem; color: var(--success);"><?php echo htmlspecialchars($success); ?></p>
                </div>
            <?php endif; ?>
            
            <p style="text-align: center; margin-top: 2rem; font-size: 1rem; color: var(--text-secondary);">
                Remember your password? <a href="login.php" style="font-weight: 700; color: var(--primary);">Return to Login</a>
            </p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
