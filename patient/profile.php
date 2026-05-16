<?php
// patient/profile.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$message = '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    try {
        $stmt = $conn->prepare("UPDATE users SET name = :name, phone = :phone, address = :address WHERE id = :id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->execute();
        
        $_SESSION['name'] = $name; // Update session name
        $message = "Profile updated successfully.";
    } catch(PDOException $e) {
        $message = "Error updating profile: " . $e->getMessage();
    }
}

// Fetch Current User Details
$stmt = $conn->prepare("SELECT name, email, phone, address FROM users WHERE id = :uid");
$stmt->bindParam(':uid', $user_id);
$stmt->execute();
$user = $stmt->fetch();

include '../includes/header.php';
?>

<div class="container" style="max-width: 600px;">
    <h2>Edit Profile</h2>
    
    <?php if(!empty($message)): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="post">
            <div class="form-group">
                <label class="form-label" for="email">Email Address (Cannot be changed)</label>
                <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="phone">Phone Number</label>
                <input type="tel" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="address">Address</label>
                <input type="text" name="address" id="address" class="form-control" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%;">Save Changes</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
