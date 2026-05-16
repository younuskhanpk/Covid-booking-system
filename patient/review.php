<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/hospital_queries.php';

$patient_id = (int) $_SESSION['user_id'];
$error = '';
$success = '';
$reviews_table_ok = true;
$hName = hospital_name_expr($conn);

$eligibleSql = "
    SELECT a.id, a.type, a.appointment_date, {$hName} AS hospital_name
    FROM appointments a
    " . hospital_join_sql($conn, 'a') . "
    LEFT JOIN reviews rev ON rev.appointment_id = a.id
    WHERE a.patient_id = :pid AND a.status = 'Completed' AND rev.id IS NULL
    ORDER BY a.appointment_date DESC
";

$eligible = [];
try {
    $elStmt = $conn->prepare($eligibleSql);
    $elStmt->execute([':pid' => $patient_id]);
    $eligible = $elStmt->fetchAll();
} catch (PDOException $e) {
    if ((int) $e->getCode() === 42 || strpos($e->getMessage(), 'reviews') !== false) {
        $reviews_table_ok = false;
    } else {
        throw $e;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $reviews_table_ok) {
    $appointment_id = (int) ($_POST['appointment_id'] ?? 0);
    $rating = (int) ($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($appointment_id <= 0 || $rating < 1 || $rating > 5) {
        $error = 'Please select a visit and give a star rating (1–5).';
    } elseif (strlen($comment) < 10) {
        $error = 'Please write at least 10 characters in your review.';
    } else {
        $chk = $conn->prepare("
            SELECT a.id, a.hospital_id
            FROM appointments a
            LEFT JOIN reviews rev ON rev.appointment_id = a.id
            WHERE a.id = :aid AND a.patient_id = :pid AND a.status = 'Completed' AND rev.id IS NULL
        ");
        $chk->execute([':aid' => $appointment_id, ':pid' => $patient_id]);
        $ap = $chk->fetch();
        if (!$ap) {
            $error = 'This visit cannot be reviewed.';
        } else {
            try {
                $hid = (int) $ap['hospital_id'];
                $ins = $conn->prepare("INSERT INTO reviews (patient_id, hospital_id, appointment_id, rating, comment, status) VALUES (?,?,?,?,?,'Approved')");
                $ins->execute([$patient_id, $hid, $appointment_id, $rating, $comment]);
                $success = 'Thank you! Your review is now visible to patients, hospitals, and administrators.';
                $elStmt = $conn->prepare($eligibleSql);
                $elStmt->execute([':pid' => $patient_id]);
                $eligible = $elStmt->fetchAll();
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate') !== false || stripos($e->getMessage(), 'uniq') !== false) {
                    $error = 'You already reviewed this visit.';
                } elseif (strpos($e->getMessage(), 'reviews') !== false) {
                    $reviews_table_ok = false;
                    $error = 'Import reviews_table.sql or unified_users_migration.sql in phpMyAdmin.';
                } else {
                    $error = 'Could not save review.';
                }
            }
        }
    }
}

include '../includes/header.php';
?>

<style>
.star-picker { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 0.35rem; margin: 0.5rem 0 1rem; }
.star-picker input { position: absolute; opacity: 0; pointer-events: none; }
.star-picker label { font-size: 2.25rem; color: #cbd5e1; cursor: pointer; transition: color 0.2s, transform 0.2s; }
.star-picker label:hover,
.star-picker label:hover ~ label,
.star-picker input:checked ~ label { color: #fbbf24; transform: scale(1.08); }
.review-form-card { max-width: 640px; margin: 2rem auto 4rem; }
</style>

<div class="container review-form-card">
    <div class="card animate-fade-up">
        <h2>Submit your review</h2>
        <p style="color:var(--text-muted);">Rate your experience after a <strong>completed</strong> COVID-19 test or vaccination.</p>

        <?php if (!$reviews_table_ok): ?>
            <div class="alert alert-error" style="padding:1rem;background:#fff7ed;border:1px solid #fdba74;border-radius:8px;">
                <strong>Setup required:</strong> Run <code>unified_users_migration.sql</code> and <code>reviews_table.sql</code> in phpMyAdmin on database <code>covid_booking_db</code>.
            </div>
        <?php endif; ?>

        <?php if ($error): ?><div class="alert alert-error" style="margin:1rem 0;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success" style="margin:1rem 0;background:#ecfdf5;padding:1rem;border-radius:8px;"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>

        <?php if ($reviews_table_ok && count($eligible) > 0): ?>
            <form method="post">
                <div class="form-group">
                    <label class="form-label">Visit</label>
                    <select name="appointment_id" class="form-control" required>
                        <option value="">— Select completed visit —</option>
                        <?php foreach ($eligible as $e): ?>
                            <option value="<?php echo (int) $e['id']; ?>">
                                <?php echo htmlspecialchars($e['hospital_name'], ENT_QUOTES, 'UTF-8'); ?> — <?php echo htmlspecialchars($e['type'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo date('M j, Y', strtotime($e['appointment_date'])); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Your rating</label>
                    <div class="star-picker">
                        <?php for ($s = 5; $s >= 1; $s--): ?>
                            <input type="radio" name="rating" id="star<?php echo $s; ?>" value="<?php echo $s; ?>" required>
                            <label for="star<?php echo $s; ?>" title="<?php echo $s; ?> stars">★</label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Your review</label>
                    <textarea name="comment" class="form-control" rows="5" required minlength="10" placeholder="Share your experience at the hospital..."></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;">Publish review</button>
            </form>
        <?php elseif ($reviews_table_ok && !$success): ?>
            <p style="margin-top:1rem;">No completed visits ready for review. <a href="search.php">Book a visit</a> first.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
