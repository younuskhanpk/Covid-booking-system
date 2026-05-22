<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/hospital_queries.php';

$patient_id = (int) $_SESSION['user_id'];
$hospital_ref = isset($_GET['hospital_id']) ? (int) $_GET['hospital_id'] : 0;

if ($hospital_ref <= 0) {
    header('Location: search.php');
    exit;
}

$hospital = fetch_hospital_for_booking($conn, $hospital_ref);
if (!$hospital) {
    header('Location: search.php');
    exit;
}

$store_hospital_id = (int) ($hospital['booking_hospital_id'] ?? $hospital['user_id'] ?? $hospital_ref);
$vaccines = array();
$vax_sql = "SELECT * FROM vaccines WHERE availability_status = 'Available'";
$vax_result = mysqli_query($conn, $vax_sql);
if ($vax_result) {
    while ($vax_row = mysqli_fetch_assoc($vax_result)) {
        $vaccines[] = $vax_row;
    }
}
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $date = $_POST['appointment_date'] ?? '';
    $vaccine_id = null;

    if (!in_array($type, ['Test', 'Vaccination'], true)) {
        $error = 'Please select COVID-19 Test or Vaccination.';
    } elseif ($type === 'Vaccination') {
        $vaccine_id = isset($_POST['vaccine_id']) ? (int) $_POST['vaccine_id'] : 0;
        if ($vaccine_id <= 0) {
            $error = 'Please choose a vaccine for vaccination bookings.';
        }
    }

    if ($error === '') {
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            $error = 'Appointment date cannot be in the past.';
        } else {
            $typeSafe = mysqli_real_escape_string($conn, $type);
            $dateSafe = mysqli_real_escape_string($conn, $date);
            $vaxIdVal = $type === 'Vaccination' ? $vaccine_id : 'NULL';
            
            $sql = "INSERT INTO appointments (patient_id, hospital_id, type, vaccine_id, appointment_date) VALUES ($patient_id, $store_hospital_id, '$typeSafe', $vaxIdVal, '$dateSafe')";
            
            if (mysqli_query($conn, $sql)) {
                // Success: Redirect back to patient dashboard
                header('Location: index.php?msg=booked');
                exit;
            } else {
                $error = 'Booking could not be saved. Please try again. ' . mysqli_error($conn);
            }
        }
    }
}

$hDisplay = $hospital['hospital_name'] ?? '';
$hLoc = $hospital['location'] ?? '';

include '../includes/header.php';
?>

<style>
.unified-book { position: relative; }
.type-pills { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.type-pills label {
    flex: 1; min-width: 140px; text-align: center; padding: 1rem 1.25rem;
    border: 2px solid var(--border); border-radius: var(--radius-lg);
    font-weight: 700; cursor: pointer; transition: var(--transition-fast);
    background: var(--bg-main); color: var(--text-secondary);
}
.type-pills label:hover { border-color: var(--primary); color: var(--primary); }
#btype_t:checked ~ .type-pills label[for="btype_t"],
#btype_v:checked ~ .type-pills label[for="btype_v"] {
    border-color: var(--primary); background: var(--primary-light); color: var(--primary-dark);
}
.vaccine-only { display: none; }
#btype_v:checked ~ .vaccine-only { display: block; }
</style>

<div class="container" style="max-width: 640px; padding-bottom: 4rem;">
    <h2 class="animate-fade-up" style="margin-top: 1rem;">Book COVID-19 Test or Vaccination</h2>
    <div class="card" style="margin-bottom: 1.5rem; background: linear-gradient(135deg, var(--primary), var(--accent)); color: #fff;">
        <h3 style="color: #fff;"><?php echo htmlspecialchars($hDisplay, ENT_QUOTES, 'UTF-8'); ?></h3>
        <p><?php echo htmlspecialchars($hLoc, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <div class="card">
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form method="post" class="unified-book">
            <input class="tab-input" type="radio" name="type" id="btype_t" value="Test" checked>
            <input class="tab-input" type="radio" name="type" id="btype_v" value="Vaccination">
            <div class="type-pills">
                <label for="btype_t">COVID-19 Test</label>
                <label for="btype_v">Vaccination</label>
            </div>
            <div class="vaccine-only">
                <div class="form-group">
                    <label class="form-label">Vaccine</label>
                    <select name="vaccine_id" class="form-control">
                        <option value="">— Choose —</option>
                        <?php foreach ($vaccines as $v): ?>
                            <option value="<?php echo (int) $v['id']; ?>"><?php echo htmlspecialchars($v['vaccine_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" name="appointment_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">Confirm booking</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
