<?php
// services.php
session_start();
include 'includes/header.php';
?>

<style>
/* ==========================================================================
   PREMIUM SERVICES PAGE STYLES
   ========================================================================== */
.services-wrapper {
    font-family: var(--font-sans);
    background-color: var(--bg-main);
    overflow-x: hidden;
}

/* --- Hero Section --- */
.services-hero {
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(14, 165, 233, 0.85)), url('https://images.unsplash.com/photo-1516549655169-df83a0774514?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover no-repeat;
    background-attachment: fixed;
    padding: 10rem 0 8rem;
    color: white;
    text-align: center;
    position: relative;
    clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
}

.services-hero::before {
    content: ''; position: absolute; inset: 0;
    background: url('data:image/svg+xml;utf8,<svg width="40" height="40" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="1.5" fill="rgba(255,255,255,0.05)"/></svg>') repeat;
}

.hero-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 10;
}

.hero-content h1 {
    font-size: 4.5rem; font-weight: 900; margin-bottom: 1.5rem; text-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.hero-content p {
    font-size: 1.35rem; opacity: 0.9; margin-bottom: 2rem; line-height: 1.6;
}

/* --- Services Grid --- */
.services-section {
    padding: 6rem 0;
    position: relative;
    margin-top: -5rem;
    z-index: 20;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 3rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.service-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 3rem;
    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
    border: 1px solid var(--border);
}

.service-card::after {
    content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 5px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
    transform: scaleX(0); transform-origin: left; transition: transform 0.4s ease;
}

.service-card:hover {
    transform: translateY(-15px);
    box-shadow: 0 30px 60px rgba(14, 165, 233, 0.15);
}

.service-card:hover::after { transform: scaleX(1); }

.s-icon {
    width: 80px; height: 80px;
    border-radius: 20px;
    background: var(--bg-main);
    display: flex; align-items: center; justify-content: center;
    font-size: 2.5rem; color: var(--primary); margin-bottom: 2rem;
    transition: transform 0.4s;
    box-shadow: inset 0 2px 5px rgba(255,255,255,0.8), 0 5px 15px rgba(0,0,0,0.05);
}

.service-card:hover .s-icon {
    transform: scale(1.1) rotateY(180deg);
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: white;
}

.service-card h3 { font-size: 1.8rem; margin-bottom: 1rem; color: var(--text-primary); font-weight: 800; }
.service-card p { font-size: 1.1rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: 2rem; }

.service-link {
    display: inline-flex; align-items: center; gap: 0.5rem;
    color: var(--primary); font-weight: 700; font-size: 1.05rem;
    text-transform: uppercase; letter-spacing: 1px;
    transition: color 0.3s;
}

.service-card:hover .service-link { color: var(--accent); gap: 1rem; }

/* --- Detailed Info Section --- */
.info-section {
    padding: 8rem 0;
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: white;
}

.info-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; max-width: 1200px; margin: 0 auto; padding: 0 2rem; align-items: center;
}

.info-content h2 { font-size: 3rem; margin-bottom: 1.5rem; font-weight: 900; line-height: 1.2; }
.info-content p { font-size: 1.15rem; color: #94a3b8; line-height: 1.8; margin-bottom: 2rem; }

.perk-list { list-style: none; padding: 0; }
.perk-list li {
    display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem; font-size: 1.1rem;
}
.perk-list li::before {
    content: '✓'; width: 28px; height: 28px; background: #10b981; color: white; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9rem; flex-shrink: 0;
}

.info-image img {
    width: 100%; border-radius: var(--radius-xl); box-shadow: 0 25px 50px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.1);
}

@media (max-width: 992px) {
    .info-grid { grid-template-columns: 1fr; text-align: center; }
    .perk-list li { justify-content: center; text-align: left; }
}

@media (max-width: 768px) {
    .hero-content h1 { font-size: 3rem; }
    .services-hero { clip-path: polygon(0 0, 100% 0, 100% 95%, 0 100%); padding: 8rem 2rem 6rem; }
    .services-grid { padding: 0 1rem; }
}
</style>

