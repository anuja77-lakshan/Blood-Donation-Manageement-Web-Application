<?php
session_start();
require_once 'php/db.php';

// User login name
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Donor';

// Visitor Tracking in Page 
$conn->query("CREATE TABLE IF NOT EXISTS site_visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(100) NOT NULL,
    visit_date DATE NOT NULL,
    visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_visit (ip_address, visit_date)
)");

$visitor_ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
$today_date = date('Y-m-d');

// Record current visit
$conn->query("INSERT IGNORE INTO site_visitors (ip_address, visit_date) VALUES ('$visitor_ip', '$today_date')");

// Today's count
$todayVisitors = 1;
$resVisitors = $conn->query("SELECT COUNT(*) as total FROM site_visitors WHERE visit_date = '$today_date'");
if ($resVisitors && $row = $resVisitors->fetch_assoc()) {
    $todayVisitors = max(1, (int)$row['total']);
}

// Registered Donors Count
$donorCount = 0;
$resUsers = $conn->query("SELECT COUNT(*) as total FROM users");
if ($resUsers && $row = $resUsers->fetch_assoc()) {
    $donorCount = $row['total'];
}

// Today Registered Donors Count
$todayDonors = 0;
$resToday = $conn->query("SELECT COUNT(*) as today_total FROM users WHERE DATE(created_at) = CURDATE()");
if ($resToday && $row = $resToday->fetch_assoc()) {
    $todayDonors = $row['today_total'];
}

// Active Camps 
$campCount = 0;
$resCamps = $conn->query("SELECT COUNT(*) as total FROM blood_camps");
if ($resCamps && $row = $resCamps->fetch_assoc()) {
    $campCount = $row['total'];
}

