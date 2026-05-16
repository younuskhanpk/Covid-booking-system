<?php
// patient/index.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/database.php';
require_once '../includes/svg_icons.php';
require_once '../includes/hospital_queries.php';

$user_id = $_SESSION['user_id'];

$reviewsCommunity = [];
try {
    $reviewsCommunity = $conn->query("
        SELECT r.rating, r.comment, r.created_at, u.name AS patient_name, h.hospital_name
        FROM reviews r
        INNER JOIN users u ON r.patient_id = u.id
        INNER JOIN users h ON r.hospital_id = h.id AND h.role_id = 2
        WHERE r.status = 'Approved'
        ORDER BY r.created_at DESC
        LIMIT 6
    ")->fetchAll();
} catch (Throwable $e) {
    $reviewsCommunity = [];
}

// Fetch User Profile
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :uid");
$stmt->bindParam(':uid', $user_id);
$stmt->execute();
$user = $stmt->fetch();

// Fetch Appointments and Results
$hN = hospital_name_expr($conn);
$stmt = $conn->prepare("
    SELECT a.id, a.type, a.appointment_date, a.status,
           {$hN} AS hospital_name, hu.location,
           v.vaccine_name,
           r.test_result, r.vaccination_status, r.notes
    FROM appointments a
    " . hospital_join_sql($conn, 'a') . "
    LEFT JOIN vaccines v ON a.vaccine_id = v.id
    LEFT JOIN results r ON a.id = r.appointment_id
    WHERE a.patient_id = :uid
    ORDER BY a.appointment_date DESC
");
$stmt->bindParam(':uid', $user_id);
$stmt->execute();
$history = $stmt->fetchAll();

// Calculate some dummy stats based on history for the new UI
$total_tests = 0;
$total_vax = 0;
foreach($history as $h) {
    if($h['type'] === 'Test') $total_tests++;
    if($h['type'] === 'Vaccination') $total_vax++;
}

include '../includes/header.php';
?>

<style>
/* ==========================================================================
   PREMIUM PATIENT DASHBOARD STYLES (Massive UI Expansion)
   ========================================================================== */
.patient-dashboard {
    padding: 0 0 6rem;
    font-family: var(--font-sans);
    position: relative;
    z-index: 0;
    isolation: isolate;
}

/* --- Hero Section with Background Image --- */
.patient-hero {
    /* Using a high-quality medical/health background from Unsplash */
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.9), rgba(79, 70, 229, 0.85)), url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover no-repeat;
    background-attachment: fixed;
    border-radius: 0 0 var(--radius-xl) var(--radius-xl);
    padding: 6rem 3rem 4rem;
    position: relative;
    overflow: hidden;
    margin-bottom: 4rem;
    box-shadow: 0 20px 40px -10px rgba(14, 165, 233, 0.4);
    color: white;
}

.patient-hero::before {
    content: '';
    position: absolute;
    top: -50%; right: -20%;
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
    border-radius: 50%;
    animation: pulseGlow 10s ease-in-out infinite alternate;
}

.patient-hero-content {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.patient-hero h2 {
    font-size: 3.5rem;
    margin-bottom: 0.5rem;
    color: white;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    font-weight: 900;
}

.patient-hero p {
    font-size: 1.2rem;
    opacity: 0.95;
    max-width: 600px;
    line-height: 1.6;
}

.patient-profile-badge {
    display: inline-flex;
    align-items: center;
    gap: 1rem;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    padding: 0.5rem 1.5rem 0.5rem 0.5rem;
    border-radius: 50px;
    border: 1px solid rgba(255,255,255,0.3);
    margin-bottom: 1.5rem;
}

.patient-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: white;
    color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 1.2rem;
}