<div class="services-wrapper">
    
    <!-- Hero Section -->
    <section class="services-hero">
        <div class="hero-content animate-fade-up">
            <div style="display: inline-block; padding: 0.5rem 1.5rem; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); border-radius: 50px; font-weight: 800; letter-spacing: 2px; margin-bottom: 1.5rem;">
                PREMIUM HEALTHCARE
            </div>
            <h1>Our Comprehensive Services</h1>
            <p>VaxiCare brings you a fully digital, secure, and instant way to manage your COVID-19 health requirements. Explore the services we offer to keep our community safe.</p>
        </div>
    </section>

    <!-- Services Grid -->
    <section class="services-section">
        <div class="services-grid">
            
            <div class="service-card animate-fade-up delay-1">
                <div class="s-icon">🧪</div>
                <h3>Diagnostic COVID Tests</h3>
                <p>Book an RT-PCR or Rapid Antigen test at verified facilities near you. Skip the lines by scheduling an exact time slot online. Secure and fast processing.</p>
                <a href="auth/register.php" class="service-link">Book a Test <span>&rarr;</span></a>
            </div>
            
            <div class="service-card animate-fade-up delay-2">
                <div class="s-icon">💉</div>
                <h3>Vaccination Programs</h3>
                <p>Secure your COVID-19 vaccine dose easily. We track multiple vaccine brands like Pfizer, Moderna, and Sinovac to ensure you get the dose you need safely.</p>
                <a href="auth/register.php" class="service-link">Get Vaccinated <span>&rarr;</span></a>
            </div>
            
            <div class="service-card animate-fade-up delay-3">
                <div class="s-icon">📱</div>
                <h3>Digital Health Certificates</h3>
                <p>Say goodbye to paper records. Get officially recognized, verifiable digital certificates for your travel and workplace directly from your dashboard.</p>
                <a href="auth/register.php" class="service-link">View Records <span>&rarr;</span></a>
            </div>
            
            <div class="service-card animate-fade-up delay-1">
                <div class="s-icon">🏥</div>
                <h3>Hospital Network Integration</h3>
                <p>For medical centers: Join our network to manage your patients digitally, update test results instantly, and track your vaccine inventory seamlessly.</p>
                <a href="auth/register.php" class="service-link">Register Facility <span>&rarr;</span></a>
            </div>
            
            <div class="service-card animate-fade-up delay-2">
                <div class="s-icon">📊</div>
                <h3>Global Health Analytics</h3>
                <p>Our platform provides authorized administrators with real-time analytics to monitor infection rates and vaccination progress across the nation.</p>
                <a href="auth/login.php" class="service-link">Admin Access <span>&rarr;</span></a>
            </div>
            
            <div class="service-card animate-fade-up delay-3">
                <div class="s-icon">🔒</div>
                <h3>Priority Patient Support</h3>
                <p>Experience uninterrupted, priority access to healthcare guidance, system notifications, and prompt updates regarding your medical appointments.</p>
                <a href="auth/register.php" class="service-link">Join Platform <span>&rarr;</span></a>
            </div>
            
        </div>
    </section>

    <!-- Info Section -->
    <section class="info-section">
        <div class="info-grid">
            <div class="info-content animate-fade-up">
                <h2>Redefining Medical Convenience</h2>
                <p>We believe that accessing essential healthcare should not be stressful. Our digital ecosystem eliminates physical paperwork and reduces waiting times to zero.</p>
                
                <ul class="perk-list">
                    <li><strong>No Waiting in Lines:</strong> Scheduled appointments mean you arrive exactly when it's your turn.</li>
                    <li><strong>100% Data Privacy:</strong> Your medical results and personal address are encrypted and protected.</li>
                    <li><strong>Instant Updates:</strong> Get real-time status changes when your test results are ready.</li>
                    <li><strong>Verified Network:</strong> All partnered hospitals are strictly vetted by the Ministry of Health.</li>
                </ul>
            </div>
            <div class="info-image animate-fade-up delay-2">
                <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Healthcare Services">
            </div>
        </div>
    </section>

    <div style="text-align: center; padding: 6rem 2rem; background: var(--bg-main);">
        <h2 style="font-size: 2.5rem; font-weight: 900; margin-bottom: 1.5rem;">Ready to experience seamless healthcare?</h2>
        <a href="auth/register.php" class="btn-primary" style="padding: 1.2rem 3rem; font-size: 1.2rem; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);">Create Your Free Account Now</a>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
