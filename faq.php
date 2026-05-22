<?php
// faq.php
session_start();
require_once __DIR__ . '/includes/svg_icons.php';
require_once __DIR__ . '/includes/image_paths.php';
include 'includes/header.php';
?>

<style>
/* ==========================================================================
   PREMIUM FAQ PAGE STYLES
   ========================================================================== */
.static-page-wrapper {
    font-family: var(--font-sans);
    background-color: var(--bg-main);
    padding-bottom: 8rem;
    min-height: 100vh;
}

.static-hero {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.9), rgba(79, 70, 229, 0.95)), url('<?php echo $img_base; ?>how-it-works-bg.jpg') center/cover no-repeat;
    background-attachment: fixed;
    padding: 10rem 2rem 8rem;
    color: white;
    text-align: center;
    clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%);
    position: relative;
}

.static-hero::before {
    content: ''; position: absolute; inset: 0; background: rgba(0,0,0,0.3); z-index: 1;
}

.static-hero-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 10;
}

.static-hero h1 { font-size: 4rem; font-weight: 900; margin-bottom: 1.5rem; text-shadow: 0 10px 20px rgba(0,0,0,0.3); }
.static-hero p { font-size: 1.25rem; opacity: 0.9; line-height: 1.6; }

.faq-container {
    max-width: 900px;
    margin: -4rem auto 0;
    position: relative;
    z-index: 20;
    padding: 0 2rem;
}

.faq-group { margin-bottom: 3rem; }
.faq-group h2 { font-size: 2rem; color: var(--primary-dark); margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--border); display: flex; align-items: center; flex-wrap: wrap; gap: 0.75rem; }
.faq-group h2 .svg-icon { flex-shrink: 0; color: var(--accent); }

.faq-item {
    background: white;
    border-radius: var(--radius-lg);
    margin-bottom: 1rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.03);
    border: 1px solid var(--border);
    overflow: hidden;
    transition: all 0.3s;
}

.faq-item:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.08); transform: translateY(-2px); border-color: var(--primary-light);}

details { width: 100%; }
details summary {
    padding: 1.5rem;
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--text-primary);
    cursor: pointer;
    list-style: none;
    display: flex; justify-content: space-between; align-items: center;
    transition: color 0.3s;
}
details summary::-webkit-details-marker { display: none; }
details summary::after {
    content: '+';
    font-size: 1.5rem;
    color: var(--primary);
    transition: transform 0.3s;
}
details[open] summary { color: var(--primary); border-bottom: 1px solid var(--border-light); }
details[open] summary::after { content: '−'; transform: rotate(180deg); }

.faq-content {
    padding: 1.5rem;
    font-size: 1.05rem;
    color: var(--text-secondary);
    line-height: 1.7;
    background: #f8fafc;
    animation: fadeInDown 0.4s ease forwards;
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.contact-box {
    background: linear-gradient(135deg, white, #f1f5f9);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 4rem 2rem;
    text-align: center;
    margin-top: 5rem;
    box-shadow: 0 20px 40px rgba(0,0,0,0.05);
}
.contact-box h3 { font-size: 2rem; margin-bottom: 1rem; color: var(--text-primary); }
</style>

<div class="static-page-wrapper">
    <div class="static-hero animate-fade-up">
        <div class="static-hero-content">
            <div style="display: inline-block; padding: 0.4rem 1.2rem; background: rgba(255,255,255,0.2); border-radius: 50px; font-weight: bold; letter-spacing: 1px; margin-bottom: 1rem;">SUPPORT CENTER</div>
            <h1>Frequently Asked Questions</h1>
            <p>Find quick answers to common queries regarding account registration, booking tests, accessing reports, and hospital partnerships.</p>
        </div>
    </div>

    <div class="faq-container animate-fade-up delay-2">
        
        <!-- Patient Queries -->
        <div class="faq-group">
            <h2><?php echo icon_stethoscope(36, 36); ?> For patients</h2>
            
            <div class="faq-item">
                <details>
                    <summary>How do I register for a COVID-19 test or vaccination?</summary>
                    <div class="faq-content">
                        Simply click on "Register" at the top right of the homepage. Select the "Patient Registration" tab, fill out your details, and log into your dashboard. From there, you can view all available hospitals and book a slot.
                    </div>
                </details>
            </div>
            
            <div class="faq-item">
                <details>
                    <summary>Is my medical data completely secure?</summary>
                    <div class="faq-content">
                        Yes, 100%. VaxiCare employs industry-standard encryption for all user data. Only you and the medical facility you book with can view your test results and vaccination records.
                    </div>
                </details>
            </div>
            
            <div class="faq-item">
                <details>
                    <summary>How will I know when my test result is ready?</summary>
                    <div class="faq-content">
                        Once the hospital updates your status, you will instantly see it on your patient dashboard. The status will change from "Pending" to either "Negative" or "Positive" and a digital certificate will become available for download.
                    </div>
                </details>
            </div>
        </div>

        <!-- Hospital Queries -->
        <div class="faq-group">
            <h2><?php echo icon_hospital_building(36, 36); ?> For hospitals</h2>
            
            <div class="faq-item">
                <details>
                    <summary>How long does the hospital approval process take?</summary>
                    <div class="faq-content">
                        After you register your facility, the VaxiCare Administration team will review your provided License Number and Location. This verification process typically takes 24-48 hours. Once approved, you can start receiving bookings.
                    </div>
                </details>
            </div>
            
            <div class="faq-item">
                <details>
                    <summary>Can we manage multiple vaccine brands?</summary>
                    <div class="faq-content">
                        Absolutely. When updating a patient's vaccination record, the system allows you to select the specific vaccine brand administered from the globally approved inventory list managed by the Admin.
                    </div>
                </details>
            </div>
        </div>

        <!-- General Queries -->
        <div class="faq-group">
            <h2><?php echo icon_gear(36, 36); ?> System & account</h2>
            
            <div class="faq-item">
                <details>
                    <summary>I forgot my password, how do I recover it?</summary>
                    <div class="faq-content">
                        Go to the Login page and click on "Forgot Password?". Enter your registered email address. The system will safely verify your email and allow you to set a new password securely.
                    </div>
                </details>
            </div>
            
            <div class="faq-item">
                <details>
                    <summary>Are there any hidden fees to use this platform?</summary>
                    <div class="faq-content">
                        No. Registration and booking through VaxiCare are completely free of charge. You only pay the hospital directly for the actual medical test or vaccine administration, if applicable based on your local health guidelines.
                    </div>
                </details>
            </div>
        </div>

        <div class="contact-box animate-fade-up delay-3">
            <h3>Still have questions?</h3>
            <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 2rem;">Our support team is available 24/7 to assist you with any technical or medical queries.</p>
            <a href="mailto:support@vaxicare.com" class="btn-primary" style="padding: 1rem 2.5rem; font-size: 1.1rem; border-radius: 50px; box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);">Contact Support</a>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
