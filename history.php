<?php
// history.php
session_start();
require_once __DIR__ . '/config/db.php';

// 1. Check if user is logged in via email (Matches logic in donor_qr.php)
if (isset($_SESSION['user_email'])) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $_SESSION['user_email']]);
    $loggedUser = $stmt->fetch();
    if ($loggedUser) {
        $_SESSION['user_id'] = $loggedUser['id'];
    }
}

// 2. Fallback to donor 1 if no one is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$userId = (int)$_SESSION['user_id'];

// 3. Fetch user info 
$stmtUser = $pdo->prepare("SELECT id, full_name, blood_group FROM users WHERE id = :id");
$stmtUser->execute(['id' => $userId]);
$donor = $stmtUser->fetch();

if (!$donor) {
    $donor = ['id' => 1, 'full_name' => 'Senith Chethiya', 'blood_group' => 'O+'];
}

// Extract first name for the navbar greeting 
$firstName = explode(' ', trim($donor['full_name']))[0];

// 4. Fetch past donations from your existing donations table
$sql = "SELECT id, user_id, donation_date, location, camp_name, status 
        FROM donations 
        WHERE user_id = :user_id 
        ORDER BY donation_date DESC, id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $userId]);
$donations = $stmt->fetchAll();

// 5. Analytics
$totalDonations = count($donations);
$lastDonationDate = $totalDonations > 0 ? $donations[0]['donation_date'] : null;

// NBTS Sri Lanka 120-day interval calculation
$nextEligibleDate = null;
$isEligible = true;
if ($lastDonationDate) {
    $lastTime = strtotime($lastDonationDate);
    $eligibleTime = strtotime("+120 days", $lastTime);
    $nextEligibleDate = date('M d, Y', $eligibleTime);
    if (time() < $eligibleTime) {
        $isEligible = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History - BloodLink</title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- CSS Links -->
    <link rel="stylesheet" href="css/camps.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-box {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: 16px;
            padding: 18px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        }

        .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: var(--primary);
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }
    </style>
</head>
<body>

    <!-- Top Full Width Navbar -->
    <header class="header-section">
        <div class="full-screen-container navbar">
            <a href="home.php" class="brand-logo">
                <img src="images/bloodlink_logo.png" alt="BloodLink Logo" class="brand-logo-img">
                <span class="brand-logo-text">BLOODLINK</span>
            </a>

            <!-- Desktop Navigation -->
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="camps.php">Camps</a></li>
                <li><a href="donor_qr.php">My QR Code</a></li>
                <li><a href="history.php" class="active">History</a></li>
                <li>
                    <a href="personal-account.php" class="user-greeting">
                        <i class="fa-regular fa-circle-user"></i>
                        <span>Hi, <?= htmlspecialchars($firstName); ?></span>
                    </a>
                </li>
            </ul>

            <!-- Mobile Hamburger Button -->
            <button id="mobile-menu-toggle" class="mobile-toggle" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <!-- Mobile Dropdown Drawer -->
        <div id="mobile-menu" class="mobile-menu menu-closed">
            <nav class="mobile-nav">
                <a href="home.php">Home</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="camps.php">Camps</a>
                <a href="donor_qr.php">My QR Code</a>
                <a href="history.php" class="active">History</a>
                <a href="personal-account.php" class="mobile-user-link">
                    <i class="fa-regular fa-circle-user"></i>
                    <span>Hi, <?= htmlspecialchars($firstName); ?></span>
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Full Screen Content -->
    <main class="full-screen-container main-content">
        <div class="page-title-row" style="margin-bottom: 20px;">
            <div class="title-group">
                <div class="section-tag hero-badge-animate">Donor Profile</div>
                <h2 class="main-heading hero-title-animate">Hello, <?= htmlspecialchars($donor['full_name']); ?> 👋</h2>
                <p class="sub-text hero-desc-animate">Here is your verified blood donation record.</p>
            </div>
            <span class="blood-badge hero-actions-animate" style="background: var(--primary); color: white; padding: 10px 20px; border-radius: 30px; font-weight: 700;">
                Blood Group: <?= htmlspecialchars($donor['blood_group']); ?>
            </span>
        </div>

        <!-- KPI summary cards -->
        <div class="stats-grid reveal-on-scroll is-visible">
            <div class="stat-box">
                <div class="stat-value"><?= $totalDonations; ?></div>
                <div class="stat-label">Total Donations</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="font-size: 18px; line-height: 2;">
                    <?= $lastDonationDate ? date('M d, Y', strtotime($lastDonationDate)) : 'None Yet'; ?>
                </div>
                <div class="stat-label">Last Donation Date</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="font-size: 18px; line-height: 2; color: <?= $isEligible ? '#10b981' : 'var(--text-dark)'; ?>;">
                    <?= $nextEligibleDate ? ($isEligible ? 'Eligible Now' : $nextEligibleDate) : 'Eligible Now'; ?>
                </div>
                <div class="stat-label">Eligibility Status</div>
            </div>
        </div>

        <!-- History Table -->
        <div class="card reveal-on-scroll is-visible" style="border: 1px solid var(--border-light); border-radius: 16px; padding: 24px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);">
            <h3 class="card-title" style="margin-bottom: 8px;">Completed Blood Donations</h3>
            <p class="card-subtitle" style="color: var(--text-muted); margin-bottom: 20px; font-size: 14px;">Verified records from blood banks and mobile camps.</p>

            <div class="table-container" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-light);">
                            <th style="padding: 12px;"># Reference</th>
                            <th style="padding: 12px;">Donation Date</th>
                            <th style="padding: 12px;">Location</th>
                            <th style="padding: 12px;">Camp / Hospital Name</th>
                            <th style="padding: 12px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donations as $row): ?>
                            <tr style="border-bottom: 1px solid var(--border-light);">
                                <td style="padding: 12px;"><strong>#DON-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                                <td style="padding: 12px;"><?= date('F j, Y', strtotime($row['donation_date'])); ?></td>
                                <td style="padding: 12px;"><?= htmlspecialchars($row['location']); ?></td>
                                <td style="padding: 12px;"><?= htmlspecialchars($row['camp_name']); ?></td>
                                <td style="padding: 12px;">
                                    <span style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                                        <?= htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-section">
        <div class="full-screen-container footer-content">
            <p>&copy; 2026 BloodLink. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuToggle = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');

            if (menuToggle && mobileMenu) {
                menuToggle.addEventListener('click', () => {
                    const isOpen = mobileMenu.classList.toggle('menu-open');
                    mobileMenu.classList.toggle('menu-closed', !isOpen);
                    
                    const icon = menuToggle.querySelector('i');
                    if (isOpen) {
                        icon.classList.replace('fa-bars', 'fa-xmark');
                    } else {
                        icon.classList.replace('fa-xmark', 'fa-bars');
                    }
                });
            }
        });
    </script>
</body>
</html>