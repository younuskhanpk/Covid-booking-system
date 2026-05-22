<?php
// admin/vaccines.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/database.php';

$message = '';
$error = '';

// Handle Add Vaccine
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['vaccine_name']);
    if (!empty($name)) {
        $nameSafe = mysqli_real_escape_string($conn, $name);
        if (mysqli_query($conn, "INSERT INTO vaccines (vaccine_name) VALUES ('$nameSafe')")) {
            $message = "Vaccine '$name' added successfully to the global inventory.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } else {
        $error = "Vaccine name cannot be empty.";
    }
}

// Handle Status Toggle
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle') {
    $id = (int)$_POST['vaccine_id'];
    $current = $_POST['current_status'];
    $new_status = ($current === 'Available') ? 'Out of Stock' : 'Available';
    $statusSafe = mysqli_real_escape_string($conn, $new_status);
    
    if (mysqli_query($conn, "UPDATE vaccines SET availability_status = '$statusSafe' WHERE id = $id")) {
        $message = "Inventory status updated successfully.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle Delete Vaccine
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['vaccine_id'];
    if (mysqli_query($conn, "DELETE FROM vaccines WHERE id = $id")) {
        $message = "Vaccine removed from the system.";
    } else {
        $error = "Cannot delete vaccine. It might be linked to existing records.";
    }
}

// Fetch Vaccines
$vaccines = [];
$res = mysqli_query($conn, "SELECT * FROM vaccines ORDER BY id DESC");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $vaccines[] = $row;
    }
}

include '../includes/header.php';
?>

<style>
/* ==========================================================================
   PREMIUM VACCINE MANAGEMENT UI
   ========================================================================== */
.admin-dashboard-wrapper {
    font-family: var(--font-sans);
    background-color: #f8fafc;
    padding-bottom: 6rem;
    min-height: 100vh;
}

.vaccine-hero {
    background: linear-gradient(135deg, #1e1b4b, #312e81, #4338ca);
     animation: adminGradientPulse 15s ease infinite;
    padding: 6rem 2rem 5rem;
    color: white;
    text-align: center;
    border-radius: 0 0 var(--radius-xl) var(--radius-xl);
    margin-bottom: -4rem;
    position: relative;
    box-shadow: var(--shadow-lg);
}

.vaccine-hero::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
    background: url('data:image/svg+xml;utf8,<svg width="40" height="40" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="1.5" fill="rgba(255,255,255,0.05)"/></svg>') repeat;
}

.vaccine-hero h2 { font-size: 3.5rem; font-weight: 900; margin-bottom: 1rem; position: relative; z-index: 1;}
.vaccine-hero p { font-size: 1.2rem; color: #94a3b8; max-width: 600px; margin: 0 auto; position: relative; z-index: 1;}

.content-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    position: relative;
    z-index: 10;
}

.action-bar {
    display: flex; justify-content: flex-end; margin-bottom: 2rem;
}

.glass-panel {
    background: white;
    border-radius: var(--radius-xl);
    padding: 3rem;
    box-shadow: 0 20px 40px rgba(0,0,0,0.05);
    border: 1px solid var(--border);
}

/* Table Design */
.vax-table { width: 100%; border-collapse: separate; border-spacing: 0 12px; }
.vax-table th { color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem; border: none; padding: 1rem 1.5rem; text-align: left; }
.vax-table td { background: var(--bg-main); padding: 1.5rem; transition: all 0.3s; vertical-align: middle; }
.vax-table tr { transition: transform 0.3s, box-shadow 0.3s; }
.vax-table td:first-child { border-radius: var(--radius-md) 0 0 var(--radius-md); font-weight: bold; color: var(--text-muted); }
.vax-table td:last-child { border-radius: 0 var(--radius-md) var(--radius-md) 0; text-align: right; }

.vax-table tr:hover td { background: white; border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light); }
.vax-table tr:hover { transform: scale(1.01); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }

