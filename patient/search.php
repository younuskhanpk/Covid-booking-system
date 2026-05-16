<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/hospital_queries.php';

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$hospitals = fetch_approved_hospitals($conn, $search_query);

include '../includes/header.php';
?>

<div class="container" style="padding-bottom: 4rem;">
    <h2 class="animate-fade-up">Find hospitals</h2>
    <div class="card animate-fade-up" style="margin-bottom: 2rem;">
        <form action="" method="get" style="display:flex;gap:1rem;flex-wrap:wrap;">
            <input type="text" name="q" class="form-control" placeholder="Hospital name or location..." value="<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="btn-primary">Search</button>
        </form>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem;">
        <?php if (count($hospitals) > 0): ?>
            <?php foreach ($hospitals as $h):
                $bookId = (int) ($h['hospital_user_id'] ?? $h['id']);
                $name = $h['hospital_name'] ?? '';
                ?>
                <div class="card animate-fade-up">
                    <h3><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p style="color:var(--text-muted);"><?php echo htmlspecialchars($h['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    <a href="book.php?hospital_id=<?php echo $bookId; ?>" class="btn-primary" style="width:100%;margin-top:1rem;">Book test or vaccination</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card" style="grid-column:1/-1;text-align:center;">No hospitals found.</div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
