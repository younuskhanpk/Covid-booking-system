<?php
// hospital/index.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Hospital') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/database.php';
require_once '../includes/svg_icons.php';
require_once '../includes/hospital_queries.php';

$user_id = (int) $_SESSION['user_id'];
$hospital = fetch_hospital_account_by_user($conn, $user_id);

if (!$hospital || ($hospital['status'] ?? '') !== 'Approved') {
    session_destroy();
    header('Location: ../auth/login.php');
    exit;
}

$apptHospitalFilter = $user_id;

// Get counts
$pending_appts = $conn->query("SELECT COUNT(*) FROM appointments WHERE hospital_id = $apptHospitalFilter AND status = 'Pending'")->fetchColumn();
$approved_appts = $conn->query("SELECT COUNT(*) FROM appointments WHERE hospital_id = $apptHospitalFilter AND status = 'Approved'")->fetchColumn();
$completed_appts = $conn->query("SELECT COUNT(*) FROM appointments WHERE hospital_id = $apptHospitalFilter AND status = 'Completed'")->fetchColumn();

// Fetch recent completed appointments to show latest updates
$recent_stmt = $conn->query("
    SELECT a.type, a.appointment_date, u.name as patient_name, r.test_result, r.vaccination_status
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    LEFT JOIN results r ON a.id = r.appointment_id
    WHERE a.hospital_id = $apptHospitalFilter AND a.status = 'Completed'
    ORDER BY r.updated_at DESC LIMIT 5
");
$recent_updates = $recent_stmt->fetchAll();

$reviewsRows = [];
try {
    $rv = $conn->prepare('SELECT r.rating, r.comment, u.name AS patient_name FROM reviews r INNER JOIN users u ON r.patient_id = u.id WHERE r.hospital_id = :hid AND r.status = \'Approved\' ORDER BY r.created_at DESC LIMIT 6');
    $rv->bindParam(':hid', $apptHospitalFilter, PDO::PARAM_INT);
    $rv->execute();
    $reviewsRows = $rv->fetchAll();
} catch (Throwable $e) {
    $reviewsRows = [];
}

include '../includes/header.php';
?>

<style>
/* ==========================================================================
   PREMIUM HOSPITAL DASHBOARD STYLES (Massive UI Expansion)
   ========================================================================== */
.hospital-dashboard {
    padding: 0 0 6rem;
    font-family: var(--font-sans);
    background-color: var(--bg-main);
    position: relative;
    z-index: 0;
    isolation: isolate;
}

/* --- Hero Section with Background Image --- */
.hospital-hero {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(30, 41, 59, 0.85)), url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover no-repeat;
    background-attachment: fixed;
    border-radius: 0 0 var(--radius-xl) var(--radius-xl);
    padding: 6rem 3rem 5rem;
    position: relative;
    overflow: hidden;
    margin-bottom: 4rem;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.5);
    color: white;
}

.hospital-hero::after {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: url('data:image/svg+xml;utf8,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" stroke="rgba(255,255,255,0.03)" stroke-width="2" fill="none"/></svg>') repeat;
    opacity: 0.5;
    animation: slideBg 40s linear infinite;
    z-index: 0;
    pointer-events: none;
}

@keyframes slideBg {
    from { background-position: 0 0; }
    to { background-position: 500px 500px; }
}

.hospital-hero-content {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 3rem;
    max-width: 1200px;
    margin: 0 auto;
}

.hospital-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.8rem;
    background: rgba(16, 185, 129, 0.15);
    color: #34d399;
    padding: 0.6rem 1.5rem;
    border-radius: 50px;
    font-weight: 800;
    font-size: 0.9rem;
    letter-spacing: 1.5px;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(16, 185, 129, 0.4);
    backdrop-filter: blur(10px);
}

.hospital-badge::before {
    content: '';
    width: 10px; height: 10px;
    background: #34d399;
    border-radius: 50%;
    box-shadow: 0 0 12px #34d399;
    animation: pulseGlow 2s infinite;
}

.hospital-hero h2 {
    font-size: 3.8rem;
    margin-bottom: 0.8rem;
    color: white;
    text-shadow: 0 4px 20px rgba(0,0,0,0.5);
    font-weight: 900;
    line-height: 1.1;
}