.vax-name { font-size: 1.2rem; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 1rem; }
.vax-icon { width: 40px; height: 40px; background: #e0f2fe; color: #0284c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

.status-badge { display: inline-block; padding: 0.5rem 1.2rem; border-radius: 50px; font-weight: 800; font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase; }
.status-avail { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
.status-out { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

.action-btns { display: flex; gap: 0.8rem; justify-content: flex-end; }
.btn-toggle { background: white; border: 2px solid var(--primary-light); color: var(--primary); font-weight: 700; padding: 0.6rem 1.2rem; border-radius: 8px; cursor: pointer; transition: all 0.3s; }
.btn-toggle:hover { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3); }

.btn-del { background: white; border: 2px solid #fecaca; color: #dc2626; font-weight: 700; padding: 0.6rem 1.2rem; border-radius: 8px; cursor: pointer; transition: all 0.3s; }
.btn-del:hover { background: #dc2626; color: white; border-color: #dc2626; box-shadow: 0 5px 15px rgba(220, 38, 38, 0.3); }

/* Add Modal Styles */
.vax-modal .modal-content { max-width: 500px; padding: 3rem; background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border-radius: var(--radius-xl); border: 1px solid var(--border); }
</style>

<div class="admin-dashboard-wrapper">
    <div class="vaccine-hero animate-fade-up">
        <h2>Vaccine Inventory</h2>
        <p>Manage the global database of available vaccines. Track inventory statuses and add new authorized vaccines into the VaxiCare network.</p>
    </div>
    
    <div class="content-container">
        
        <div class="action-bar animate-fade-up delay-1">
            <a href="#addVaccineModal" class="btn-primary" style="padding: 1rem 2rem; border-radius: 50px; font-size: 1.1rem; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);">+ Add New Vaccine</a>
        </div>
        
        <?php if($message): ?>
            <div class="alert alert-success animate-fade-up delay-1" style="background: var(--success-bg); color: var(--success); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; border: 1px solid rgba(16, 185, 129, 0.3); font-weight: 700;">
                <span style="font-size: 1.2rem; margin-right: 10px;">✅</span> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-error animate-fade-up delay-1" style="background: var(--danger-bg); color: var(--danger); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; border: 1px solid rgba(239, 68, 68, 0.3); font-weight: 700;">
                <span style="font-size: 1.2rem; margin-right: 10px;">⚠️</span> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="glass-panel animate-fade-up delay-2">
            <table class="vax-table">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="40%">Vaccine Details</th>
                        <th width="20%">Inventory Status</th>
                        <th width="30%" style="text-align: right;">Administrative Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($vaccines) > 0): ?>
                        <?php foreach($vaccines as $v): ?>
                            <tr>
                                <td>#<?php echo str_pad($v['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                <td>
                                    <div class="vax-name">
                                        <div class="vax-icon">💉</div>
                                        <?php echo htmlspecialchars($v['vaccine_name']); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($v['availability_status'] === 'Available'): ?>
                                        <span class="status-badge status-avail">Available</span>
                                    <?php else: ?>
                                        <span class="status-badge status-out">Out of Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <form method="post" style="margin:0;">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="vaccine_id" value="<?php echo $v['id']; ?>">
                                            <input type="hidden" name="current_status" value="<?php echo $v['availability_status']; ?>">
                                            <button type="submit" class="btn-toggle">Toggle Status</button>
                                        </form>
                                        
                                        <form method="post" style="margin:0;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="vaccine_id" value="<?php echo $v['id']; ?>">
                                            <button type="submit" class="btn-del">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 4rem;">
                                <div style="font-size: 4rem; opacity: 0.3; margin-bottom: 1rem;">📭</div>
                                <h3 style="color: var(--text-primary);">No Vaccines Found</h3>
                                <p style="color: var(--text-muted);">The inventory is currently empty. Add a new vaccine to start.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Vaccine Modal -->
<div id="addVaccineModal" class="modal vax-modal">
    <div class="modal-content">
        <a href="#" class="modal-close" style="background: var(--bg-main); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; top: -15px; right: -15px; box-shadow: var(--shadow-md);">&times;</a>
        <h3 style="font-size: 2rem; margin-bottom: 0.5rem; color: var(--primary-dark);">Add Vaccine</h3>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Register a new authorized vaccine to the system.</p>
        
        <form method="post">
            <input type="hidden" name="action" value="add">
            <div class="form-group" style="margin-bottom: 2rem;">
                <label class="form-label" for="vaccine_name" style="font-weight: 700;">Official Vaccine Name</label>
                <input type="text" name="vaccine_name" id="vaccine_name" class="form-control" required placeholder="e.g., Pfizer-BioNTech" style="padding: 1.2rem; border-radius: 12px; border: 2px solid var(--border);">
            </div>
            <button type="submit" class="btn-primary" style="width: 100%; padding: 1.2rem; font-size: 1.1rem; border-radius: 12px; box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);">Save to Inventory</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