.patient-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.patient-actions a {
    backdrop-filter: blur(10px);
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

.patient-actions .btn-primary {
    background: white;
    color: var(--primary) !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.patient-actions .btn-primary:hover {
    box-shadow: 0 15px 35px rgba(0,0,0,0.3);
    transform: translateY(-5px);
}

.patient-actions .btn-outline {
    border-color: rgba(255,255,255,0.6);
    color: white !important;
}

.patient-actions .btn-outline:hover {
    background: rgba(255,255,255,0.2);
    border-color: white;
}

/* --- Content Wrapper --- */
.dashboard-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* --- Quick Stats Section --- */
.quick-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 1.5rem;
    position: relative;
    z-index: 10;
    margin-bottom: 4rem;
}

.stat-box {
    background: white;
    border-radius: var(--radius-xl);
    padding: 2rem;
    box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: var(--transition);
    border: 1px solid var(--border);
}

.stat-box:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.12);
    border-color: var(--accent);
}

.stat-icon .svg-icon { width: 2.25rem; height: 2.25rem; }

.stat-icon.blue { background: #e0f2fe; color: #0284c7; }
.stat-icon.green { background: #dcfce7; color: #16a34a; }
.stat-icon.purple { background: #f3e8ff; color: #9333ea; }

.stat-info h4 { margin: 0; font-size: 2.5rem; color: var(--text-primary); line-height: 1; }
.stat-info p { margin: 0; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; margin-top: 0.5rem; }

/* --- Section Titles --- */
.section-title {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 2.2rem;
    margin-bottom: 2rem;
    color: var(--text-primary);
    font-weight: 800;
}

.section-title::before {
    content: '';
    display: block;
    width: 8px; height: 40px;
    background: linear-gradient(180deg, var(--accent), var(--primary));
    border-radius: 10px;
}

/* --- Medical History Grid --- */
.history-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2.5rem;
    margin-bottom: 5rem;
}

.history-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 2.5rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    border: 1px solid var(--border);
    position: relative;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.history-card::before {
    content: '';
    position: absolute; top: 0; left: 0; width: 100%; height: 5px;
    background: linear-gradient(90deg, var(--accent), var(--primary));
    opacity: 0; transition: opacity 0.3s;
}

.history-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(14, 165, 233, 0.15);
}

.history-card:hover::before { opacity: 1; }

.history-badge {
    position: absolute;
    top: 1.5rem; right: 1.5rem;
    padding: 0.5rem 1.2rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.status-pending { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
.status-approved { background: var(--primary-light); color: var(--primary-dark); border: 1px solid rgba(79,70,229,0.2); }
.status-completed { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }

.history-type {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--accent);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1rem;
    background: rgba(14, 165, 233, 0.1);
    padding: 0.4rem 1rem;
    border-radius: 8px;
}

.history-card h4 {
    font-size: 1.6rem;
    margin-bottom: 0.8rem;
    color: var(--text-primary);
    line-height: 1.3;
}

.history-details {
    color: var(--text-secondary);
    font-size: 1rem;
    margin-bottom: 1.5rem;
    flex-grow: 1;
    background: var(--bg-main);
    padding: 1.5rem;
    border-radius: var(--radius-md);
}

.history-details div {
    margin-bottom: 0.8rem;
    display: flex; align-items: flex-start; gap: 0.8rem;
}
.history-details div:last-child { margin-bottom: 0; }

.history-details strong {
    color: var(--text-primary);
    min-width: 80px;
    display: inline-block;
}

.result-box {
    background: #f8fafc;
    border-radius: var(--radius-md);
    padding: 1.8rem;
    border: 1px solid var(--border);
    border-left: 5px solid var(--primary);
    margin-top: 1rem;
}

.result-box h5 {
    font-size: 1.1rem;
    margin-bottom: 1rem;
    color: var(--text-primary);
    font-weight: 800;
}

.result-badge {
    display: inline-block;
    padding: 0.4rem 1rem;
    border-radius: 8px;
    font-weight: 800;
    font-size: 0.95rem;
    letter-spacing: 0.5px;
}

.result-negative, .result-completed { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.result-positive { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.result-pending { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

.empty-state {
    text-align: center;
    padding: 6rem 2rem;
    background: white;
    border-radius: var(--radius-xl);
    border: 2px dashed var(--border);
    margin-bottom: 5rem;
}

.empty-state-icon {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    opacity: 0.5;
    animation: floating 3s ease-in-out infinite;
}

/* --- Health Guidelines Section --- */
.guidelines-section {
    background: linear-gradient(135deg, #0f172a, #1e293b);
    border-radius: var(--radius-xl);
    padding: 4rem;
    color: white;
    margin-bottom: 5rem;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-xl);
}
.guidelines-section::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
    background: url('data:image/svg+xml;utf8,<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="1" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
}
.guidelines-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; position: relative; z-index: 1; margin-top: 3rem;
}
.guideline-card {
    background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);
    padding: 2rem; border-radius: var(--radius-lg); transition: all 0.3s;
}
.guideline-card:hover { background: rgba(255,255,255,0.1); transform: translateY(-5px); }
.guideline-card h4 { color: #38bdf8; font-size: 1.3rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; }
.guideline-card h4 .svg-icon { flex-shrink: 0; }
.guideline-card p { opacity: 0.8; font-size: 0.95rem; line-height: 1.6; }

/* --- Patient Testimonials / Reviews Section --- */
.reviews-section {
    margin-bottom: 5rem;
}
.reviews-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;
}
.review-card {
    background: white; padding: 2.5rem; border-radius: var(--radius-xl); box-shadow: var(--shadow-md); border: 1px solid var(--border);
    position: relative;
}
.review-card::after {
    content: '\"'; position: absolute; top: 20px; right: 30px; font-size: 5rem; color: var(--primary-light); font-family: serif; line-height: 1; opacity: 0.5;
}
.review-stars { color: #fbbf24; font-size: 1.2rem; margin-bottom: 1rem; }
.review-text { font-size: 1.05rem; font-style: italic; color: var(--text-secondary); margin-bottom: 1.5rem; line-height: 1.7; relative: z-index: 1;}
.review-author { display: flex; align-items: center; gap: 1rem; }
.author-avatar { width: 50px; height: 50px; border-radius: 50%; background: var(--bg-main); }
.author-info h5 { margin: 0; font-size: 1rem; color: var(--text-primary); }
.author-info p { margin: 0; font-size: 0.85rem; color: var(--text-muted); }

@media (max-width: 768px) {
    .patient-hero { padding: 4rem 2rem 8rem; text-align: center; border-radius: 0 0 2rem 2rem; }
    .patient-hero-content { justify-content: center; }
    .patient-actions { flex-direction: column; width: 100%; }
    .patient-actions a { width: 100%; justify-content: center; }
    .quick-stats { margin-top: 1rem; padding: 0 1rem; }
    .guidelines-section { padding: 3rem 1.5rem; }
}
</style>

<div class="patient-dashboard portal-page">

<!-- HERO SECTION -->
<div class="patient-hero animate-fade-up">
    <div class="patient-hero-content">
        <div>
            <div class="patient-profile-badge">
                <div class="patient-avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                <span style="font-weight: 700; letter-spacing: 1px; font-size: 0.9rem; text-transform: uppercase;">Verified Patient Profile</span>
            </div>
            <h2>Welcome back, <?php echo htmlspecialchars($user['name'] ?? 'Patient'); ?>!</h2>
            <p>Your comprehensive health dashboard is ready. Book new appointments, track your medical history, view real-time test results, and stay informed with the latest health guidelines seamlessly.</p>
        </div>
        <div class="patient-actions">
            <a href="search.php" class="btn-primary">Book New Appointment</a>
            <a href="profile.php" class="btn-outline">Update Profile</a>
        </div>
    </div>
</div>

<div class="dashboard-content">
    
    <!-- QUICK STATS -->
    <div class="quick-stats animate-fade-up delay-1">
        <div class="stat-box">
            <div class="stat-icon blue"><?php echo icon_test_tube(40, 40); ?></div>
            <div class="stat-info">
                <h4><?php echo $total_tests; ?></h4>
                <p>Tests Taken</p>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon green"><?php echo icon_syringe(40, 40); ?></div>
            <div class="stat-info">
                <h4><?php echo $total_vax; ?></h4>
                <p>Vaccines Received</p>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon purple"><?php echo icon_building_small(40, 40); ?></div>
            <div class="stat-info">
                <h4><?php echo count($history); ?></h4>
                <p>Total Visits</p>
            </div>
        </div>
    </div>

    <!-- MEDICAL HISTORY -->
    <h3 class="section-title animate-fade-up delay-2">My Medical History</h3>
    
    <?php if(count($history) > 0): ?>
        <div class="history-grid">
            <?php $delay = 2; foreach($history as $h): $delay++; ?>
                <div class="history-card animate-fade-up delay-<?php echo min($delay, 5); ?>">
                    <div class="history-badge status-<?php echo strtolower($h['status']); ?>">
                        <?php echo $h['status']; ?>
                    </div>
                    
                    <div class="history-type">
                        <?php if($h['type'] === 'Test'): ?>
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        <?php else: ?>
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <?php endif; ?>
                        <?php echo $h['type']; ?>
                    </div>
                    
                    <h4>
                        <?php if($h['type'] === 'Vaccination'): ?>
                            <?php echo htmlspecialchars($h['vaccine_name']); ?>
                        <?php else: ?>
                            COVID-19 Diagnostic Test
                        <?php endif; ?>
                    </h4>
                    
                    <div class="history-details">
                        <div>
                            <svg width="20" height="20" fill="none" stroke="var(--accent)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <strong>Date:</strong> <span><?php echo date('F d, Y', strtotime($h['appointment_date'])); ?></span>
                        </div>
                        <div>
                            <svg width="20" height="20" fill="none" stroke="var(--accent)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <strong>Facility:</strong> <span><?php echo htmlspecialchars($h['hospital_name']); ?> <br><small style="color: var(--text-muted);"><?php echo htmlspecialchars($h['location']); ?></small></span>
                        </div>
                    </div>
                    
                    <?php if($h['status'] === 'Completed'): ?>
                        <div class="result-box">
                            <h5>Official Result / Status</h5>
                            
                            <?php if($h['type'] === 'Test'): ?>
                                <?php $res_class = ($h['test_result'] === 'Negative') ? 'result-negative' : (($h['test_result'] === 'Positive') ? 'result-positive' : 'result-pending'); ?>
                                <div style="margin-bottom: 1rem;"><span class="result-badge <?php echo $res_class; ?>"><?php echo $h['test_result'] ?? 'Pending'; ?></span></div>
                            <?php elseif($h['type'] === 'Vaccination'): ?>
                                <?php $res_class = ($h['vaccination_status'] === 'Completed') ? 'result-completed' : 'result-pending'; ?>
                                <div style="margin-bottom: 1rem;"><span class="result-badge <?php echo $res_class; ?>"><?php echo $h['vaccination_status'] ?? 'Pending'; ?></span></div>
                            <?php endif; ?>
                            
                            <?php if(!empty($h['notes'])): ?>
                                <p style="font-size: 0.9rem; font-style: italic; color: var(--text-secondary); background: white; padding: 1rem; border-radius: 8px; border: 1px solid var(--border);">"<?php echo htmlspecialchars($h['notes']); ?>"</p>
                            <?php endif; ?>
                            
                            <a href="appointment_print.php?id=<?php echo (int) $h['id']; ?>" target="_blank" rel="noopener" class="btn-outline" style="margin-top: 1.5rem; width: 100%; padding: 0.8rem; font-size: 0.95rem; border-radius: 8px; font-weight: 700; border-width: 2px; text-align:center;">
                                Open printable certificate
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state animate-fade-up delay-2">
            <div class="empty-state-icon"><?php echo icon_building_small(72, 72); ?></div>
            <h4 style="font-size: 2rem; color: var(--text-primary); margin-bottom: 1rem; font-weight: 900;">No Medical History Found</h4>
            <p style="color: var(--text-muted); font-size: 1.2rem; margin-bottom: 2.5rem; max-width: 500px; margin-left: auto; margin-right: auto; line-height: 1.6;">You haven't booked any tests or vaccinations yet. Our network of premium hospitals is ready to serve you.</p>
            <a href="search.php" class="btn-primary" style="padding: 1.2rem 3rem; font-size: 1.1rem; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);">Find a Hospital Now</a>
        </div>
    <?php endif; ?>

    <!-- HEALTH GUIDELINES SECTION -->
    <div class="guidelines-section animate-fade-up delay-3">
        <div style="position: relative; z-index: 1;">
            <h3 style="font-size: 2.5rem; margin-bottom: 0.5rem; font-weight: 900;">Health Guidelines & Prevention</h3>
            <p style="font-size: 1.1rem; opacity: 0.8; max-width: 600px;">Stay informed with the latest protocols to keep yourself and your community safe from viruses and infections.</p>
        </div>
        
        <div class="guidelines-grid">
            <div class="guideline-card">
                <h4><?php echo icon_soap_hands(26, 26); ?> Wash hands frequently</h4>
                <p>Regularly and thoroughly clean your hands with an alcohol-based hand rub or wash them with soap and water for at least 20 seconds.</p>
            </div>
            <div class="guideline-card">
                <h4><?php echo icon_mask(26, 26); ?> Wear a mask</h4>
                <p>Make wearing a mask a normal part of being around other people. The appropriate use, storage and cleaning or disposal of masks are essential.</p>
            </div>
            <div class="guideline-card">
                <h4><?php echo icon_distance(26, 26); ?> Keep physical distance</h4>
                <p>Maintain at least a 1-metre distance between yourself and others to reduce your risk of infection when they cough, sneeze or speak.</p>
            </div>
            <div class="guideline-card">
                <h4><?php echo icon_syringe(26, 26); ?> Get vaccinated</h4>
                <p>Authorized vaccines can help protect you from severe illness. Once fully vaccinated, you can resume many activities safely.</p>
            </div>
        </div>
    </div>

    <!-- REVIEWS FROM DATABASE -->
    <h3 class="section-title animate-fade-up delay-3">Community reviews</h3>
    <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 2rem; max-width: 640px;">Recent feedback from patients after completed visits. <a href="review.php">Share your own review</a> after your hospital marks your appointment as completed.</p>
    
    <div class="reviews-section animate-fade-up delay-4">
        <div class="reviews-grid">
            <?php if (count($reviewsCommunity) > 0): ?>
                <?php foreach ($reviewsCommunity as $rc): ?>
                    <div class="review-card">
                        <?php echo icon_star_rating((int) $rc['rating']); ?>
                        <p class="review-text"><?php echo htmlspecialchars($rc['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="review-author">
                            <div class="author-avatar" style="display:flex;align-items:center;justify-content:center;font-weight:800;background:var(--primary-light);color:var(--primary-dark);">
                                <?php echo strtoupper(substr($rc['patient_name'], 0, 1)); ?>
                            </div>
                            <div class="author-info">
                                <h5><?php echo htmlspecialchars($rc['patient_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                <p><?php echo htmlspecialchars($rc['hospital_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                        <p style="margin-top:0.75rem;font-size:0.8rem;color:var(--text-muted);"><?php echo date('M j, Y', strtotime($rc['created_at'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="review-card" style="grid-column: 1 / -1;">
                    <p class="review-text" style="font-style: normal;">No reviews in the system yet. Complete a visit and submit feedback from the Reviews page.</p>
                    <p style="margin-top: 1rem;"><a class="btn-primary" href="review.php">Go to reviews</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
</div>

<?php include '../includes/footer.php'; ?>