// Emergency Requests Count
$requestCount = 0;
$resRequests = $conn->query("SELECT COUNT(*) as total FROM emergency_requests");
if ($resRequests && $row = $resRequests->fetch_assoc()) {
    $requestCount = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BloodLink - Dashboard</title>
    
    <!--Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- CSS File Set-->
    <link rel="stylesheet" href="css/dashboard.css">
    <!-- Font-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet.js and Map CSS Code -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>

  <!-- Header and Navigation Bar -->
  <header class="header-section">
    <div class="full-screen-container navbar">
      <a href="dashboard.php" class="brand-logo">
        <img src="images/bloodlink_logo.png" alt="BloodLink Logo" class="brand-logo-img">
        <span class="brand-logo-text">BLOODLINK</span>
      </a>
      <ul class="nav-links">
        <li><a href="html/home.html">Home</a></li>
        <li><a href="dashboard.php" class="active">Dashboard</a></li>
        <li><a href="html/camps.html">Camps</a></li>
        <li><a href="html/contact.html">Contact</a></li>
        <li>
          <a href="#" class="user-greeting">
            <i class="fa-regular fa-circle-user"></i>
            <span><?php echo htmlspecialchars($userName); ?></span>
          </a>
        </li>
      </ul>
    </div>
  </header>

    <main class="dashboard-wrapper">
        <!--System Metrics Bar-->
        <section class="overview-section">
            <p class="tagline">LIVE SYSTEM DASHBOARD</p>
            <h2 class="title">Donation Network Overview</h2>
            <div class="metrics-grid">
                <!-- 1. Live Visitors Card -->
                <div class="metric-card">
                    <div class="card-top"><span>New Visitors (Today)</span> <i class="fa-regular fa-eye"></i></div>
                    <div class="card-num"><?php echo number_format($todayVisitors); ?></div>
                    <span class="trend success">Live system traffic</span>
                </div>

                <!-- 2. Registered Donors Card -->
                <div class="metric-card">
                    <div class="card-top"><span>Registered Donors</span> <i class="fa-regular fa-heart"></i></div>
                    <div class="card-num"><?php echo number_format($donorCount); ?></div>
                    <span class="trend">+<?php echo $todayDonors; ?> new registrations today</span>
                </div>

                <!-- 3. Active Camps Card -->
                <a href="html/camps.html" class="metric-card highlight-card">
                    <div class="card-top"><span>Active Camps</span> <i class="fa-regular fa-flag"></i></div>
                    <div class="card-num"><?php echo number_format($campCount); ?></div>
                    <span class="trend">Click here for more...</span>
                </a>

                <!-- 4. Emergency Requests Card -->
                <a href="html/emergency-requests.html" class="metric-card highlight-card">
                    <div class="card-top"><span>Emergency Requests</span> <i class="fa-regular fa-message"></i></div>
                    <div class="card-num"><?php echo number_format($requestCount); ?></div>
                    <span class="trend">Add or review requests</span>
                </a>
            </div>
        </section>

        <!-- Cards for emergency -->
        <section class="action-card-banner">
            <div class="banner-img">
                <img src="images/card1.png" alt="Become a Donor">
            </div>
            <div class="banner-details">
                <p class="tagline">JOIN THE REGISTRY</p>
                <h2>Become a Donor Today & Save Local Lives</h2>
                <p>Every individual donation can save up to three lives. Our centralized network coordinates directly with regional blood banks, ensuring your contribution lands exactly where the emergency demand is highest.</p>
                <div class="btn-cluster">
                    <a href="html/login-register.html" class="btn btn-red">REGISTER AS DONOR</a>
                    <a href="https://www.who.int/campaigns/world-blood-donor-day/2018/who-can-give-blood" class="btn btn-border" target="_blank">CHECK ELIGIBILITY</a>
                </div>
            </div>
        </section>

        <section class="action-card-banner reverse-layout">
            <div class="banner-img">
                <img src="images/card2.png" alt="Urgent Blood Assistance">
            </div>
            <div class="banner-details">
                <p class="tagline emergency-tag">EMERGENCY REQUESTS</p>
                <h2>Do You Need Urgent Blood Assistance?</h2>
                <p>If you or a family member requires immediate blood replenishment for critical procedures or urgent surgical intervention, you can post a verified emergency request onto our active campaign network immediately.</p>
                <div class="btn-cluster">
                    <a href="html/emergency-requests.html" class="btn btn-red">REQUEST EMERGENCY BLOOD</a>
                    <a href="contact.php" class="btn btn-border" target="_blank">CONTACT FOR CLINICAL GUIDELINES</a>
                </div>
            </div>
        </section>

        <!--Featured Camps-->
        <section class="campaign-spotlight">
            <div class="spotlight-head">
                <div>
                    <p class="tagline">ACTIVE COMMUNITY DRIVES</p>
                    <h2>Featured Camps</h2>
                </div>
                <a href="html/camps.html" class="btn btn-red btn-sm">ALL CAMPS &rarr;</a>
            </div>
            <div class="camp-highlight-card">
                <div class="thumb-wrapper">
                    <img src="images/card3.png" alt="Save Lives Event">
                </div>
            </div>
        </section>

        <!--Blood level-->
        <section class="stock-dashboard-section">
            <p class="tagline text-center">REAL-TIME RESERVE STATUS</p>
            <h2 class="title text-center">Current Blood Stock Levels</h2>
            <div class="stock-dashboard-grid">
                <div class="stock-box">
                    <div class="box-top"><strong>A+</strong><span class="badge optimal">OPTIMAL</span></div>
                    <div class="meter"><div class="fill" style="width: 84%;"></div></div>
                    <span class="meter-num">84%</span>
                </div>
                <div class="stock-box">
                    <div class="box-top"><strong>B+</strong><span class="badge optimal">OPTIMAL</span></div>
                    <div class="meter"><div class="fill" style="width: 72%;"></div></div>
                    <span class="meter-num">72%</span>
                </div>
                <div class="stock-box">
                    <div class="box-top"><strong>O+</strong><span class="badge moderate">MODERATE</span></div>
                    <div class="meter"><div class="fill" style="width: 48%;"></div></div>
                    <span class="meter-num">48%</span>
                </div>
                <div class="stock-box">
                    <div class="box-top"><strong>AB+</strong><span class="badge optimal">OPTIMAL</span></div>
                    <div class="meter"><div class="fill" style="width: 90%;"></div></div>
                    <span class="meter-num">90%</span>
                </div>
                <div class="stock-box critical-box">
                    <div class="box-top"><strong>A-</strong><span class="badge critical">CRITICAL ALERT</span></div>
                    <div class="meter"><div class="fill fill-critical" style="width: 22%;"></div></div>
                    <span class="meter-num">22%</span>
                </div>
                <div class="stock-box">
                    <div class="box-top"><strong>B-</strong><span class="badge moderate">MODERATE</span></div>
                    <div class="meter"><div class="fill" style="width: 35%;"></div></div>
                    <span class="meter-num">35%</span>
                </div>
                <div class="stock-box critical-box">
                    <div class="box-top"><strong>O-</strong><span class="badge critical">CRITICAL ALERT</span></div>
                    <div class="meter"><div class="fill fill-critical" style="width: 12%;"></div></div>
                    <span class="meter-num">12%</span>
                </div>
                <div class="stock-box">
                    <div class="box-top"><strong>AB-</strong><span class="badge stable">STABLE</span></div>
                    <div class="meter"><div class="fill" style="width: 65%;"></div></div>
                    <span class="meter-num">65%</span>
                </div>
            </div>
        </section>

        <!--Sri Lanka Map Using leaflet js-->
        <section class="nearby-section">
            <div class="map-header-flex">
                <div>
                    <p class="tagline">INTERACTIVE LOCATOR</p>
                    <h2 class="title">Find Nearby Camps & Blood Banks</h2>
                </div>
                <div class="map-legend">
                    <span class="legend-item"><span class="dot camp-dot"></span> Blood Donation Camps</span>
                    <span class="legend-item"><span class="dot hospital-dot"></span> Hospitals / Blood Banks</span>
                </div>
            </div>
            
            <div id="bloodLinkMap" class="map-render-area"></div>
        </section>
    </main>

    <!--Contact Info Bar-->
    <div class="info-bar">
      <div class="info-bar-content">
        <div class="info-item">
          <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
          <div class="info-text">
            <span>TOLL FREE HELPLINE</span>
            <strong>1990 / +94 25 226 6641</strong>
          </div>
        </div>
        <div class="info-item">
          <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
          <div class="info-text">
            <span>EMAIL SUPPORT</span>
            <strong>info@bloodlink.lk</strong>
          </div>
        </div>
      </div>
    </div>

    <!--Footer Section-->
    <footer class="footer-section" id="contact">
      <div class="full-screen-container">
        <div class="footer-grid">
          <div class="brand-col">
            <h3>BLOODLINK</h3>
            <p class="footer-val">Connecting voluntary blood donors with regional banks & hospitals across Sri Lanka to save lives 24/7.</p>
            <div class="social-links">
              <a href="#"><i class="fa-brands fa-facebook"></i></a>
              <a href="#"><i class="fa-brands fa-twitter"></i></a>
              <a href="#"><i class="fa-brands fa-instagram"></i></a>
            </div>
          </div>

          <div class="links-col">
            <p class="footer-label header-label">QUICK LINKS</p>
            <ul class="footer-links">
              <li><a href="html/home.html">Home</a></li>
              <li><a href="dashboard.php">Dashboard</a></li>
              <li><a href="html/camps.html">Donation Camps</a></li>
              <li><a href="html/contact.html">Contact Us</a></li>
            </ul>
          </div>

          <div class="help-col">
            <div class="doc-help-badge">
              <div class="badge-title">EMERGENCY HOTLINE</div>
              <div class="badge-number">+94 25 226 6641</div>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <!--Leaflet JS Map-->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="js/map.js"></script>

    <!-- Real-time Blood Stock Live Update -->
    <script>
    async function loadLiveStockFromDB() {
        try {
            const response = await fetch('php/get_blood_stocks.php');
            const stockData = await response.json();
            
            const stockBoxes = document.querySelectorAll('.stock-dashboard-grid .stock-box');

            stockData.forEach((data) => {
                stockBoxes.forEach((box) => {
                    const groupTitle = box.querySelector('.box-top strong');
                    if (groupTitle && groupTitle.innerText.trim() === data.type) {
                        const percentText = box.querySelector('.meter-num');
                        const fillBar = box.querySelector('.meter .fill');
                        const statusBadge = box.querySelector('.box-top .badge');

                        // Value Update
                        if (percentText) percentText.innerText = data.val + '%';
                        if (fillBar) fillBar.style.width = data.val + '%';

                        // Dynamic Styling
                        if (data.val < 30) {
                            if (statusBadge) {
                                statusBadge.innerText = 'CRITICAL ALERT';
                                statusBadge.className = 'badge critical';
                            }
                            box.classList.add('critical-box');
                            if (fillBar) fillBar.className = 'fill fill-critical';
                        } else if (data.val <= 60) {
                            if (statusBadge) {
                                statusBadge.innerText = 'MODERATE';
                                statusBadge.className = 'badge moderate';
                            }
                            box.classList.remove('critical-box');
                            if (fillBar) fillBar.className = 'fill';
                        } else {
                            if (statusBadge) {
                                statusBadge.innerText = 'OPTIMAL';
                                statusBadge.className = 'badge optimal';
                            }
                            box.classList.remove('critical-box');
                            if (fillBar) fillBar.className = 'fill';
                        }
                    }
                });
            });
        } catch (err) {
            console.error("Failed to load blood stocks from DB:", err);
        }
    }

    document.addEventListener('DOMContentLoaded', loadLiveStockFromDB);
    </script>
</body>
</html>