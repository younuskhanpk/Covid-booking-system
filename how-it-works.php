<?php
// How It Works — full detailed guide for patients and hospitals
session_start();
require_once 'config/database.php';
require_once 'includes/svg_icons.php';
require_once 'includes/image_paths.php';
include 'includes/header.php';
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/landing-pages.css">

<style>
:root {
    --img-hero: url('<?php echo $img_base; ?>how-it-works-bg.jpg');
    --img-patient: url('<?php echo $img_base; ?>patient-care.jpg');
    --img-hospital: url('<?php echo $img_base; ?>hospital-building.jpg');
    --img-test: url('<?php echo $img_base; ?>covid-test.jpg');
    --img-vax: url('<?php echo $img_base; ?>vaccination.jpg');
    --img-clinic: url('<?php echo $img_base; ?>clinic-hall.jpg');
}

.hiw-hero {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
    padding: 8rem 2rem 6rem;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(79, 70, 229, 0.8)), var(--img-hero) center/cover no-repeat;
}

.hiw-hero h1 {
    font-size: 4rem;
    font-weight: 900;
    margin-bottom: 1.25rem;
    color: white;
    text-shadow: 0 8px 30px rgba(0,0,0,0.4);
}

.hiw-hero p {
    font-size: 1.35rem;
    max-width: 720px;
    margin: 0 auto 2.5rem;
    opacity: 0.95;
    line-height: 1.7;
}

.hiw-nav-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
}

.hiw-nav-pills a {
    padding: 0.85rem 1.75rem;
    background: rgba(255,255,255,0.15);
    border: 2px solid rgba(255,255,255,0.4);
    color: white;
    border-radius: 50px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s;
}

.hiw-nav-pills a:hover {
    background: white;
    color: var(--primary-dark);
}

.hiw-section-title {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 3rem;
    padding: 0 1rem;
}

.hiw-section-title h2 {
    font-size: 3rem;
    font-weight: 900;
    margin-bottom: 1rem;
}

.hiw-section-title p {
    font-size: 1.2rem;
    color: var(--text-secondary);
    line-height: 1.7;
}

.hiw-patient-band {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.15), rgba(79, 70, 229, 0.08));
    padding: 6rem 0;
}

.hiw-hospital-band {
    background: var(--img-hospital) center/cover;
    position: relative;
    padding: 6rem 2rem;
    margin: 4rem 0;
}

.hiw-hospital-band::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.92), rgba(30, 41, 59, 0.88));
}

.hiw-hospital-band .hiw-section-title,
.hiw-hospital-band .timeline-detailed {
    position: relative;
    z-index: 1;
}

.hiw-hospital-band .hiw-section-title h2,
.hiw-hospital-band .timeline-item h3 {
    color: white;
}

.hiw-hospital-band .hiw-section-title p,
.hiw-hospital-band .timeline-item p,
.hiw-hospital-band .timeline-item li {
    color: rgba(255,255,255,0.85);
}

.hiw-hospital-band .timeline-item {
    border-color: rgba(255,255,255,0.15);
}

.hiw-hospital-band .timeline-num {
    background: #34d399;
}

.hiw-compare {
    padding: 6rem 2rem;
    background: white;
}

.hiw-compare-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2.5rem;
    max-width: 1100px;
    margin: 0 auto;
}

.hiw-compare-card {
    padding: 2.5rem;
    border-radius: 20px;
    border: 2px solid var(--border);
}

.hiw-compare-card.patient-side {
    background: linear-gradient(180deg, #eff6ff, #fff);
    border-color: #93c5fd;
}

.hiw-compare-card.hospital-side {
    background: linear-gradient(180deg, #ecfdf5, #fff);
    border-color: #6ee7b7;
}

.hiw-faq-strip {
    padding: 5rem 2rem;
    background: var(--img-clinic) center/cover;
    position: relative;
}

.hiw-faq-strip::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,0.94);
}

.hiw-faq-strip .bg-section-inner {
    position: relative;
    z-index: 1;
    text-align: center;
}

@media (max-width: 768px) {
    .hiw-hero h1 { font-size: 2.5rem; }
    .hiw-compare-grid { grid-template-columns: 1fr; }
}
</style>