.hospital-hero p {
    font-size: 1.25rem;
    opacity: 0.85;
    max-width: 600px;
    line-height: 1.6;
}

.hospital-info-bar {
    display: flex; gap: 2rem; margin-top: 2rem;
}

.h-info-item {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.95rem; opacity: 0.9;
}

.h-info-item svg { width: 20px; height: 20px; color: var(--accent); }

.date-widget {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(20px);
    padding: 2rem 3rem;
    border-radius: var(--radius-xl);
    border: 1px solid rgba(255,255,255,0.1);
    text-align: center;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.date-widget .date-label {
    font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; opacity: 0.7; margin-bottom: 0.5rem;
}

.date-widget .date-value {
    font-size: 2rem; font-weight: 800; font-family: var(--font-heading); color: white;
}

.dashboard-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* --- Core Stats Cards --- */
.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2.5rem;
    margin-top: 2rem;
    position: relative;
    z-index: 10;
    margin-bottom: 5rem;
}

.appt-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(15px);
    border-radius: var(--radius-xl);
    padding: 3rem 2.5rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0,0,0,0.06);
    border: 1px solid rgba(255,255,255,0.8);
    transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    z-index: 1;
}

.appt-card::before {
    content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 8px;
    transition: height 0.4s ease;
    z-index: -1;
}

.appt-card:hover {
    transform: translateY(-15px);
    box-shadow: 0 30px 60px rgba(0,0,0,0.12);
}

.appt-card:hover::before {
    height: 100%;
    opacity: 0.05;
}

