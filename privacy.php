<?php
// privacy.php
session_start();
include 'includes/header.php';
?>

<style>
/* ==========================================================================
   PREMIUM PRIVACY POLICY STYLES
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
    border-bottom: 5px solid var(--primary);
}

.legal-hero::after {
    content: ''; position: absolute; bottom: 0; left: 50%; transform: translate(-50%, 50%) rotate(45deg);
    width: 30px; height: 30px; background: var(--primary);
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
    content: ''; display: block; width: 6px; height: 25px; background: var(--primary); border-radius: 10px;
}
.legal-section p, .legal-section ul {
    font-size: 1.1rem; color: var(--text-secondary); line-height: 1.8; margin-bottom: 1rem;
}
.legal-section ul { padding-left: 1.5rem; }
.legal-section li { margin-bottom: 0.8rem; }

.highlight-box {
    background: #e0e7ff;
    border-left: 4px solid var(--primary);
    padding: 1.5rem;
    border-radius: 0 var(--radius-md) var(--radius-md) 0;
    margin: 2rem 0;
    color: var(--primary-dark);
    font-weight: 600;
}

@media (max-width: 768px) {
    .legal-card { padding: 2rem; }
    .legal-hero h1 { font-size: 2.5rem; }
}
</style>

<div class="legal-wrapper">
    <div class="legal-hero animate-fade-up">
        <h1>Privacy Policy</h1>
        <p>Your data security and medical privacy are our utmost priorities.</p>
    </div>

    <div class="legal-content-container animate-fade-up delay-2">
        <div class="legal-card">
            <div class="legal-date">Last Updated: <?php echo date('F d, Y'); ?></div>

            <div class="highlight-box">
                At VaxiCare, we are committed to safeguarding the confidentiality of your personal and medical information. This document outlines how we collect, use, and protect your data.
            </div>

            <div class="legal-section">
                <h2>1. Information We Collect</h2>
                <p>When you register and use the VaxiCare platform, we collect the following types of information:</p>
                <ul>
                    <li><strong>Personal Identity Data:</strong> Full name, email address, phone number, and physical address.</li>
                    <li><strong>Medical Data:</strong> Booking history, COVID-19 test results, vaccination status, and selected hospital facilities.</li>
                    <li><strong>Hospital Data:</strong> Facility name, license numbers, location, and representative contact information (for hospital accounts only).</li>
                </ul>
            </div>

            <div class="legal-section">
                <h2>2. How We Use Your Information</h2>
                <p>The information collected is used strictly for the purpose of facilitating your healthcare journey. Specifically, we use your data to:</p>
                <ul>
                    <li>Process your bookings and appointments with verified hospitals.</li>
                    <li>Generate and store digital medical certificates and test reports.</li>
                    <li>Send important notifications regarding appointment status or password recovery.</li>
                    <li>Provide authorized administrators with anonymized analytical data to monitor public health trends.</li>
                </ul>
            </div>

            <div class="legal-section">
                <h2>3. Data Protection and Encryption</h2>
                <p>We employ state-of-the-art security measures to protect your information against unauthorized access, alteration, disclosure, or destruction. All passwords are encrypted using strong cryptographic hashing algorithms, and sensitive medical reports are only accessible by you and your authorized healthcare provider.</p>
            </div>

            <div class="legal-section">
                <h2>4. Data Sharing and Third Parties</h2>
                <p>VaxiCare <strong>does not</strong> sell, trade, or rent your personal identification information to others. Your medical data is only shared with the specific hospital you choose to book with. Anonymized, non-identifiable statistics may be shared with government health authorities strictly for monitoring the pandemic situation.</p>
            </div>

            <div class="legal-section">
                <h2>5. Your Rights and Control</h2>
                <p>You have the right to review the personal information we hold about you. You can update your profile details at any time through your dashboard. If you wish to permanently delete your account and all associated data, you may contact the system administrator.</p>
            </div>
            
            <div style="margin-top: 4rem; text-align: center; padding-top: 2rem; border-top: 1px solid var(--border-light);">
                <p style="color: var(--text-muted); margin-bottom: 1rem;">If you have any questions about this Privacy Policy, please contact our Data Protection Officer.</p>
                <a href="mailto:privacy@vaxicare.com" style="color: var(--primary); font-weight: bold; text-decoration: none;">privacy@vaxicare.com</a>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
