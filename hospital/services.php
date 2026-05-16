<?php
// hospital/services.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Hospital') {
    header("Location: ../auth/login.php");
    exit;
}

include '../includes/header.php';
?>

<style>
/* ==========================================================================
   HOSPITAL DASHBOARD SERVICES UI
   ========================================================================== */
.hospital-services-wrapper {
    font-family: var(--font-sans);
    background-color: #f8fafc;
    padding-bottom: 6rem;
    min-height: 100vh;
}

.services-hero {
    background: linear-gradient(135deg, #1e1b4b, #4338ca);
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
    background: url('data:image/svg+xml;utf8,<svg width="40" height="40" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></svg>') repeat;
}

.services-hero h2 { font-size: 3.5rem; font-weight: 900; margin-bottom: 1rem; position: relative; z-index: 1; text-shadow: 0 4px 10px rgba(0,0,0,0.2);}
.services-hero p { font-size: 1.2rem; color: #c7d2fe; max-width: 600px; margin: 0 auto; position: relative; z-index: 1;}

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
    box-shadow: 0 30px 60px rgba(67, 56, 202, 0.15);
    border-color: #a5b4fc;
}

.s-icon {
    width: 80px; height: 80px; margin: 0 auto 2rem;
    border-radius: 20px;
    background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
    color: #4338ca; display: flex; align-items: center; justify-content: center;
    font-size: 2.5rem; transition: transform 0.4s;
    box-shadow: 0 10px 20px rgba(67, 56, 202, 0.1);
}

.service-card:hover .s-icon {
    transform: scale(1.1) rotate(-10deg);
    background: linear-gradient(135deg, #4338ca, #312e81);
    color: white;
}

.service-card h3 { font-size: 1.8rem; margin-bottom: 1rem; color: var(--text-primary); font-weight: 800; }
.service-card p { font-size: 1.1rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 2.5rem; flex-grow: 1; }

.service-btn {
    display: inline-block; padding: 1rem 2rem; width: 100%;
    background: var(--bg-main); color: #4338ca; font-weight: 700;
    border-radius: 50px; text-decoration: none; transition: all 0.3s;
    border: 2px solid transparent;
}

.service-card:hover .service-btn {
    background: #4338ca; color: white; box-shadow: 0 10px 20px rgba(67, 56, 202, 0.3);
}

</style>

<div class="hospital-services-wrapper">
    <div class="services-hero animate-fade-up">
        <h2>Facility Services Hub</h2>
        <p>Manage your daily operations effortlessly. From accepting patient appointments to generating digital health reports, everything is streamlined.</p>
    </div>
    
    <div class="content-container">
        <div class="services-grid">
            
            <div class="service-card animate-fade-up delay-1">
                <div class="s-icon">📅</div>
                <h3>Patient Appointments</h3>
                <p>View, accept, or manage incoming COVID-19 testing and vaccination requests from registered patients in your area.</p>
                <a href="appointments.php" class="service-btn">Manage Bookings</a>
            </div>
            
            <div class="service-card animate-fade-up delay-2">
                <div class="s-icon">📝</div>
                <h3>Update Health Records</h3>
                <p>Digitally input patient test results (Positive/Negative) or update vaccination statuses. Instantly generate digital certificates.</p>
                <a href="appointments.php" class="service-btn">Update Records</a>
            </div>
            
            <div class="service-card animate-fade-up delay-3">
                <div class="s-icon">📊</div>
                <h3>Facility Analytics</h3>
                <p>Monitor your hospital's performance, track the total number of patients served, and oversee your facility's operational impact.</p>
                <a href="index.php" class="service-btn">View Dashboard</a>
            </div>

            <div class="service-card animate-fade-up delay-1">
                <div class="s-icon">🛡️</div>
                <h3>Staff & Security</h3>
                <p>Ensure that all operations within your facility meet the platform's stringent security and data privacy standards.</p>
                <a href="../privacy.php" class="service-btn">Security Guidelines</a>
            </div>
            
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
