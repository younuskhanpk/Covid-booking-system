<?php
// auth/register.php
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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $role_id = role_id_from_name($role);
    
    try {
        $conn->beginTransaction();
        
        $chk = $conn->prepare('SELECT id FROM users WHERE email = :email');
        $chk->execute([':email' => $email]);
        
        if ($chk->rowCount() > 0) {
            throw new Exception('Email is already registered.');
        }
        
        if ($role === 'Hospital') {
            $hospital_name = trim($_POST['hospital_name']);
            $location = trim($_POST['location']);
            $license = trim($_POST['license_number']);
            $stmt = $conn->prepare('INSERT INTO users (name, email, password, role, role_id, phone, address, hospital_name, location, license_number, facility_status) VALUES (:name, :email, :password, :role, :rid, :phone, :address, :hname, :loc, :lic, \'Pending\')');
            $stmt->execute([
                ':name' => $name, ':email' => $email, ':password' => $password, ':role' => $role,
                ':rid' => $role_id, ':phone' => $phone, ':address' => $address,
                ':hname' => $hospital_name, ':loc' => $location, ':lic' => $license,
            ]);
            $success = 'Hospital registration successful! Pending admin approval.';
        } else {
            $stmt = $conn->prepare('INSERT INTO users (name, email, password, role, role_id, phone, address) VALUES (:name, :email, :password, :role, :rid, :phone, :address)');
            $stmt->execute([':name' => $name, ':email' => $email, ':password' => $password, ':role' => $role, ':rid' => $role_id, ':phone' => $phone, ':address' => $address]);
            $success = 'Registration successful! You can now log in.';
        }
        
        $conn->commit();
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $error = $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="container animate-fade-up">
    <div class="auth-wrapper">
        <div class="auth-image" style="background: linear-gradient(-45deg, var(--accent), var(--primary-dark), var(--primary));">
            <div class="auth-image-content">
                <h2 style="font-size: 2.5rem;">Join VaxiCare Today</h2>
                <p style="font-size: 1.15rem; line-height: 1.6;">Create an account to book your appointments, access medical records, and stay updated with the latest health guidelines.</p>
                
                <ul style="list-style: none; text-align: left; margin-top: 2rem; display: inline-block;">
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
                        <span style="background: rgba(255,255,255,0.2); width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">✓</span> Fast Registration
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
                        <span style="background: rgba(255,255,255,0.2); width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">✓</span> Secure Records
                    </li>
                    <li style="display: flex; align-items: center; gap: 10px;">
                        <span style="background: rgba(255,255,255,0.2); width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">✓</span> Instant Bookings
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="auth-form-container">
            <h2 style="font-size: 2.2rem; margin-bottom: 0.5rem; letter-spacing: -1px;">Create Account</h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 1.1rem;">Fill in your details to get started.</p>
            
            <?php if($error): ?>
                <div class="alert alert-error" style="background: var(--danger-bg); color: var(--danger); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border: 1px solid rgba(239, 68, 68, 0.3);">
                    <span style="margin-right: 10px; font-weight: bold;">!</span> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success" style="background: var(--success-bg); color: var(--success); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border: 1px solid rgba(16, 185, 129, 0.3);">
                    <span style="margin-right: 10px; font-weight: bold;">✓</span> <?php echo htmlspecialchars($success); ?> <br><br> <a href="login.php" style="font-weight: bold; color: var(--success);">Proceed to Log In &rarr;</a>
                </div>
            <?php else: ?>
            
            <div class="tabs">
                <style>
                    /* Inline CSS to guarantee tab functionality */
                    .tab-content { display: none; padding-top: 1rem; animation: fadeInUp 0.4s ease forwards; }
                    #tab_patient:checked ~ #content_patient { display: block; }
                    #tab_hospital:checked ~ #content_hospital { display: block; }
                    
                    .tab-label { padding: 1rem; font-weight: 700; color: #94a3b8; border-bottom: 3px solid #e2e8f0; cursor: pointer; transition: all 0.3s; }
                    #tab_patient:checked ~ .tab-labels label[for="tab_patient"],
                    #tab_hospital:checked ~ .tab-labels label[for="tab_hospital"] {
                        color: var(--primary); border-bottom-color: var(--primary);
                    }
                </style>
                <input type="radio" name="reg_tab" id="tab_patient" class="tab-input" checked style="display:none;">
                <input type="radio" name="reg_tab" id="tab_hospital" class="tab-input" style="display:none;">
                
                <div class="tab-labels" style="display: flex; width: 100%; margin-bottom: 1.5rem;">
                    <label for="tab_patient" class="tab-label" style="flex:1; text-align:center;">Patient Registration</label>
                    <label for="tab_hospital" class="tab-label" style="flex:1; text-align:center; ">Hospital Registration</label>
                </div>
                
                <!-- Patient Form -->
                <div id="content_patient" class="tab-content">
                    <form method="POST" action="">
                        <input type="hidden" name="role" value="Patient">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required placeholder="you@example.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" required placeholder="+1 234 567 8900">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Home Address</label>
                            <textarea name="address" class="form-control" required placeholder="Your full residential address" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Create a strong password" minlength="6">
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;">Register as Patient</button>
                    </form>
                </div>
                
                <!-- Hospital Form -->
                <div id="content_hospital" class="tab-content">
                    <form method="POST" action="">
                        <input type="hidden" name="role" value="Hospital">
                        <div class="form-group">
                            <label class="form-label">Representative Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="Jane Smith">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hospital Email</label>
                            <input type="email" name="email" class="form-control" required placeholder="contact@hospital.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Contact Phone Number</label>
                            <input type="text" name="phone" class="form-control" required placeholder="+1 800 123 4567">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Full Address / Location</label>
                            <textarea name="address" class="form-control" required placeholder="Official hospital address" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Create a strong password" minlength="6">
                        </div>
                        <hr style="margin: 1.5rem 0; border: 0; border-top: 1px solid var(--border);">
                        <h4 style="margin-bottom: 1rem; color: var(--primary);">Hospital Specific Details</h4>
                        <div class="form-group">
                            <label class="form-label">Hospital Name</label>
                            <input type="text" name="hospital_name" class="form-control" required placeholder="City General Hospital">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Location (City/Area)</label>
                            <input type="text" name="location" class="form-control" required placeholder="Downtown Area">
                        </div>
                        <div class="form-group">
                            <label class="form-label">License Number</label>
                            <input type="text" name="license_number" class="form-control" required placeholder="LIC-12345678">
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;">Register as Hospital</button>
                    </form>
                </div>
            </div>
            
            <?php endif; ?>
            
            <p style="text-align: center; margin-top: 2rem; font-size: 1rem; color: var(--text-secondary);">
                Already have an account? <a href="login.php" style="font-weight: 700; color: var(--primary);">Log In</a>
            </p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>