.appt-card-pending::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.appt-card-approved::before { background: linear-gradient(90deg, var(--primary), var(--accent)); }
.appt-card-completed::before { background: linear-gradient(90deg, #10b981, #34d399); }

.appt-icon .svg-icon { width: 3rem; height: 3rem; margin: 0 auto; color: inherit; }

.appt-card:hover .appt-icon { transform: scale(1.15) translateY(-10px); }
.appt-card-pending .appt-icon { color: #f59e0b; }
.appt-card-approved .appt-icon { color: var(--primary); }
.appt-card-completed .appt-icon { color: #10b981; }

.appt-value {
    font-size: 4.5rem; font-weight: 900; font-family: var(--font-heading); line-height: 1; margin-bottom: 0.5rem; color: var(--text-primary);
}

.appt-title {
    color: var(--text-secondary); font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 2.5rem; font-size: 0.95rem;
}

.appt-action {
    display: inline-block; padding: 1rem 2.5rem; border-radius: 50px; font-weight: 700; transition: all 0.3s; text-decoration: none; font-size: 1.05rem; width: 100%;
}

.appt-card-pending .appt-action { background: #fffbeb; color: #d97706; border: 2px solid #fde68a; }
.appt-card-pending .appt-action:hover { background: #f59e0b; color: white; box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3); border-color: #f59e0b;}

.appt-card-approved .appt-action { background: var(--primary-light); color: var(--primary-dark); border: 2px solid rgba(79,70,229,0.2); }
.appt-card-approved .appt-action:hover { background: var(--primary); color: white; box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3); border-color: var(--primary);}

.appt-card-completed .appt-action { background: #ecfdf5; color: #059669; border: 2px solid #a7f3d0; cursor: default; }

/* --- Complex Layout Grid (Main Dashboard) --- */
.dashboard-grid-complex {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
    margin-bottom: 5rem;
}

.section-header {
    font-size: 1.8rem; font-weight: 900; color: var(--text-primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;
}
.section-header::before {
    content: ''; display: block; width: 6px; height: 30px; background: var(--primary); border-radius: 10px;
}

/* Panel Design */
.panel {
    background: white; border-radius: var(--radius-xl); padding: 2.5rem; box-shadow: var(--shadow-md); border: 1px solid var(--border);
}

/* Recent Updates Table */
.recent-updates-list { list-style: none; padding: 0; margin: 0; }
.update-item {
    display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 0; border-bottom: 1px solid var(--border);
}
.update-item:last-child { border-bottom: none; padding-bottom: 0; }
.update-patient { font-weight: 700; color: var(--text-primary); font-size: 1.1rem; margin-bottom: 0.3rem;}
.update-type { font-size: 0.9rem; color: var(--text-muted); }
.update-result { padding: 0.4rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; }

/* Resource Management */
.resource-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
.resource-item {
    background: var(--bg-main); padding: 1.5rem; border-radius: var(--radius-lg); border-left: 4px solid var(--accent);
}
.resource-header { display: flex; justify-content: space-between; margin-bottom: 0.8rem; font-weight: 700; color: var(--text-primary); }
.progress-bar-bg { width: 100%; height: 8px; background: rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden; }
.progress-bar-fill { height: 100%; background: linear-gradient(90deg, var(--accent), var(--primary)); border-radius: 10px; }

/* --- Testimonials & Reviews --- */
.reviews-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 5rem; }
.review-card {
    background: white; padding: 2.5rem; border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); border: 1px solid var(--border);
    transition: transform 0.3s;
}
.review-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }
.review-stars { color: #fbbf24; font-size: 1.2rem; margin-bottom: 1rem; }
.review-text { font-size: 1.05rem; font-style: italic; color: var(--text-secondary); margin-bottom: 1.5rem; line-height: 1.6; }
.review-author { display: flex; align-items: center; gap: 1rem; }
.author-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--bg-main); display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--primary);}

@media (max-width: 992px) {
    .dashboard-grid-complex { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .hospital-hero { padding: 4rem 2rem 8rem; text-align: center; }
    .hospital-hero-content { justify-content: center; }
    .hospital-info-bar { flex-direction: column; align-items: center; gap: 1rem; }
}
</style>

<div class="hospital-dashboard portal-page">

<!-- HOSPITAL HERO SECTION -->
<div class="hospital-hero animate-fade-up">
    <div class="hospital-hero-content">
        <div>
            <div class="hospital-badge">
                OFFICIAL VERIFIED FACILITY
            </div>
            <h2><?php echo htmlspecialchars($hospital['hospital_name']); ?></h2>
            <p>Welcome to your hospital management portal. Oversee incoming test and vaccination requests, manage medical stock, and update patient results securely in real-time.</p>
            
            <div class="hospital-info-bar">
                <div class="h-info-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <?php echo htmlspecialchars($hospital['location']); ?>
                </div>
                <div class="h-info-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    License: <?php echo htmlspecialchars($hospital['license_number']); ?>
                </div>
            </div>
        </div>
        
        <div class="date-widget">
            <div class="date-label">Today's Date</div>
            <div class="date-value"><?php echo date('M d, Y'); ?></div>
            <div style="font-size: 1.2rem; color: var(--accent); margin-top: 0.5rem; font-weight: 600;"><?php echo date('l'); ?></div>
        </div>
    </div>
</div>

<div class="dashboard-content">
    
    <!-- DASHBOARD CARDS -->
    <div class="dashboard-cards">
        <!-- Pending Requests -->
        <div class="appt-card appt-card-pending delay-1 animate-fade-up">
            <div class="appt-icon"><?php echo icon_clock(48, 48); ?></div>
            <div class="appt-value"><?php echo $pending_appts; ?></div>
            <div class="appt-title">Pending Requests</div>
            <?php if($pending_appts > 0): ?>
                <a href="appointments.php" class="appt-action">Review & Approve</a>
            <?php else: ?>
                <span class="appt-action" style="opacity: 0.5; background: transparent; border-color: transparent;">Queue is empty</span>
            <?php endif; ?>
        </div>
        
        <!-- Upcoming Appointments -->
        <div class="appt-card appt-card-approved delay-2 animate-fade-up">
            <div class="appt-icon"><?php echo icon_calendar(48, 48); ?></div>
            <div class="appt-value"><?php echo $approved_appts; ?></div>
            <div class="appt-title">Upcoming Appointments</div>
            <?php if($approved_appts > 0): ?>
                <a href="results.php" class="appt-action">Process Results</a>
            <?php else: ?>
                <span class="appt-action" style="opacity: 0.5; background: transparent; border-color: transparent;">No upcoming tasks</span>
            <?php endif; ?>
        </div>
        
        <!-- Completed -->
        <div class="appt-card appt-card-completed delay-3 animate-fade-up">
            <div class="appt-icon"><?php echo icon_clipboard_ok(48, 48); ?></div>
            <div class="appt-value"><?php echo $completed_appts; ?></div>
            <div class="appt-title">Records Processed</div>
            <div class="appt-action">Operation Successful</div>
        </div>
    </div>

    <!-- COMPLEX GRID -->
    <div class="dashboard-grid-complex animate-fade-up delay-2">
        
        <!-- Recent Operations -->
        <div>
            <h3 class="section-header">Recent Patient Operations</h3>
            <div class="panel">
                <?php if(count($recent_updates) > 0): ?>
                    <ul class="recent-updates-list">
                        <?php foreach($recent_updates as $ru): ?>
                            <li class="update-item">
                                <div>
                                    <div class="update-patient"><?php echo htmlspecialchars($ru['patient_name']); ?></div>
                                    <div class="update-type"><?php echo $ru['type']; ?> on <?php echo date('M d', strtotime($ru['appointment_date'])); ?></div>
                                </div>
                                <div>
                                    <?php if($ru['type'] === 'Test'): ?>
                                        <?php 
                                            $bg = ($ru['test_result'] === 'Negative') ? '#dcfce7' : '#fee2e2';
                                            $col = ($ru['test_result'] === 'Negative') ? '#15803d' : '#b91c1c';
                                        ?>
                                        <span class="update-result" style="background: <?php echo $bg; ?>; color: <?php echo $col; ?>;">
                                            <?php echo $ru['test_result']; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="update-result" style="background: #e0f2fe; color: #0369a1;">
                                            <?php echo $ru['vaccination_status']; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                        <div style="margin-bottom: 1rem; opacity: 0.35; display: flex; justify-content: center;"><?php echo icon_folder(56, 56); ?></div>
                        <p>No recent completed operations found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Facility Resources (Dummy Data for UI expansion) -->
        <div>
            <h3 class="section-header">Facility Resources</h3>
            <div class="panel">
                <div class="resource-grid">
                    <div class="resource-item">
                        <div class="resource-header">
                            <span>Covid-19 Test Kits</span>
                            <span style="color: var(--primary);">85%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: 85%;"></div>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">Stock is adequate for 2 weeks.</div>
                    </div>
                    
                    <div class="resource-item" style="border-left-color: #10b981;">
                        <div class="resource-header">
                            <span>Vaccine Inventory (Pfizer)</span>
                            <span style="color: #10b981;">62%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: 62%; background: #10b981;"></div>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">Resupply requested from admin.</div>
                    </div>
                    
                    <div class="resource-item" style="border-left-color: #f59e0b;">
                        <div class="resource-header">
                            <span>Staff Availability (Today)</span>
                            <span style="color: #f59e0b;">92%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: 92%; background: #f59e0b;"></div>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">All departments fully operational.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PATIENT REVIEWS -->
    <h3 class="section-header animate-fade-up delay-3">Recent Patient Feedback</h3>
    <p style="color: var(--text-secondary); margin-bottom: 3rem;">Authentic reviews from patients who visited <?php echo htmlspecialchars($hospital['hospital_name']); ?>.</p>
    
    <div class="reviews-grid animate-fade-up delay-4">
        <?php if (count($reviewsRows) > 0): ?>
            <?php $ri = 0; foreach ($reviewsRows as $rr): $ri++; ?>
                <div class="review-card">
                    <?php echo icon_star_rating((int) $rr['rating']); ?>
                    <p class="review-text"><?php echo htmlspecialchars($rr['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="review-author">
                        <div class="author-avatar"><?php echo strtoupper(substr($rr['patient_name'], 0, 1)); ?></div>
                        <div>
                            <h5 style="margin: 0; font-size: 1rem; color: var(--text-primary);"><?php echo htmlspecialchars($rr['patient_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                            <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Verified patient</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="review-card" style="grid-column: 1 / -1;">
                <p class="review-text" style="font-style: normal;">No reviews yet for your facility. When patients complete care and submit feedback, it will show here and on the public home page.</p>
            </div>
        <?php endif; ?>
    </div>

</div>
</div>

<?php include '../includes/footer.php'; ?>
