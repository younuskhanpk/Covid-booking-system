<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'Admin') {
        header('Location: ../admin/index.php');
    } elseif ($_SESSION['role'] === 'Hospital') {
        header('Location: ../hospital/index.php');
    } else {
        header('Location: ../patient/index.php');
    }
    exit;
}

require_once '../config/database.php';
require_once '../includes/auth_helpers.php';
require_once '../includes/hospital_queries.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare('SELECT id, name, password, role, role_id, facility_status FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
    } catch (PDOException $e) {
        $stmt = $conn->prepare('SELECT id, name, password, role FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        if ($user) {
            $user['role_id'] = role_id_from_name($user['role']);
        }
    }

    if ($user && password_verify($password, $user['password'])) {
        $roleName = !empty($user['role']) ? $user['role'] : role_name_from_id((int) ($user['role_id'] ?? 3));

        if ($roleName === 'Hospital') {
            $status = $user['facility_status'] ?? 'Pending';
            if ($status === 'Pending') {
                $error = 'Your hospital account is pending admin approval.';
            } elseif ($status === 'Rejected') {
                $error = 'Your hospital registration was rejected.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = 'Hospital';
                $_SESSION['role_id'] = 2;
                header('Location: ../hospital/index.php');
                exit;
            }
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $roleName;
            $_SESSION['role_id'] = (int) ($user['role_id'] ?? role_id_from_name($roleName));
            if ($roleName === 'Admin') {
                header('Location: ../admin/index.php');
            } else {
                header('Location: ../patient/index.php');
            }
            exit;
        }
    } else {
        $error = 'Invalid email or password.';
    }
}

include '../includes/header.php';
?>

<div class="container animate-fade-up">
    <div class="auth-wrapper">
        <div class="auth-form-container">
            <h2>Welcome back</h2>
            <?php if ($error): ?>
                <div class="alert alert-error" style="padding:1rem;margin-bottom:1rem;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;">Log in</button>
            </form>
            <p style="text-align:center;margin-top:1.5rem;"><a href="register.php">Create account</a></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
