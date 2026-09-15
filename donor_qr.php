<?php
// donor_qr.php
session_start();
require_once __DIR__ . '/config/db.php';

$donor = null;

// 1. Check if user is logged in via email (from login-register.php)
if (isset($_SESSION['user_email'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $_SESSION['user_email']]);
    $donor = $stmt->fetch();
    if ($donor) {
        $_SESSION['user_id'] = $donor['id'];
    }
}

// 2. Fallback: Check if user_id is in session, or default to ID 1 for testing
if (!$donor && isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => (int)$_SESSION['user_id']]);
    $donor = $stmt->fetch();
}

// 3. Fallback dummy if database has no record yet
if (!$donor) {
    $donor = [
        'id'          => 1,
        'full_name'   => 'Senith Chethiya',
        'email'       => 'sc@gmail.com',
        'blood_group' => 'O+'
    ];
}

// Support both 'full_name' and 'name' column names
$donorDisplayName = !empty($donor['full_name']) ? $donor['full_name'] : (!empty($donor['name']) ? $donor['name'] : 'Donor');

// Generate the cryptographically signed token
$secureToken = generateDonorToken((int)$donor['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Donor Pass - BloodLink</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- QRCode.js Library CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        /* Base Layout for Header/Footer spacing */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .main-wrapper {
            flex: 1;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        /* --- Header & Navbar Styles from Camps --- */
        .header-section {
            width: 100%;
            border-bottom: 1px solid #e2e8f0;
            background: #ffffff;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .full-screen-container {
            width: 100%;
            max-width: 100% !important;
            padding-left: clamp(24px, 4.5vw, 70px);
            padding-right: clamp(24px, 4.5vw, 70px);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 18px;
            padding-bottom: 18px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .brand-logo-img {
            width: 45px;
            height: 45px;
            object-fit: contain;
            display: block;
        }

        .brand-logo-text {
            font-size: 22px;
            font-weight: 900;
            color: black;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 36px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-links a {
            text-decoration: none;
            color: #334155;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: #ef3446;
        }

        .scanner-btn-link {
            background: #fee2e2;
            color: #b91c1c !important;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600 !important;
            transition: all 0.2s;
        }
        
        .scanner-btn-link:hover {
            background: #fca5a5;
        }

        /* Mobile Hamburger Toggle */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 22px;
            color: #0f1115;
            cursor: pointer;
            padding: 6px;
        }

        /* Mobile Dropdown Drawer */
        .mobile-menu {
            transition: max-height 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            overflow: hidden;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
        }

        .menu-closed {
            max-height: 0;
            opacity: 0;
        }

        .menu-open {
            max-height: 380px;
            opacity: 1;
        }

        .mobile-nav {
            display: flex;
            flex-direction: column;
            padding: 16px clamp(24px, 4.5vw, 70px) 20px;
            gap: 12px;
        }

        .mobile-nav a {
            text-decoration: none;
            color: #334155;
            font-weight: 600;
            font-size: 15px;
            padding: 8px 0;
            transition: color 0.2s ease;
        }

        .mobile-nav a:hover {
            color: #ef3446;
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .mobile-toggle { display: block; }
        }

        /* --- Footer Styles from Camps --- */
        .footer-section {
            width: 100%;
            background-color: #000000;
            color: #a3a3a3;
            padding: 24px 0;
            border-top: 1px solid #1a1a1a;
            margin-top: auto;
        }

        .footer-content {
            text-align: center;
        }

        .footer-content p {
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0.3px;
            margin: 0;
        }

        /* --- QR Card Specific Styles --- */
        .qr-card-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 100%;
        }

        .qr-frame {
            padding: 16px;
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            margin: 20px 0;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        #qrcode img, #qrcode canvas {
            margin: 0 auto;
            display: block;
        }

        .donor-meta {
            margin-top: 15px;
            width: 100%;
            max-width: 360px;
            background-color: #f8fafc;
            border: 1px solid var(--border, #e2e8f0);
            border-radius: 10px;
            padding: 14px;
            text-align: left;
        }

        .donor-meta-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13.5px;
            border-bottom: 1px solid #edf2f7;
        }

        .donor-meta-row:last-child {
            border-bottom: none;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <header class="header-section">
        <div class="full-screen-container navbar">
            <a href="dashboard.php" class="brand-logo">
                <img src="images/bloodlink_logo.png" alt="BloodLink Logo" class="brand-logo-img" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2VmMzQ0NiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMTIgMi42OWw1LjY2IDUuNjZhOCA4IDAgMSAxLTExLjMxIDB6Ii8+PC9zdmc+'">
                <span class="brand-logo-text">BLOODLINK</span>
            </a>

            <!-- Desktop Navigation -->
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="personal-account.php"><i class="fa-solid fa-arrow-left"></i> Back to Account</a></li>
                <li><a href="history.php">My Donation History</a></li>
                
            </ul>

            <!-- Mobile Hamburger Button -->
            <button id="mobile-menu-toggle" class="mobile-toggle" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <!-- Mobile Dropdown Drawer -->
        <div id="mobile-menu" class="mobile-menu menu-closed">
            <nav class="mobile-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="personal-account.php"><i class="fa-solid fa-arrow-left"></i> Back to Account</a>
                <a href="history.php">My Donation History</a>
                <a href="scanner.php" target="_blank" style="color:#b91c1c; font-weight:700;">Hospital Scanner ↗</a>
            </nav>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="main-wrapper">
        <div class="container" style="max-width: 480px; width: 100%;">
            <div class="card qr-card-wrapper">
                <h1 class="card-title">Digital Donor Pass</h1>
                <p class="card-subtitle">Show this QR code at the intake desk of any blood donation drive.</p>

                <span class="blood-badge">Blood Group: <?= htmlspecialchars($donor['blood_group']); ?></span>

                <!-- Rendered QR Code Container -->
                <div class="qr-frame">
                    <div id="qrcode"></div>
                </div>

                <!-- Profile Info -->
                <div class="donor-meta">
                    <div class="donor-meta-row">
                        <span style="color: var(--text-muted, #64748b);">Donor Name:</span>
                        <strong><?= htmlspecialchars($donorDisplayName); ?></strong>
                    </div>
                    <div class="donor-meta-row">
                        <span style="color: var(--text-muted, #64748b);">Donor ID:</span>
                        <strong>#BL-<?= str_pad($donor['id'], 4, '0', STR_PAD_LEFT); ?></strong>
                    </div>
                    <div class="donor-meta-row">
                        <span style="color: var(--text-muted, #64748b);">Security:</span>
                        <strong style="color: var(--success, #10b981);">HMAC Anti-Spoof Active</strong>
                    </div>
                </div>

                <div class="actions">
                    <a href="personal-account.php" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Account
                    </a>
                    <button class="btn btn-secondary" onclick="window.location.reload();">
                        <i class="fa-solid fa-arrows-rotate"></i> Refresh
                    </button>
                    <button class="btn btn-primary" id="downloadQrBtn">
                        <i class="fa-solid fa-download"></i> Save QR
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-section">
        <div class="full-screen-container footer-content">
            <p>&copy; 2026 BloodLink. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle Logic
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

        // QR Code Generation
        const donorToken = "<?= $secureToken; ?>";
        const qrcodeContainer = document.getElementById("qrcode");
        
        new QRCode(qrcodeContainer, {
            text: donorToken,
            width: 220,
            height: 220,
            colorDark: "#0f172a",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Download functionality
        document.getElementById("downloadQrBtn").addEventListener("click", function () {
            const img = qrcodeContainer.querySelector("img");
            const canvas = qrcodeContainer.querySelector("canvas");
            let dataUrl = img && img.src ? img.src : (canvas ? canvas.toDataURL("image/png") : "");

            if (dataUrl) {
                const link = document.createElement("a");
                link.href = dataUrl;
                link.download = "BloodLink_Donor_QR_<?= $donor['id']; ?>.png";
                link.click();
            } else {
                alert("QR code is rendering. Please try again in a moment.");
            }
        });
    </script>
</body>
</html>