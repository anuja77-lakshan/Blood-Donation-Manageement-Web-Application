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
        .qr-card-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
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
            border: 1px solid var(--border);
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

    <nav class="navbar">
        <a href="personal-account.php" class="brand">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
            BloodLink
        </a>
        <div class="nav-links">
            <a href="personal-account.php"><i class="fa-solid fa-arrow-left"></i> Back to Account</a>
            <a href="history.php">My Donation History</a>
            <a href="scanner.php" target="_blank" style="background:#fee2e2; color:#b91c1c;">Hospital Scanner ↗</a>
        </div>
    </nav>

    <div class="container" style="max-width: 480px;">
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
                    <span style="color: var(--text-muted);">Donor Name:</span>
                    <strong><?= htmlspecialchars($donorDisplayName); ?></strong>
                </div>
                <div class="donor-meta-row">
                    <span style="color: var(--text-muted);">Donor ID:</span>
                    <strong>#BL-<?= str_pad($donor['id'], 4, '0', STR_PAD_LEFT); ?></strong>
                </div>
                <div class="donor-meta-row">
                    <span style="color: var(--text-muted);">Security:</span>
                    <strong style="color: var(--success);">HMAC Anti-Spoof Active</strong>
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

    <script>
        const donorToken = "<?= $secureToken; ?>";

        // Initialize QRCode.js
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