<?php
// hospital/results.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Hospital') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/database.php';
require_once '../includes/hospital_queries.php';

$user_id = (int) $_SESSION['user_id'];
$hospital = fetch_hospital_account_by_user($conn, $user_id);
if (!$hospital || ($hospital['status'] ?? '') !== 'Approved') {
    header('Location: ../auth/login.php');
    exit;
}
$hospital_id = hospital_appointment_filter_id($conn, $user_id);

$message = '';

// Handle Result Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['result_id'])) {
    $result_id = $_POST['result_id'];
    $appointment_id = $_POST['appointment_id'];
    $type = $_POST['type'];
    $notes = trim($_POST['notes']);
    
    $is_completed = false;

    try {
        $conn->beginTransaction();
        
        if ($type === 'Test') {
            $test_result = $_POST['test_result'];
            $upd = $conn->prepare("UPDATE results SET test_result = :res, notes = :notes WHERE id = :id");
            $upd->bindParam(':res', $test_result);
            $upd->bindParam(':notes', $notes);
            $upd->bindParam(':id', $result_id);
            $upd->execute();
            
            if ($test_result === 'Positive' || $test_result === 'Negative') {
                $is_completed = true;
            }
        } else if ($type === 'Vaccination') {
            $vax_status = $_POST['vaccination_status'];
            $upd = $conn->prepare("UPDATE results SET vaccination_status = :stat, notes = :notes WHERE id = :id");
            $upd->bindParam(':stat', $vax_status);
            $upd->bindParam(':notes', $notes);
            $upd->bindParam(':id', $result_id);
            $upd->execute();
            
            if ($vax_status === 'Completed') {
                $is_completed = true;
            }
        }

        // If completed, update appointment status
        if ($is_completed) {
            $apt_upd = $conn->prepare("UPDATE appointments SET status = 'Completed' WHERE id = :aid");
            $apt_upd->bindParam(':aid', $appointment_id);
            $apt_upd->execute();
        }
        
        $conn->commit();
        $message = "Medical records updated successfully.";
    } catch(PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $message = "Error: " . $e->getMessage();
    }
}

// Check if specific appointment is selected via GET
$selected_appt = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch Approved appointments that need results
$stmt = $conn->prepare("
    SELECT a.id as appointment_id, a.type, a.appointment_date, u.name as patient_name, 
           v.vaccine_name, r.id as result_id, r.test_result, r.vaccination_status, r.notes
    FROM appointments a 
    JOIN users u ON a.patient_id = u.id 
    JOIN results r ON a.id = r.appointment_id
    LEFT JOIN vaccines v ON a.vaccine_id = v.id
    WHERE a.hospital_id = :hid AND a.status = 'Approved'
    ORDER BY a.appointment_date ASC
");
$stmt->bindParam(':hid', $hospital_id);
$stmt->execute();
$appointments = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="container">
    <h2>Update Medical Results</h2>
    
    <?php if(!empty($message)): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if(count($appointments) > 0): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php foreach($appointments as $a): ?>
                <div class="card" <?php echo ($selected_appt == $a['appointment_id']) ? 'style="border: 2px solid var(--primary-blue);"' : ''; ?>>
                    <h3 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($a['patient_name']); ?></h3>
                    <p style="color: var(--text-muted); margin-bottom: 1rem;">
                        <strong>Date:</strong> <?php echo date('M d, Y', strtotime($a['appointment_date'])); ?><br>
                        <strong>Type:</strong> <?php echo $a['type']; ?> 
                        <?php if($a['type'] === 'Vaccination') echo "(" . htmlspecialchars($a['vaccine_name']) . ")"; ?>
                    </p>
                    
                    <form method="post">
                        <input type="hidden" name="result_id" value="<?php echo $a['result_id']; ?>">
                        <input type="hidden" name="appointment_id" value="<?php echo $a['appointment_id']; ?>">
                        <input type="hidden" name="type" value="<?php echo $a['type']; ?>">
                        
                        <?php if($a['type'] === 'Test'): ?>
                            <div class="form-group">
                                <label class="form-label">Test Result</label>
                                <select name="test_result" class="form-control" required>
                                    <option value="Pending" <?php echo ($a['test_result'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Negative" <?php echo ($a['test_result'] == 'Negative') ? 'selected' : ''; ?>>Negative</option>
                                    <option value="Positive" <?php echo ($a['test_result'] == 'Positive') ? 'selected' : ''; ?>>Positive</option>
                                </select>
                            </div>
                        <?php elseif($a['type'] === 'Vaccination'): ?>
                            <div class="form-group">
                                <label class="form-label">Vaccination Status</label>
                                <select name="vaccination_status" class="form-control" required>
                                    <option value="Not Started" <?php echo ($a['vaccination_status'] == 'Not Started') ? 'selected' : ''; ?>>Not Started</option>
                                    <option value="Dose 1" <?php echo ($a['vaccination_status'] == 'Dose 1') ? 'selected' : ''; ?>>Dose 1 Completed</option>
                                    <option value="Dose 2" <?php echo ($a['vaccination_status'] == 'Dose 2') ? 'selected' : ''; ?>>Dose 2 Completed</option>
                                    <option value="Completed" <?php echo ($a['vaccination_status'] == 'Completed') ? 'selected' : ''; ?>>Fully Completed</option>
                                </select>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label class="form-label">Medical Notes</label>
                            <textarea name="notes" class="form-control" rows="2"><?php echo htmlspecialchars($a['notes'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn-primary" style="width: 100%;">Save Result & Complete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card" style="text-align: center;">
            <p>No pending results to update.</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
