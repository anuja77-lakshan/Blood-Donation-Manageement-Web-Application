<?php
session_start();

// Block unauthorized user access
if (!isset($_SESSION['user_email'])) {
    header("Location: login-register.php#login");
    exit();
}

// Redirect admin to admin dashboard
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header("Location: admin_dashboard.php");
    exit();
}

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

// Fetch Featured Camps from Database 
$featuredCamps = [];
$resFeatured = $conn->query("SELECT * FROM blood_camps ORDER BY camp_date ASC LIMIT 3");
if ($resFeatured && $resFeatured->num_rows > 0) {
    while ($camp = $resFeatured->fetch_assoc()) {
        $featuredCamps[] = $camp;
    }
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

    <!-- CSS File Set -->
    <link rel="stylesheet" href="css/dashboard.css?v=<?php echo time(); ?>">
    <!-- Font-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet.js and Map CSS Code -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        /* Mobile Hamburger Button */
        .mobile-hamburger-btn {
            display: none;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            font-size: 24px;
            color: #0f172a;
            cursor: pointer;
            padding: 4px;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .mobile-hamburger-btn:hover {
            color: #ef3446;
        }

        .mobile-dropdown-menu {
            display: none;
        }

        @media (max-width: 768px) {
            .navbar .nav-links {
                display: none !important;
            }

            .mobile-hamburger-btn {
                display: flex !important;
            }

            .mobile-dropdown-menu.active {
                display: flex !important;
                flex-direction: column;
                background-color: #ffffff;
                border-top: 1px solid #e2e8f0;
                padding: 16px 20px;
                gap: 12px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
            }

            .mobile-dropdown-menu a {
                text-decoration: none;
                color: #334155;
                font-weight: 600;
                font-size: 15px;
                padding: 4px 0;
            }

            .mobile-dropdown-menu a.active-mobile,
            .mobile-dropdown-menu a:hover {
                color: #ef3446;
            }

            .mobile-user-row {
                padding-top: 10px;
                border-top: 1px solid #e2e8f0;
            }
        }
    </style>
</head>
<body>

  <!-- Header and Navigation Bar -->
  <header class="header-section">
    <div class="full-screen-container navbar">
      <a href="dashboard.php" class="brand-logo">
        <img src="images/bloodlink_logo.png" alt="BloodLink Logo" class="brand-logo-img">
        <span class="brand-logo-text">BLOODLINK</span>
      </a>
      
      <!-- Navigation Links -->
      <ul class="nav-links">
        <li><a href="home.php">Home</a></li>
        <li><a href="dashboard.php" class="active">Dashboard</a></li>
        <li><a href="camps.php">Camps</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li>
          <a href="personal-account.php" class="user-greeting">
            <i class="fa-regular fa-circle-user"></i>
            <span><?php echo htmlspecialchars($userName); ?></span>
          </a>
        </li>
      </ul>

      <!-- Mobile Hamburger Button -->
      <button id="mobileMenuBtn" type="button" class="mobile-hamburger-btn" aria-label="Toggle navigation">
        <i id="menuIcon" class="fa-solid fa-bars"></i>
      </button>
    </div>

    <!-- Mobile Dropdown -->
    <div id="mobileMenu" class="mobile-dropdown-menu">
      <a href="home.php">Home</a>
      <a href="dashboard.php" class="active-mobile">Dashboard</a>
      <a href="camps.php">Camps</a>
      <a href="contact.php">Contact</a>
      <div class="mobile-user-row">
        <a href="personal-account.php">
          <i class="fa-regular fa-circle-user"></i>
          <span><?php echo htmlspecialchars($userName); ?></span>
        </a>
      </div>
    </div>
  </header>

    <main class="dashboard-wrapper">
        <!--System Metrics Bar-->
        <section class="overview-section">
            <p class="tagline">LIVE SYSTEM DASHBOARD</p>
            <h2 class="title">Donation Network Overview</h2>
            <div class="metrics-grid">
                <!--Active Camps Card-->
                <a href="camps.php" class="metric-card highlight-green">
                    <div class="card-top"><span>Active Camps</span> <i class="fa-regular fa-flag"></i></div>
                    <div class="card-num"><?php echo number_format($campCount); ?></div>
                    <span class="trend">Click here for more...</span>
                </a>

                <!--Emergency Requests Card-->
                <a href="emergency-requests.php" class="metric-card highlight-card">
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
                    <a href="emergency-requests.php" class="btn btn-red">REQUEST EMERGENCY BLOOD</a>
                    <a href="contact.php" class="btn btn-border" target="_blank">CONTACT FOR CLINICAL GUIDELINES</a>
                </div>
            </div>
        </section>

        <!--Featured Camps (Dynamic Cards from Database)-->
        <section class="campaign-spotlight">
            <div class="spotlight-head">
                <div>
                    <p class="tagline">ACTIVE COMMUNITY DRIVES</p>
                    <h2>Featured Camps</h2>
                </div>
                <a href="camps.php" class="btn btn-green btn-sm">ALL CAMPS &rarr;</a>
            </div>
            
            <div class="camps-grid-custom">
                <?php if (!empty($featuredCamps)): ?>
                    <?php foreach ($featuredCamps as $camp): 
                        // Camp Title
                        $cName = !empty($camp['camp_name']) ? $camp['camp_name'] : 
                                 (!empty($camp['name']) ? $camp['name'] : 
                                 (!empty($camp['title']) ? $camp['title'] : 'Blood Donation Drive'));

                        // Camp Date
                        $cDate = !empty($camp['camp_date']) ? $camp['camp_date'] : 
                                 (!empty($camp['date']) ? $camp['date'] : 'Upcoming');

                        // Location
                        $cLoc = !empty($camp['location']) ? $camp['location'] : 
                                (!empty($camp['address']) ? $camp['address'] : 'Local Community Center');

                        // Conatact details
                        $cContact = !empty($camp['contact_no']) ? $camp['contact_no'] : 
                                    (!empty($camp['contact']) ? $camp['contact'] : 
                                    (!empty($camp['phone']) ? $camp['phone'] : '1990'));

                        // Organizer
                        $cOrg = !empty($camp['organization']) ? $camp['organization'] : 
                                (!empty($camp['organizer']) ? $camp['organizer'] : 
                                (!empty($camp['organizer_name']) ? $camp['organizer_name'] : 
                                (!empty($camp['conducted_by']) ? $camp['conducted_by'] : $cName)));
                    ?>
                        <div class="featured-camp-card">
                            <div class="camp-card-header">
                                <h3><?php echo htmlspecialchars($cName); ?></h3>
                                <div class="camp-date-badge">
                                    <i class="fa-regular fa-calendar"></i>
                                    <span><?php echo htmlspecialchars($cDate); ?></span>
                                </div>
                            </div>
                            <div class="camp-card-body">
                                <div class="camp-info-row">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span><?php echo htmlspecialchars($cLoc); ?></span>
                                </div>
                                <div class="camp-info-row">
                                    <i class="fa-solid fa-sitemap"></i>
                                    <span>Organized by: <strong><?php echo htmlspecialchars($cOrg); ?></strong></span>
                                </div>
                                <div class="camp-info-row">
                                    <i class="fa-solid fa-phone"></i>
                                    <span>Helpline: <?php echo htmlspecialchars($cContact); ?></span>
                                </div>
                            </div>
                            <div class="camp-card-footer">
                                <a href="camps.php" class="btn-camp-view">View Details & Register &rarr;</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="featured-camp-card" style="grid-column: 1 / -1; padding: 30px; text-align: center;">
                        <i class="fa-solid fa-campground" style="font-size: 36px; color: #cbd5e1; margin-bottom: 10px;"></i>
                        <h4 style="margin: 0 0 8px 0; color: #475569;">No Active Camps Found</h4>
                        <p style="margin: 0 0 15px 0; color: #64748b; font-size: 14px;">Organize or add a blood donation drive to see it listed here.</p>
                        <a href="add_camps.php" class="btn btn-green btn-sm" style="display: inline-block;">Add Blood Camp</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!--Blood level-->
        <section class="stock-dashboard-section">
            <p class="tagline text-center">REAL TIME RESERVE STATUS</p>
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

  <footer class="footer-section">
    <div class="full-screen-container footer-content">
      <p>&copy; 2026 BloodLink. All Rights Reserved.</p>
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

                        if (percentText) percentText.innerText = data.val + '%';
                        if (fillBar) fillBar.style.width = data.val + '%';

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

    <!-- Mobile Hamburger toggole-->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const menuIcon = document.getElementById('menuIcon');

        if (menuBtn && mobileMenu && menuIcon) {
          menuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = mobileMenu.classList.toggle('active');
            
            if (isOpen) {
              menuIcon.classList.remove('fa-bars');
              menuIcon.classList.add('fa-xmark');
            } else {
              menuIcon.classList.remove('fa-xmark');
              menuIcon.classList.add('fa-bars');
            }
          });

          document.addEventListener('click', function(e) {
            if (!mobileMenu.contains(e.target) && !menuBtn.contains(e.target)) {
              if (mobileMenu.classList.contains('active')) {
                mobileMenu.classList.remove('active');
                menuIcon.classList.remove('fa-xmark');
                menuIcon.classList.add('fa-bars');
              }
            }
          });
        }
      });
    </script>
</body>
</html>