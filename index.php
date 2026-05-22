<?php
// index.php — public landing (logged-in users may still view this page)
session_start();

require_once 'config/database.php';
require_once 'includes/svg_icons.php';
require_once 'includes/image_paths.php';

include 'includes/header.php';
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/landing-pages.css">

<style>
:root {
    --img-hero: url('<?php echo $img_base; ?>hero-home.jpg');
    --img-patient: url('<?php echo $img_base; ?>patient-care.jpg');
    --img-hospital: url('<?php echo $img_base; ?>hospital-building.jpg');
    --img-safety: url('<?php echo $img_base; ?>doctor-team.jpg');
    --img-services: url('<?php echo $img_base; ?>services-lab.jpg');
    --img-vax: url('<?php echo $img_base; ?>vaccination.jpg');
    --img-test: url('<?php echo $img_base; ?>covid-test.jpg');
    --img-clinic: url('<?php echo $img_base; ?>clinic-hall.jpg');
}
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
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(14, 165, 233, 0.9)), var(--img-hero) center/cover no-repeat;
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
.hero-p{padding: 20px;}
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
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.92), rgba(30, 41, 59, 0.9)), var(--img-hospital) center/cover no-repeat;
    color: white;
}

.services-preview {
    padding: 7rem 2rem;
    background: linear-gradient(180deg, #fff 0%, #f0f9ff 100%);
}
.services-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2.5rem;
    max-width: 1200px;
    margin: 3rem auto 0;
}
.service-preview-card {
    border-radius: 24px;
    overflow: hidden;
    background: white;
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
    border: 1px solid var(--border);
}
.service-preview-card img { width: 100%; height: 220px; object-fit: cover; }
.service-preview-card .sp-body { padding: 2rem; }
.service-preview-card h3 { font-size: 1.5rem; margin-bottom: 0.75rem; }
.service-preview-card p { color: var(--text-secondary); line-height: 1.7; margin-bottom: 1.25rem; }

.platform-detail {
    padding: 7rem 2rem;
    background: var(--img-clinic) center/cover;
    position: relative;
}
.platform-detail::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,0.93);
}
.platform-detail-inner {
    position: relative;
    z-index: 1;
    max-width: 1200px;
    margin: 0 auto;
}
.detail-columns {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}
.detail-box {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    border: 1px solid var(--border);
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
}
.detail-box h4 { font-size: 1.25rem; margin-bottom: 0.75rem; color: var(--primary-dark); }
.detail-box p { color: var(--text-secondary); line-height: 1.7; margin: 0; }

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

/* --- Trust stats & Why sections --- */
.landing-stats-bar {
    background: linear-gradient(90deg, #0f172a, #1e3a5f);
    padding: 3.5rem 2rem;
    margin: 4rem 0;
}
.landing-stats-inner {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 2rem;
    text-align: center;
}
.landing-stat-num {
    display: block;
    font-size: 2.5rem;
    font-weight: 900;
    font-family: 'Outfit', sans-serif;
    color: #38bdf8;
}
.landing-stat-label { color: rgba(255,255,255,0.85); font-size: 1rem; margin-top: 0.35rem; display: block; }

.landing-why { padding: 5rem 2rem; max-width: 1200px; margin: 0 auto; }
.landing-why-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}
.landing-why-card {
    background: white;
    border-radius: 20px;
    padding: 2.25rem;
    border: 1px solid var(--border);
    box-shadow: 0 12px 40px rgba(15,23,42,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.landing-why-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 24px 48px rgba(79,70,229,0.12);
}
.landing-why-icon { margin-bottom: 1.25rem; color: var(--primary); }
.landing-why-card h3 { font-size: 1.35rem; margin-bottom: 0.75rem; color: var(--text-primary); }
.landing-why-card p { color: var(--text-secondary); line-height: 1.7; margin: 0; }

.landing-safety {
    background: linear-gradient(180deg, rgba(240, 249, 255, 0.95) 0%, rgba(255, 255, 255, 0.92) 100%), var(--img-safety) center/cover;
    padding: 6rem 2rem;
    margin: 2rem 0;
}
.landing-safety-inner {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}
@media (max-width: 768px) {
    .landing-safety-inner { grid-template-columns: 1fr; }
}
.landing-safety-list {
    list-style: none;
    padding: 0;
    margin: 1.5rem 0 0;
}
.landing-safety-list li {
    padding: 1rem 0 1rem 2rem;
    border-bottom: 1px solid var(--border);
    position: relative;
    color: var(--text-secondary);
    line-height: 1.6;
}
.landing-safety-list li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: var(--accent);
    font-weight: 800;
}
.landing-safety-visual img {
    width: 100%;
    border-radius: 24px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
}

