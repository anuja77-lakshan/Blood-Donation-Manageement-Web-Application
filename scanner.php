<?php
// scanner.php - Unauthenticated Prototype for Hospital / Camp intake desk
require_once __DIR__ . '/config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital QR Scanner - BloodLink</title>
    <link rel="stylesheet" href="css/style.css">

    <!-- HTML5-QRCode Library CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

    <style>
        .scanner-container {
            max-width: 620px;
            margin: 20px auto;
        }

        #reader {
            width: 100%;
            border-radius: var(--radius);
            overflow: hidden;
            border: 2px solid var(--border);
            background: #0f172a;
        }

        .camp-selector {
            margin-bottom: 18px;
            text-align: left;
        }

        .camp-selector label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .camp-selector select {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 14px;
            background: #fff;
        }

        .controls {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            justify-content: center;
        }

        .status-box {
            margin-top: 18px;
            display: none;
        }

        .donor-summary-card {
            background: #ffffff;
            border: 1px solid #bbf7d0;
            background-color: #f0fdf4;
            border-radius: 8px;
            padding: 16px;
            margin-top: 15px;
            display: none;
            text-align: left;
        }

        .security-warning {
            background-color: #fffbeb; 
            border-left: 4px solid #f59e0b; 
            padding: 12px; 
            margin-bottom: 20px; 
            font-size: 13px; 
            text-align: left; 
            border-radius: 4px; 
            color: #92400e;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="scanner.php" class="brand" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
            <img src="images/bloodlink_logo.png" alt="BloodLink Logo" style="width: 35px; height: 35px; object-fit: contain;">
            <span style="color: black; font-weight: 900;">BLOODLINK</span> <span style="font-weight: 500;">Hospital Intake Scanner(ADMIN)</span>
        </a>
        <div class="nav-links">
            <a href="donor_qr.php" target="_blank">Donor QR View ↗</a>
        </div>
    </nav>

    <div class="container scanner-container">
        <div class="card" style="text-align: center;">
            
            <!-- Security Caution Banner -->
            <div class="security-warning">
                <strong>⚠️ Security Notice:</strong> This terminal connects directly to the live database. Do strictly ensure that you do not share the admin page link, scanner terminal URLs, or login credentials with unauthorized personnel.
            </div>

            <h1 class="card-title">Hospital Scanner Terminal</h1>
            <p class="card-subtitle">Scan a donor's QR code to record their blood donation directly into the database.</p>

            <!-- Simplified Static Selector -->
            <div class="camp-selector">
                <label for="campSelect">Active Intake Location:</label>
                <select id="campSelect">
                    <option value="" disabled selected>-- Select an Intake Location --</option>
                    <option value='{"camp":"Hospital","location":"Main Hospital Intake"}'>Hospital</option>
                    <option value='{"camp":"Blood Donation Camp","location":"Mobile Donation Camp"}'>Blood Donation Camp</option>
                </select>
            </div>

            <!-- HTML5 Video Viewport -->
            <div id="reader"></div>

            <div class="controls">
                <button id="btnStart" class="btn btn-primary">📷 Start Camera</button>
                <button id="btnStop" class="btn btn-secondary" disabled>⏹ Stop Camera</button>
            </div>

            <!-- Dynamic Scan Status Message -->
            <div id="statusAlert" class="alert status-box"></div>

            <!-- Result Box -->
            <div id="donorSummary" class="donor-summary-card">
                <h3 style="color: var(--success); margin-bottom: 8px;">✅ Donation Successfully Saved</h3>
                <p><strong>Donor Name:</strong> <span id="resDonorName">-</span></p>
                <p><strong>Blood Group:</strong> <span id="resBloodGroup" class="blood-badge" style="font-size:12px;">-</span></p>
                <p><strong>Location:</strong> <span id="resLocation">-</span></p>
                <p><strong>Intake Type:</strong> <span id="resCamp">-</span></p>
                <p><strong>Date:</strong> <span id="resDate">-</span></p>
            </div>
        </div>
    </div>

    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const btnStart = document.getElementById("btnStart");
        const btnStop = document.getElementById("btnStop");
        const statusAlert = document.getElementById("statusAlert");
        const donorSummary = document.getElementById("donorSummary");
        const campSelect = document.getElementById("campSelect");

        let isScanning = false;
        let isCooldown = false; // Cooldown lock

        // Synthesize an audio confirmation beep using Web Audio API
        function playBeep() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = "sine";
                osc.frequency.setValueAtTime(880, audioCtx.currentTime); // 880Hz beep
                gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.15);
            } catch (e) {
                console.warn(e);
            }
        }

        // Camera QR scan callback
        function onScanSuccess(decodedText, decodedResult) {
            if (isCooldown) return; // Prevent spamming duplicate scans
            
            // Check if a location is actually selected
            if (!campSelect.value) {
                showStatus("alert-danger", "Please select an Intake Location (Hospital or Camp) before scanning.");
                return;
            }

            isCooldown = true;
            playBeep();

            showStatus("alert-success", "QR Code detected! Sending to server...");

            // Parse selected camp & location
            let selectedCampInfo = null;
            try {
                selectedCampInfo = JSON.parse(campSelect.value);
            } catch (e) {
                showStatus("alert-danger", "Error reading location selection.");
                isCooldown = false;
                return;
            }

            // Send AJAX POST to process_scan.php
            fetch("process_scan.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify({ 
                    token: decodedText,
                    camp_name: selectedCampInfo.camp,
                    location: selectedCampInfo.location
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    showStatus("alert-success", data.message);

                    document.getElementById("resDonorName").textContent = data.donor.full_name;
                    document.getElementById("resBloodGroup").textContent = data.donor.blood_group;
                    document.getElementById("resLocation").textContent = data.donation.location;
                    document.getElementById("resCamp").textContent = data.donation.camp_name;
                    document.getElementById("resDate").textContent = data.donation.donation_date;
                    donorSummary.style.display = "block";
                } else {
                    showStatus("alert-danger", "REJECTED: " + (data.message || "Failed to process scan."));
                    donorSummary.style.display = "none";
                }
            })
            .catch(err => {
                console.error(err);
                showStatus("alert-danger", "Network error or invalid server response.");
                donorSummary.style.display = "none";
            })
            .finally(() => {
                // Re-enable camera scanning after 4 seconds
                setTimeout(() => {
                    isCooldown = false;
                }, 4000);
            });
        }

        function showStatus(typeClass, message) {
            statusAlert.className = "alert status-box " + typeClass;
            statusAlert.textContent = message;
            statusAlert.style.display = "block";
        }

        btnStart.addEventListener("click", () => {
            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess
            ).then(() => {
                isScanning = true;
                btnStart.disabled = true;
                btnStop.disabled = false;
                showStatus("alert-success", "Camera active. Hold donor QR code up to lens.");
            }).catch(err => {
                showStatus("alert-danger", "Camera error: " + err);
            });
        });

        btnStop.addEventListener("click", () => {
            if (!isScanning) return;
            html5QrCode.stop().then(() => {
                isScanning = false;
                btnStart.disabled = false;
                btnStop.disabled = true;
                statusAlert.style.display = "none";
            });
        });
    </script>
</body>
</html>