<div class="main-landing">

    <section class="hiw-hero reveal-section">
        <div>
            <p style="letter-spacing:3px;font-weight:800;opacity:0.9;margin-bottom:1rem;">VAXICARE GUIDE</p>
            <h1>How It Works</h1>
            <p>A complete walkthrough for patients booking care and hospitals managing COVID-19 tests, vaccinations, and digital records — simple steps from registration to results.</p>
            <div class="hiw-nav-pills">
                <a href="#for-patients">For Patients</a>
                <a href="#for-hospitals">For Hospitals</a>
                <a href="#booking-types">Tests & Vaccines</a>
                <a href="auth/register.php" class="btn-primary" style="border:none;">Get Started</a>
            </div>
        </div>
    </section>

    <!-- PATIENTS -->
    <section id="for-patients" class="hiw-patient-band">
        <div class="hiw-section-title reveal-section">
            <h2>Patient journey — start to finish</h2>
            <p>From creating your account to viewing test results online. Each step is designed to be simple on phone or computer.</p>
        </div>

        <div class="split-block reveal-section">
            <div>
                <h2 style="font-size:2.5rem;font-weight:900;margin-bottom:1rem;">Register & secure your profile</h2>
                <p style="font-size:1.1rem;color:var(--text-secondary);line-height:1.8;margin-bottom:1.5rem;">Sign up with your name, email, phone, and address. Your password is stored safely. Only you and authorized hospital staff can see your booking history.</p>
                <ul style="line-height:2;color:var(--text-secondary);">
                    <li>One account for all future bookings</li>
                    <li>Update profile anytime from dashboard</li>
                    <li>Logout keeps your session private</li>
                </ul>
                <a href="auth/register.php" class="btn-primary" style="margin-top:1.5rem;display:inline-block;">Register as patient</a>
            </div>
            <img src="<?php echo $img_base; ?>patient-care.jpg" alt="Patient using health portal">
        </div>

        <div class="timeline-detailed reveal-section">
            <div class="timeline-item">
                <div class="timeline-num">1</div>
                <div>
                    <h3>Search approved hospitals</h3>
                    <p>Use <strong>Find hospitals</strong> after login. Only facilities approved by the admin appear in search. You can see hospital name, location, and services before booking.</p>
                    <ul>
                        <li>Filter by name or area</li>
                        <li>Compare multiple facilities</li>
                        <li>No walk-in guessing — book a confirmed slot</li>
                    </ul>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-num">2</div>
                <div>
                    <h3>Choose test or vaccination</h3>
                    <p>On the booking page, select <strong>COVID-19 Test</strong> or <strong>Vaccination</strong>. For vaccination, pick an available vaccine from the hospital list. Choose a date that is today or in the future.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-num">3</div>
                <div>
                    <h3>Wait for hospital approval</h3>
                    <p>Your booking starts as <strong>Pending</strong>. The hospital reviews the request and changes status to <strong>Approved</strong>. You see status updates on your patient dashboard.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-num">4</div>
                <div>
                    <h3>Visit on appointment day</h3>
                    <p>Go to the hospital on your booked date with ID. Staff already have your booking in their system — less waiting and paperwork.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-num">5</div>
                <div>
                    <h3>View results online</h3>
                    <p>After your visit, the hospital marks the appointment <strong>Completed</strong> and enters test result (Positive/Negative) or vaccination status. You see this on <strong>My Medical History</strong> immediately.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- BOOKING TYPES -->
    <section id="booking-types" class="bg-section-light reveal-section" style="background-image: var(--img-test);">
        <div class="bg-section-inner">
            <div class="hiw-section-title">
                <h2>Two types of care</h2>
                <p>Everything you book fits into testing or vaccination — both tracked digitally.</p>
            </div>
            <div class="photo-grid-3">
                <div class="card-with-photo">
                    <img src="<?php echo $img_base; ?>covid-test.jpg" alt="COVID test">
                    <div class="card-body">
                        <h3>COVID-19 diagnostic test</h3>
                        <p>Book a test slot. After the lab processes your sample, the hospital records Negative or Positive. Result shows on your dashboard with optional notes from staff.</p>
                    </div>
                </div>
                <div class="card-with-photo">
                    <img src="<?php echo $img_base; ?>vaccination.jpg" alt="Vaccination">
                    <div class="card-body">
                        <h3>Vaccination appointment</h3>
                        <p>Select a vaccine (Pfizer, Moderna, etc. if offered). After the dose, staff update vaccination status — Completed, Scheduled for 2nd dose, and similar.</p>
                    </div>
                </div>
                <div class="card-with-photo">
                    <img src="<?php echo $img_base; ?>medical-tech.jpg" alt="Digital records">
                    <div class="card-body">
                        <h3>Digital health record</h3>
                        <p>All visits stay in one timeline: hospital name, date, type, status, and official results — no lost paper slips.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOSPITALS -->
    <section id="for-hospitals" class="hiw-hospital-band reveal-section">
        <div class="hiw-section-title">
            <h2>Hospital & clinic workflow</h2>
            <p>How healthcare providers join VaxiCare, accept bookings, and update patient records.</p>
        </div>
        <div class="timeline-detailed" style="max-width:1000px;">
            <div class="timeline-item">
                <div class="timeline-num">1</div>
                <div>
                    <h3>Register your facility</h3>
                    <p>Sign up as Hospital with license number, location, and contact details. Status stays <strong>Pending</strong> until admin approves.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-num">2</div>
                <div>
                    <h3>Admin verification</h3>
                    <p>Platform admin reviews your license and approves the account. Only then patients can find and book your hospital.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-num">3</div>
                <div>
                    <h3>Manage appointments</h3>
                    <p>Open <strong>Appointments</strong> to approve or reject pending requests. Approved visits appear in your daily schedule.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-num">4</div>
                <div>
                    <h3>File results</h3>
                    <p>After care, use <strong>Results</strong> to enter test outcomes or vaccination status. Patients are notified through their dashboard.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-num">5</div>
                <div>
                    <h3>Vaccines & services</h3>
                    <p>Add vaccines your site offers. Manage inventory visibility so patients know what is available when booking.</p>
                </div>
            </div>
        </div>
        <p style="text-align:center;margin-top:3rem;position:relative;z-index:1;">
            <a href="auth/register.php" class="btn-primary" style="padding:1rem 2.5rem;">Register your hospital</a>
        </p>
    </section>

    <!-- COMPARE -->
    <section class="hiw-compare reveal-section">
        <div class="hiw-section-title">
            <h2>Patient vs hospital — who does what?</h2>
        </div>
        <div class="hiw-compare-grid">
            <div class="hiw-compare-card patient-side">
                <h3 style="font-size:1.5rem;margin-bottom:1.5rem;">Patient responsibilities</h3>
                <ul style="line-height:2;color:var(--text-secondary);">
                    <li>Create account & keep profile updated</li>
                    <li>Search and book at approved hospitals</li>
                    <li>Attend on scheduled date</li>
                    <li>Check dashboard for results</li>
                </ul>
            </div>
            <div class="hiw-compare-card hospital-side">
                <h3 style="font-size:1.5rem;margin-bottom:1.5rem;">Hospital responsibilities</h3>
                <ul style="line-height:2;color:var(--text-secondary);">
                    <li>Register with valid license info</li>
                    <li>Approve or reject booking requests</li>
                    <li>Deliver test or vaccination service</li>
                    <li>Update results in the system promptly</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="split-block reverse reveal-section" style="background:var(--bg-main);">
        <img src="<?php echo $img_base; ?>hospital-building.jpg" alt="Hospital facility">
        <div>
            <h2 style="font-size:2.5rem;font-weight:900;">Ready to begin?</h2>
            <p style="font-size:1.15rem;color:var(--text-secondary);line-height:1.8;">Join thousands using digital booking instead of queues and paper forms.</p>
            <div style="display:flex;flex-wrap:wrap;gap:1rem;margin-top:2rem;">
                <a href="auth/register.php" class="btn-primary">Create account</a>
                <a href="auth/login.php" class="btn-outline">Log in</a>
                <a href="index.php" class="btn-outline">Back to home</a>
            </div>
        </div>
    </section>

</div>

<?php include 'includes/footer.php'; ?>