/* --- Community Reviews (removed) --- */
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
.hero-p{
    margin-left: 130px;
    font-weight: bolder;
    padding: 30px 30px 0px 0px;
    
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
            <p class="hero-p" style="color:white; padding: 10px;">Join the movement that is transforming our society. VaxiCare is a community-driven platform designed to make COVID-19 testing and vaccinations accessible, transparent, and seamless for everyone.</p>
            <div class="hero-buttons">
                <a href="auth/register.php" class="btn-massive btn-glass" style="background: white; color: var(--primary) !important;">Join the Community</a>
                <a href="how-it-works.php" class="btn-massive btn-glass">Learn How It Works</a>
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
            <p>Taking control of your health has never been this easy. Follow these simple steps to get tested or vaccinated safely. <a href="how-it-works.php#for-patients" style="color:var(--primary);font-weight:700;">Read full patient guide →</a></p>
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
                        <p>Search for authorized medical centers near your location. View their available vaccines and testing facilities in one place.</p>
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
                <img src="<?php echo $img_base; ?>patient-care.jpg" alt="Patient Journey">
            </div>
        </div>
    </section>

    <!-- FOR HOSPITALS: HOW TO WORK -->
    <section class="hospital-journey reveal-section">
        <div class="section-header-center animate-fade-up">
            <h2 style="color: white; font-size: 3rem;">Empowering Healthcare Providers</h2>
            <p style="color: rgba(255,255,255,0.8);">If you represent a hospital or clinic, join our network to serve the community efficiently and digitally. <a href="how-it-works.php#for-hospitals" style="color:#34d399;font-weight:700;">Read full hospital guide →</a></p>
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
                <img src="<?php echo $img_base; ?>hospital-building.jpg" alt="Hospital Management">
            </div>
        </div>
    </section>

    <!-- SERVICES PREVIEW -->
    <section id="services" class="services-preview reveal-section">
        <div class="section-header-center animate-fade-up">
            <h2>Our medical services</h2>
            <p>COVID-19 testing and vaccination booking with digital results — everything connected in one platform.</p>
            <p style="margin-top:1rem;"><a href="services.php" class="btn-primary">View full services page</a> &nbsp; <a href="how-it-works.php" class="btn-outline">Detailed how it works</a></p>
        </div>
        <div class="services-preview-grid">
            <div class="service-preview-card animate-fade-up delay-1">
                <img src="<?php echo $img_base; ?>covid-test.jpg" alt="COVID testing">
                <div class="sp-body">
                    <h3>COVID-19 testing</h3>
                    <p>Book a diagnostic test at an approved hospital. Receive Negative or Positive results digitally after your visit — no need to return for paper reports.</p>
                    <a href="how-it-works.php#booking-types" class="btn-outline">Learn more</a>
                </div>
            </div>
            <div class="service-preview-card animate-fade-up delay-2">
                <img src="<?php echo $img_base; ?>vaccination.jpg" alt="Vaccination">
                <div class="sp-body">
                    <h3>Vaccination programs</h3>
                    <p>Schedule vaccination doses with vaccine choice where available. Track completion status and follow-up doses from your patient dashboard.</p>
                    <a href="auth/register.php" class="btn-outline">Book vaccination</a>
                </div>
            </div>
            <div class="service-preview-card animate-fade-up delay-3">
                <img src="<?php echo $img_base; ?>services-lab.jpg" alt="Lab services">
                <div class="sp-body">
                    <h3>Hospital network</h3>
                    <p>Clinics and hospitals manage queues, approve bookings, and update records in real time — reducing crowding and improving safety for everyone.</p>
                    <a href="auth/register.php" class="btn-outline">Register hospital</a>
                </div>
            </div>
        </div>
    </section>

    <!-- PLATFORM DETAILS -->
    <section class="platform-detail reveal-section">
        <div class="platform-detail-inner">
            <div class="section-header-center">
                <h2>Built for three roles — one system</h2>
                <p>Patients book care, hospitals deliver it, and administrators keep the network safe and organized.</p>
            </div>
            <div class="detail-columns">
                <div class="detail-box animate-fade-up delay-1">
                    <h4>For patients</h4>
                    <p>Search hospitals, book tests or vaccines, track pending and approved visits, and read official results when your appointment is marked completed.</p>
                </div>
                <div class="detail-box animate-fade-up delay-2">
                    <h4>For hospitals</h4>
                    <p>Approve bookings, manage daily appointments, add vaccines, and file test or vaccination outcomes so patients see updates instantly.</p>
                </div>
                <div class="detail-box animate-fade-up delay-3">
                    <h4>For administrators</h4>
                    <p>Verify new hospitals, manage global vaccine list, view all bookings, and export CSV reports for any date range.</p>
                </div>
                <div class="detail-box animate-fade-up delay-4">
                    <h4>Privacy & security</h4>
                    <p>Passwords are hashed. Sessions protect your login. Each role only sees the tools they need — patient, hospital, or admin console.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- TRUST & STATS -->
    <section class="landing-stats-bar reveal-section">
        <div class="landing-stats-inner">
            <div class="landing-stat-item">
                <span class="landing-stat-num">24/7</span>
                <span class="landing-stat-label">Online booking</span>
            </div>
            <div class="landing-stat-item">
                <span class="landing-stat-num">100%</span>
                <span class="landing-stat-label">Digital records</span>
            </div>
            <div class="landing-stat-item">
                <span class="landing-stat-num">Fast</span>
                <span class="landing-stat-label">Result updates</span>
            </div>
            <div class="landing-stat-item">
                <span class="landing-stat-num">Secure</span>
                <span class="landing-stat-label">Patient data</span>
            </div>
        </div>
    </section>

    <!-- WHY CHOOSE VAXICARE -->
    <section class="landing-why reveal-section" id="why-vaxicare">
        <div class="section-header-center animate-fade-up">
            <h2>Why families choose VaxiCare</h2>
            <p>One simple platform for COVID-19 tests, vaccinations, and hospital coordination — built for real clinics on XAMPP localhost or live hosting.</p>
        </div>
        <div class="landing-why-grid">
            <div class="landing-why-card animate-fade-up delay-1">
                <div class="landing-why-icon"><?php echo icon_hospital_building(40, 40); ?></div>
                <h3>Find approved hospitals</h3>
                <p>Search by location and book at verified facilities only. No phone calls or paper forms.</p>
            </div>
            <div class="landing-why-card animate-fade-up delay-2">
                <div class="landing-why-icon"><?php echo icon_calendar(40, 40); ?></div>
                <h3>Pick your date</h3>
                <p>Choose test or vaccination, select a vaccine when needed, and confirm your appointment in minutes.</p>
            </div>
            <div class="landing-why-card animate-fade-up delay-3">
                <div class="landing-why-icon"><?php echo icon_clipboard_ok(40, 40); ?></div>
                <h3>Results in your dashboard</h3>
                <p>Hospitals update test results and vaccination status online. You see updates as soon as staff files them.</p>
            </div>
            <div class="landing-why-card animate-fade-up delay-4">
                <div class="landing-why-icon"><?php echo icon_shield_admin(40, 40); ?></div>
                <h3>Admin oversight</h3>
                <p>Platform admins approve hospitals, manage vaccines, and export booking reports for accountability.</p>
            </div>
        </div>
    </section>

    <!-- SAFETY STEPS -->
    <section class="landing-safety reveal-section">
        <div class="landing-safety-inner">
            <div class="landing-safety-text animate-fade-up">
                <h2>Stay protected — step by step</h2>
                <ul class="landing-safety-list">
                    <li><strong>Book online</strong> — reserve a slot at a partner hospital.</li>
                    <li><strong>Visit on your date</strong> — bring ID; staff will check you in from the system.</li>
                    <li><strong>Get tested or vaccinated</strong> — care delivered at the facility.</li>
                    <li><strong>Check your portal</strong> — results and vaccination status appear on your patient dashboard.</li>
                </ul>
                <a href="auth/register.php" class="btn-primary" style="margin-top:1.5rem;">Create free patient account</a>
            </div>
            <div class="landing-safety-visual animate-fade-up delay-2">
                <img src="<?php echo $img_base; ?>doctor-team.jpg" alt="Healthcare professional">
            </div>
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

    <!-- <div class="landing-reviews-bar reveal-section">
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
    </div> -->

    <!-- CALL TO ACTION -->
   

</div>

<?php include 'includes/footer.php'; ?>
