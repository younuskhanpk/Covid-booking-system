<?php
// terms.php
session_start();
include 'includes/header.php';
?>

<style>
/* ==========================================================================
   PREMIUM TERMS OF SERVICE STYLES
   ========================================================================== */
.legal-wrapper {
    font-family: var(--font-sans);
    background-color: #f8fafc;
    padding-bottom: 8rem;
}

.legal-hero {
    background: linear-gradient(135deg, #0f172a, #334155);
    padding: 10rem 2rem 8rem;
    color: white;
    text-align: center;
    position: relative;
    border-bottom: 5px solid var(--accent);
}

.legal-hero::after {
    content: ''; position: absolute; bottom: 0; left: 50%; transform: translate(-50%, 50%) rotate(45deg);
    width: 30px; height: 30px; background: var(--accent);
}

.legal-hero h1 { font-size: 3.5rem; font-weight: 900; margin-bottom: 1rem; letter-spacing: -1px; }
.legal-hero p { font-size: 1.2rem; color: #cbd5e1; }

.legal-content-container {
    max-width: 900px;
    margin: -4rem auto 0;
    position: relative;
    z-index: 10;
    padding: 0 2rem;
}

.legal-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 4rem;
    box-shadow: 0 25px 50px rgba(0,0,0,0.05);
    border: 1px solid var(--border);
}

.legal-date {
    display: inline-block;
    padding: 0.5rem 1.2rem;
    background: #f1f5f9;
    color: var(--text-muted);
    font-weight: 700;
    border-radius: 50px;
    margin-bottom: 3rem;
    font-size: 0.9rem;
    border: 1px solid var(--border-light);
}

.legal-section { margin-bottom: 3rem; }
.legal-section h2 { 
    font-size: 1.8rem; 
    color: var(--primary-dark); 
    margin-bottom: 1.2rem; 
    display: flex; 
    align-items: center; 
    gap: 0.8rem; 
}
.legal-section h2::before {
    content: ''; display: block; width: 6px; height: 25px; background: var(--accent); border-radius: 10px;
}
.legal-section p, .legal-section ul {
    font-size: 1.1rem; color: var(--text-secondary); line-height: 1.8; margin-bottom: 1rem;
}
.legal-section ul { padding-left: 1.5rem; }
.legal-section li { margin-bottom: 0.8rem; }

.highlight-box {
    background: #fffbeb;
    border-left: 4px solid #f59e0b;
    padding: 1.5rem;
    border-radius: 0 var(--radius-md) var(--radius-md) 0;
    margin: 2rem 0;
    color: #b45309;
    font-weight: 600;
}

@media (max-width: 768px) {
    .legal-card { padding: 2rem; }
    .legal-hero h1 { font-size: 2.5rem; }
}
</style>

<div class="legal-wrapper">
    <div class="legal-hero animate-fade-up">
        <h1>Terms of Service</h1>
        <p>Please read these terms carefully before using the VaxiCare platform.</p>
    </div>

    <div class="legal-content-container animate-fade-up delay-2">
        <div class="legal-card">
            <div class="legal-date">Effective Date: <?php echo date('F d, Y'); ?></div>

            <div class="highlight-box">
                By accessing, browsing, or using the VaxiCare platform, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service.
            </div>

            <div class="legal-section">
                <h2>1. Acceptance of Terms</h2>
                <p>Welcome to VaxiCare. These Terms of Service govern your use of our COVID-19 Test and Vaccination Booking System. If you do not agree with any part of these terms, you must not use our platform or services.</p>
            </div>

            <div class="legal-section">
                <h2>2. User Accounts and Responsibilities</h2>
                <p>To access certain features, you must create an account as a Patient or Hospital. You are responsible for:</p>
                <ul>
                    <li>Providing accurate, current, and complete information during registration.</li>
                    <li>Maintaining the confidentiality of your account credentials (password).</li>
                    <li>All activities that occur under your account.</li>
                    <li>Notifying us immediately of any unauthorized use of your account.</li>
                </ul>
            </div>

            <div class="legal-section">
                <h2>3. Hospital Facility Guidelines</h2>
                <p>Healthcare facilities registering on the platform must hold valid, recognized medical licenses. VaxiCare Administration reserves the right to reject, suspend, or permanently delete any hospital account that provides false information or violates medical ethics.</p>
            </div>

            <div class="legal-section">
                <h2>4. Medical Disclaimer</h2>
                <p>VaxiCare is a booking and digital record-keeping platform. We do not provide medical advice, diagnosis, or treatment. All medical procedures, including testing and vaccination, are the sole responsibility of the registered hospital you visit. Always consult a qualified healthcare provider for medical concerns.</p>
            </div>

            <div class="legal-section">
                <h2>5. System Availability</h2>
                <p>While we strive for 99.99% uptime, VaxiCare does not guarantee continuous, uninterrupted access to the platform. The system may occasionally be down for maintenance, upgrades, or due to factors beyond our control.</p>
            </div>
            
            <div class="legal-section">
                <h2>6. Modifications to Terms</h2>
                <p>We reserve the right to update or modify these Terms of Service at any time without prior notice. Your continued use of the platform following any changes indicates your acceptance of the new terms.</p>
            </div>
            
            <div style="margin-top: 4rem; text-align: center; padding-top: 2rem; border-top: 1px solid var(--border-light);">
                <p style="color: var(--text-muted); margin-bottom: 1rem;">For legal inquiries or clarifications regarding these terms, please contact our legal department.</p>
                <a href="mailto:legal@vaxicare.com" style="color: var(--primary); font-weight: bold; text-decoration: none;">legal@vaxicare.com</a>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
