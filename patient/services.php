<?php
// patient/services.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: ../auth/login.php");
    exit;
}

include '../includes/header.php';
?>

<style>
/* ==========================================================================
   PATIENT DASHBOARD SERVICES UI
   ========================================================================== */
.patient-services-wrapper {
    font-family: var(--font-sans);
    background-color: #f8fafc;
    padding-bottom: 6rem;
    min-height: 100vh;
}

.services-hero {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    padding: 6rem 2rem 5rem;
    color: white;
    text-align: center;
    border-radius: 0 0 var(--radius-xl) var(--radius-xl);
    margin-bottom: -4rem;
    position: relative;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}

.services-hero::before {
    content: ''; position: absolute; inset: 0;
    background: url('data:image/svg+xml;utf8,<svg width="40" height="40" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="1.5" fill="rgba(255,255,255,0.15)"/></svg>') repeat;
    opacity: 0.5;
}

.services-hero h2 { font-size: 3.5rem; font-weight: 900; margin-bottom: 1rem; position: relative; z-index: 1; text-shadow: 0 4px 10px rgba(0,0,0,0.2);}
.services-hero p { font-size: 1.2rem; color: #e0f2fe; max-width: 600px; margin: 0 auto; position: relative; z-index: 1;}

.content-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    position: relative;
    z-index: 10;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 3rem;
}

.service-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 3rem;
    box-shadow: 0 20px 40px rgba(0,0,0,0.06);
    transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid var(--border);
    text-align: center;
    display: flex; flex-direction: column; height: 100%;
}

.service-card:hover {
    transform: translateY(-15px);
    box-shadow: 0 30px 60px rgba(14, 165, 233, 0.15);
    border-color: var(--primary-light);
}

.s-icon {
    width: 80px; height: 80px; margin: 0 auto 2rem;
    border-radius: 50%;
    background: linear-gradient(135deg, #e0f2fe, #bae6fd);
    color: #0284c7; display: flex; align-items: center; justify-content: center;
    font-size: 2.5rem; transition: transform 0.4s;
    box-shadow: 0 10px 20px rgba(2, 132, 199, 0.1);
}

.service-card:hover .s-icon {
    transform: scale(1.1) rotate(10deg);
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: white;
}

.service-card h3 { font-size: 1.8rem; margin-bottom: 1rem; color: var(--text-primary); font-weight: 800; }
.service-card p { font-size: 1.1rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 2.5rem; flex-grow: 1; }

.service-btn {
    display: inline-block; padding: 1rem 2rem; width: 100%;
    background: var(--bg-main); color: var(--primary); font-weight: 700;
    border-radius: 50px; text-decoration: none; transition: all 0.3s;
    border: 2px solid transparent;
}

.service-card:hover .service-btn {
    background: var(--primary); color: white; box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
}

</style>

<div class="patient-services-wrapper">
    <div class="services-hero animate-fade-up">
        <h2>Your Medical Services</h2>
        <p>Access all your personalized healthcare services in one place. Book tests, manage vaccinations, and download your official health certificates securely.</p>
    </div>
    
    <div class="content-container">
        <div class="services-grid">
            
            <div class="service-card animate-fade-up delay-1">
                <div class="s-icon">🏥</div>
                <h3>Book Medical Appointment</h3>
                <p>Search for verified hospitals near you and securely book a time slot for COVID-19 testing or vaccination.</p>
                <a href="search.php" class="service-btn">Find Hospitals</a>
            </div>
            
            <div class="service-card animate-fade-up delay-2">
                <div class="s-icon">📄</div>
                <h3>Diagnostic Reports</h3>
                <p>Access your past and recent COVID-19 test results. View detailed digital reports updated directly by the medical facility.</p>
                <a href="index.php" class="service-btn">View Reports</a>
            </div>
            
            <div class="service-card animate-fade-up delay-3">
                <div class="s-icon">💉</div>
                <h3>Vaccination Certificates</h3>
                <p>Download your globally recognized, digitally signed COVID-19 vaccination certificates for travel and official use.</p>
                <a href="index.php" class="service-btn">Download Certificate</a>
            </div>

            <div class="service-card animate-fade-up delay-1">
                <div class="s-icon">📞</div>
                <h3>Priority Support</h3>
                <p>Get instant assistance from our 24/7 dedicated support team regarding any booking issues or platform queries.</p>
                <a href="../faq.php" class="service-btn">Get Help</a>
            </div>
            
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
