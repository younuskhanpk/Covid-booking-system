<?php
// index.php — public landing (logged-in users may still view this page)
session_start();

require_once 'config/database.php';
require_once 'includes/svg_icons.php';
require_once 'includes/hospital_queries.php';

$reviewsHome = [];
try {
    $hN = hospital_name_expr($conn);
    $reviewsHome = $conn->query("
        SELECT r.rating, r.comment, r.created_at, u.name AS patient_name, {$hN} AS hospital_name
        FROM reviews r
        INNER JOIN users u ON r.patient_id = u.id
        " . hospital_join_sql($conn, 'r') . "
        WHERE r.status = 'Approved'
        ORDER BY r.created_at DESC
        LIMIT 9
    ")->fetchAll();
} catch (Throwable $e) {
    $reviewsHome = [];
}

include 'includes/header.php';
?>

<style>
/* ==========================================================================
   MASSIVE PREMIUM PUBLIC LANDING PAGE STYLES
   ========================================================================== */

   

.reveal-section {
    opacity: 0;
    transform: translateY(36px);
    animation: revealOnScroll ease-out forwards;
    animation-timeline: view();
    animation-range: entry 0% cover 35%;
}

@supports not (animation-timeline: view()) {
    .reveal-section { opacity: 1; transform: none; animation: none; }
}

@keyframes revealOnScroll {
    to { opacity: 1; transform: translateY(0); }
}

.impact-icon .svg-icon { width: 2.75rem; height: 2.75rem; color: var(--accent); }

.hero-pill-icon { display: inline-flex; align-items: center; gap: 0.5rem; vertical-align: middle; }
.hero-pill-icon .svg-icon { width: 1.1rem; height: 1.1rem; color: #fff; }

.landing-reviews-bar {
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: #fff;
    padding: 3rem 2rem;
    margin-top: 4rem;
    border-top: 1px solid rgba(255,255,255,0.08);
    border-bottom: 1px solid rgba(255,255,255,0.08);
}
.landing-reviews-bar-inner {
    max-width: 1100px;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
}
.landing-reviews-bar h3 { color: #fff; margin: 0; font-size: 1.5rem; }
.landing-reviews-bar p { margin: 0.35rem 0 0; opacity: 0.85; max-width: 520px; font-size: 1rem; }
.landing-reviews-bar .btn-primary { box-shadow: 0 12px 28px rgba(0,0,0,0.25); }
.landing-reviews-bar .btn-outline { border-color: rgba(255,255,255,0.5); color: #fff !important; }
.landing-reviews-bar .btn-outline:hover { background: rgba(255,255,255,0.12); }

.test-card .star-rating { display: flex; gap: 2px; margin-bottom: 1rem; color: #fbbf24; }

/* --- Massive Hero Section --- */
.landing-hero {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(14, 165, 233, 0.9)), url('https://images.unsplash.com/photo-1584483766114-2cea6facdf57?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover no-repeat;
    background-attachment: fixed;
    min-height: 95vh;
    display: flex;
    align-items: center;
    position: relative;
    padding: 10rem 0 8rem;
    color: white;
}

.hero-particle {
    position: absolute;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    animation: floatParticle 10s infinite linear;
}

@keyframes floatParticle {
    0% { transform: translateY(0) rotate(0deg); opacity: 0; }
    50% { opacity: 1; }
    100% { transform: translateY(-1000px) rotate(360deg); opacity: 0; }
}

.hero-content {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
    position: relative;
    z-index: 10;
}

.hero-content h1 {
    font-size: 5rem;
    font-weight: 900;
    line-height: 1.1;
    margin-bottom: 1.5rem;
    color: white;
    text-shadow: 0 10px 30px rgba(0,0,0,0.5);
    letter-spacing: -2px;
}

.hero-content p {
    font-size: 1.4rem;
    opacity: 0.95;
    margin-bottom: 3.5rem;
    line-height: 1.6;
    font-weight: 400;
    text-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.hero-buttons {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
}

.btn-massive {
    padding: 1.2rem 3rem;
    font-size: 1.25rem;
    font-weight: 800;
    border-radius: 50px;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-glass {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.5);
    color: white !important;
}

.btn-glass:hover {
    background: white;
    color: var(--primary-dark) !important;
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

/* --- Social Impact Section (Muashray main tabdeeli) --- */
.impact-section {
    padding: 8rem 0;
    background: white;
    position: relative;
}

.section-header-center {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 5rem;
}

.section-header-center h2 {
    font-size: 3.5rem;
    font-weight: 900;
    margin-bottom: 1.5rem;
    color: var(--text-primary);
    line-height: 1.2;
}

.section-header-center p {
    font-size: 1.25rem;
    color: var(--text-secondary);
    line-height: 1.7;
}

.impact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 3rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.impact-card {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: var(--radius-xl);
    padding: 3rem;
    border: 1px solid var(--border);
    transition: all 0.5s ease;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.impact-card:hover {
    transform: translateY(-15px);
    box-shadow: 0 30px 60px rgba(14, 165, 233, 0.15);
    background: white;
    border-color: var(--accent);
}

.impact-icon {
    width: 90px; height: 90px;
    background: white;
    color: var(--accent);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 3rem;
    margin: 0 auto 2rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
}

.impact-card h3 { font-size: 1.8rem; margin-bottom: 1rem; color: var(--text-primary); font-weight: 800;}
.impact-card p { color: var(--text-secondary); line-height: 1.7; font-size: 1.1rem; }

/* --- How it Works: For Patients --- */
.journey-section {
    padding: 8rem 0;
    background: var(--bg-main);
}

.journey-row {
    display: flex;
    align-items: center;
    gap: 5rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.journey-image {
    flex: 1;
    position: relative;
}

.journey-image img {
    border-radius: var(--radius-xl);
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    width: 100%;
    height: 600px;
    object-fit: cover;
}

.journey-content {
    flex: 1.2;
}

.step-card {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 2rem;
    background: white;
    padding: 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    transition: transform 0.3s;
}

.step-card:hover {
    transform: translateX(10px);
    box-shadow: var(--shadow-md);
    border-left: 5px solid var(--primary);
}

.step-number {
    width: 50px; height: 50px;
    background: var(--primary-light);
    color: var(--primary-dark);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
    font-weight: 900;
    flex-shrink: 0;
}

.step-text h4 { font-size: 1.4rem; margin-bottom: 0.5rem; color: var(--text-primary); }
.step-text p { font-size: 1.05rem; color: var(--text-secondary); line-height: 1.6; margin: 0; }

/* --- How it Works: For Hospitals --- */
.hospital-journey {
    padding: 8rem 0;
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: white;
}

.h-step-card {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
    color: white;
}
.h-step-card:hover {
    background: rgba(255,255,255,0.1);
    border-left: 5px solid #34d399;
}
.h-step-card .step-number { background: rgba(52, 211, 153, 0.2); color: #34d399; }
.h-step-card .step-text h4 { color: white; }
.h-step-card .step-text p { color: rgba(255,255,255,0.7); }

/* --- Community Reviews --- */
.testimonials {
    padding: 8rem 0;
    background: white;
}

.test-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2.5rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.test-card {
    background: var(--bg-main);
    padding: 3rem;
    border-radius: var(--radius-xl);
    box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    position: relative;
    border: 1px solid var(--border);
}

.test-card::after {
    content: '\"';
    position: absolute;
    top: 20px; right: 30px;
    font-size: 6rem;
    color: var(--primary-light);
    font-family: serif;
    opacity: 0.3;
    line-height: 1;
}

.test-stars { color: #fbbf24; font-size: 1.4rem; margin-bottom: 1.5rem; }
.test-text { font-size: 1.15rem; font-style: italic; color: var(--text-secondary); margin-bottom: 2rem; line-height: 1.7; position: relative; z-index: 1;}
.test-author { display: flex; align-items: center; gap: 1.5rem; }
.t-avatar { width: 60px; height: 60px; border-radius: 50%; background: white; object-fit: cover; }
.t-info h4 { margin: 0; font-size: 1.1rem; color: var(--text-primary); }
.t-info p { margin: 0; font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-top: 0.2rem;}

/* --- Call to Action --- */
.cta-section {
    padding: 8rem 0;
    background: linear-gradient(135deg, var(--accent), var(--primary));
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
    background: url('data:image/svg+xml;utf8,<svg width="40" height="40" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="1.5" fill="rgba(255,255,255,0.15)"/></svg>') repeat;
}

.cta-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative; z-index: 1;
}

.cta-content h2 { font-size: 4rem; color: white; margin-bottom: 1.5rem; font-weight: 900;}
.cta-content p { font-size: 1.35rem; opacity: 0.9; margin-bottom: 3.5rem; line-height: 1.6;}

@media (max-width: 1024px) {
    .journey-row { flex-direction: column; }
    .journey-image img { height: 400px; }
}

@media (max-width: 768px) {
    .hero-content h1 { font-size: 3.5rem; }
    .hero-buttons { flex-direction: column; }
    .impact-grid { grid-template-columns: 1fr; }
    .cta-content h2 { font-size: 3rem; }
}
</style>

<div class="main-landing">

    <!-- MASSIVE HERO SECTION -->
    <section class="landing-hero reveal-section">
        <div class="hero-particle" style="width: 50px; height: 50px; left: 10%; bottom: 20%; animation-duration: 15s;"></div>
        <div class="hero-particle" style="width: 30px; height: 30px; left: 30%; bottom: 40%; animation-duration: 12s;"></div>
        <div class="hero-particle" style="width: 80px; height: 80px; right: 20%; bottom: 10%; animation-duration: 20s;"></div>

        <div class="hero-content animate-fade-up">
            <div style="display: inline-block; padding: 0.5rem 1.5rem; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(5px); border: 1px solid rgba(255, 255, 255, 0.4); color: white; border-radius: 50px; font-weight: 800; letter-spacing: 2px; margin-bottom: 2rem; font-size: 0.9rem;">
                <span class="hero-pill-icon"><?php echo icon_check_circle(18, 18); ?></span> EMPOWERING HEALTHCARE GLOBALLY
            </div>
            <h1>A Healthier Future, Together.</h1>
            <p>Join the movement that is transforming our society. VaxiCare is a community-driven platform designed to make COVID-19 testing and vaccinations accessible, transparent, and seamless for everyone.</p>
            <div class="hero-buttons">
                <a href="auth/register.php" class="btn-massive btn-glass" style="background: white; color: var(--primary) !important;">Join the Community</a>
                <a href="#how-it-works" class="btn-massive btn-glass">Learn How It Works</a>
            </div>
        </div>
    </section>

    <!-- IMPACT ON SOCIETY SECTION -->
    <section class="impact-section reveal-section">
        <div class="section-header-center animate-fade-up">
            <h2>Transforming Our Society</h2>
            <p>Our platform is not just a tool; it's a social initiative. Here is how we are bringing a positive change to our communities and making healthcare accessible to all.</p>
        </div>
        
        <div class="impact-grid">
            <div class="impact-card animate-fade-up delay-1">
                <div class="impact-icon"><?php echo icon_hospital_building(); ?></div>
                <h3>Reducing Hospital Rush</h3>
                <p>By shifting the booking process entirely online, we eliminate long physical queues at medical centers. Patients only visit when their time slot is confirmed, saving time and reducing the risk of virus transmission.</p>
            </div>
            <div class="impact-card animate-fade-up delay-2">
                <div class="impact-icon"><?php echo icon_mobile_phone(); ?></div>
                <h3>Digital Transparency</h3>
                <p>No more lost paper reports or fake certificates. Every COVID test result and vaccination record is instantly digitized and verified, creating a safer and more trustworthy environment for travel and work.</p>
            </div>
            <div class="impact-card animate-fade-up delay-3">
                <div class="impact-icon"><?php echo icon_handshake(); ?></div>
                <h3>Uniting Communities</h3>
                <p>We bridge the gap between healthcare providers and citizens. Hospitals can reach more people efficiently, while patients get instant access to critical health resources without any panic or confusion.</p>
            </div>
        </div>
    </section>

    <!-- FOR PATIENTS: HOW TO WORK -->
    <section id="how-it-works" class="journey-section reveal-section">
        <div class="section-header-center animate-fade-up">
            <h2 style="font-size: 3rem;">Your Journey as a Patient</h2>
            <p>Taking control of your health has never been this easy. Follow these simple steps to get tested or vaccinated safely.</p>
        </div>

        <div class="journey-row">
            <div class="journey-content">
                <div class="step-card animate-fade-up delay-1">
                    <div class="step-number">1</div>
                    <div class="step-text">
                        <h4>Create Your Secure Profile</h4>
                        <p>Sign up easily by providing your basic details, phone number, and address. Your data remains 100% private and secure within our encrypted system.</p>
                    </div>
                </div>
                
                <div class="step-card animate-fade-up delay-2">
                    <div class="step-number">2</div>
                    <div class="step-text">
                        <h4>Find Verified Hospitals</h4>
                        <p>Search for authorized medical centers near your location. View their available vaccines, testing facilities, and read reviews from other community members.</p>
                    </div>
                </div>
                
                <div class="step-card animate-fade-up delay-3">
                    <div class="step-number">3</div>
                    <div class="step-text">
                        <h4>Book & Visit Safely</h4>
                        <p>Select a date and time that suits you. The hospital will be ready for you when you arrive, ensuring zero wait time and maximum safety protocols.</p>
                    </div>
                </div>
                
                <div class="step-card animate-fade-up delay-4">
                    <div class="step-number">4</div>
                    <div class="step-text">
                        <h4>Access Digital Results</h4>
                        <p>Once your procedure is done, log back into your dashboard to instantly view your test reports or download your official vaccination certificate.</p>
                    </div>
                </div>
            </div>
            
            <div class="journey-image animate-fade-up delay-2">
                <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Patient Journey">
            </div>
        </div>
    </section>

    <!-- FOR HOSPITALS: HOW TO WORK -->
    <section class="hospital-journey reveal-section">
        <div class="section-header-center animate-fade-up">
            <h2 style="color: white; font-size: 3rem;">Empowering Healthcare Providers</h2>
            <p style="color: rgba(255,255,255,0.8);">If you represent a hospital or clinic, join our network to serve the community efficiently and digitally.</p>
        </div>

        <div class="journey-row" style="flex-direction: row-reverse;">
            <div class="journey-content">
                <div class="step-card h-step-card animate-fade-up delay-1">
                    <div class="step-number">1</div>
                    <div class="step-text">
                        <h4>Register Your Facility</h4>
                        <p>Provide your license number and location details. Once verified, your hospital becomes visible to thousands of patients searching for care.</p>
                    </div>
                </div>
                
                <div class="step-card h-step-card animate-fade-up delay-2">
                    <div class="step-number">2</div>
                    <div class="step-text">
                        <h4>Manage Incoming Requests</h4>
                        <p>Review and accept digital booking requests. Plan your daily operations and staff allocation based on accurate, real-time appointment data.</p>
                    </div>
                </div>
                
                <div class="step-card h-step-card animate-fade-up delay-3">
                    <div class="step-number">3</div>
                    <div class="step-text">
                        <h4>Update Patient Records</h4>
                        <p>After testing or vaccinating a patient, update their status directly on your portal. The patient gets instantly notified, removing the need for physical paperwork.</p>
                    </div>
                </div>
            </div>
            
            <div class="journey-image animate-fade-up delay-2">
                <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Hospital Management">
            </div>
        </div>
    </section>

    <!-- REVIEWS FROM REAL PATIENTS -->
    <section class="testimonials reveal-section">
        <div class="section-header-center animate-fade-up">
            <h2>Community reviews</h2>
            <p>These are real comments from patients after completed visits at partner hospitals.</p>
        </div>

        <div class="test-grid">
            <?php if (count($reviewsHome) > 0): ?>
                <?php $d = 0; foreach ($reviewsHome as $rev): $d++; ?>
                    <div class="test-card animate-fade-up delay-<?php echo min($d, 4); ?>">
                        <?php echo icon_star_rating((int) $rev['rating']); ?>
                        <p class="test-text"><?php echo htmlspecialchars($rev['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="test-author">
                            <div class="t-avatar" style="display:flex;align-items:center;justify-content:center;font-weight:800;background:var(--primary-light);color:var(--primary-dark);">
                                <?php echo strtoupper(substr($rev['patient_name'], 0, 1)); ?>
                            </div>
                            <div class="t-info">
                                <h4><?php echo htmlspecialchars($rev['patient_name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                <p><?php echo htmlspecialchars($rev['hospital_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                        <p style="margin-top:1rem;font-size:0.8rem;color:var(--text-muted);"><?php echo date('M j, Y', strtotime($rev['created_at'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="test-card animate-fade-up" style="grid-column: 1 / -1; text-align: center;">
                    <p class="test-text" style="font-style: normal;">No public reviews yet. When patients complete a test or vaccination and submit feedback, it will appear here for everyone.</p>
                    <p style="margin-top: 1rem;"><a class="btn-primary" href="faq.php#reviews-faq">Read how reviews work</a></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
     <section class="cta-section reveal-section">
        <div class="cta-content animate-fade-up">
            <h2>Be a Part of the Change</h2>
            <p>Whether you are seeking safety for yourself, or looking to serve the community as a healthcare provider, your journey starts here.</p>
            <div style="display: flex; gap: 1.5rem; justify-content: center; flex-wrap: wrap; margin-top: 2rem;">
                <a href="auth/register.php" class="btn-massive btn-glass" style="background: white; color: var(--primary) !important; border-color: white;">Register as Patient</a>
                <a href="auth/register.php" class="btn-massive btn-glass">Register as Hospital</a>
            </div>
            <p style="margin-top: 3rem; font-size: 1.1rem; opacity: 0.8;">Already a member? <a href="auth/login.php" style="color: white; text-decoration: underline; font-weight: bold;">Login here</a></p>
        </div>
    </section>

    <div class="landing-reviews-bar reveal-section">
        <div class="landing-reviews-bar-inner">
            <div>
                <h3>Share your experience</h3>
                <p>Completed a visit? Your review helps others choose safe, reliable care.</p>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:0.75rem;">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'Patient'): ?>
                    <a href="<?php echo $base_url; ?>/patient/review.php" class="btn-primary">Submit a review</a>
                    <a href="<?php echo $base_url; ?>/faq.php#reviews-faq" class="btn-outline">FAQ</a>
                <?php else: ?>
                    <a href="<?php echo $base_url; ?>/auth/login.php" class="btn-primary">Log in to review</a>
                    <a href="<?php echo $base_url; ?>/faq.php#reviews-faq" class="btn-outline">How reviews work</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- CALL TO ACTION -->
   

</div>

<?php include 'includes/footer.php'; ?>
