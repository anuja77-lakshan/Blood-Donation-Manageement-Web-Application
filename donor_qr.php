<?php
session_start();
require_once __DIR__ . '/config/db.php';

$donor = null;

// Check email
if (isset($_SESSION['user_email'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $_SESSION['user_email']]);
    $donor = $stmt->fetch();
    if ($donor) {
        $_SESSION['user_id'] = $donor['id'];
    }
}

// Check session
if (!$donor && isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => (int)$_SESSION['user_id']]);
    $donor = $stmt->fetch();
}

// Fallback user
if (!$donor) {
    $donor = [
        'id'          => 1,
        'full_name'   => 'Senith Chethiya',
        'email'       => 'sc@gmail.com',
        'blood_group' => 'O+'
    ];
}

// Set name
$donorDisplayName = !empty($donor['full_name']) ? $donor['full_name'] : (!empty($donor['name']) ? $donor['name'] : 'Donor');

// Generate token
$secureToken = generateDonorToken((int)$donor['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Donor Pass - BloodLink</title>
    <link rel="stylesheet" href="css/donor_qr.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- QRCode -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    
</head>
<body>

    <!-- Top navbar -->
    <header class="header-section">
        <div class="full-screen-container navbar">
            <a href="dashboard.php" class="brand-logo">
                <img src="images/bloodlink_logo.png" alt="BloodLink Logo" class="brand-logo-img" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2VmMzQ0NiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMTIgMi42l
                8LjY2IDUuNjZhOCA4IDAgMTAxMS4zMSAweiIvPjwvc3Zn+'">
                <span class="brand-logo-text">BLOODLINK</span>
            </a>

            <!-- Desktop nav -->
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="personal-account.php"><i class="fa-solid fa-arrow-left"></i> Back to Account</a></li>
                <li><a href="history.php">My Donation History</a></li>
            </ul>

            <!-- Mobile toggle -->
            <button id="mobile-menu-toggle" class="mobile-toggle" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <!-- Mobile drawer -->
        <div id="mobile-menu" class="mobile-menu menu-closed">
            <nav class="mobile-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="personal-account.php"><i class="fa-solid fa-arrow-left"></i> Back to Account</a>
                <a href="history.php">My Donation History</a>
                <a href="scanner.php" target="_blank" style="color:#b91c1c; font-weight:700;">Hospital Scanner ↗</a>
            </nav>
        </div>
    </header>

    <!-- Main content -->
    <main class="main-wrapper">
        <div class="container" style="max-width: 480px; width: 100%;">
            <div class="card qr-card-wrapper">
                <h1 class="card-title">Digital Donor Pass</h1>
                <p class="card-subtitle">Show this QR code at the intake desk of any blood donation drive.</p>

                <span class="blood-badge">Blood Group: <?= htmlspecialchars($donor['blood_group']); ?></span>

                <!-- QR container -->
                <div class="qr-frame">
                    <div id="qrcode"></div>
                </div>

                <!-- Donor info -->
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
        // Menu toggle
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

        // Generate QR
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

        // Download QR
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