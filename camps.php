<?php
session_start();
// Check if user is logged in, otherwise default to Guest
$user_display_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BloodLink - Upcoming Blood Camps</title>
  
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Leaflet Map CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/camps.css">
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
        <li><a href="camps.php" class="active">Camps</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li>
          <a href="personal-account.php" class="user-greeting">
            <i class="fa-regular fa-circle-user"></i>
            <span>Hi, <?php echo htmlspecialchars($user_display_name); ?></span>
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
        <a href="contact.php">Contact</a>
        <a href="personal-account.php" class="mobile-user-link">
          <i class="fa-regular fa-circle-user"></i>
          <span>Hi, <?php echo htmlspecialchars($user_display_name); ?></span>
        </a>
      </nav>
    </div>
  </header>

  <!-- Main Full Screen Content -->
  <main class="full-screen-container main-content">
    
    <!-- Title & Add Button Row -->
    <div class="page-title-row">
      <div class="title-group">
        <div class="section-tag hero-badge-animate">Save a Life Today</div>
        <h1 class="main-heading hero-title-animate">UPCOMING BLOOD CAMPS</h1>
        <p class="sub-text hero-desc-animate">Browse and register for blood donation camps near you. Help save lives by participating.</p>
      </div>
      <a href="add_camps.php" class="btn-add hero-actions-animate" style="text-decoration: none;">
        <i class="fa-solid fa-plus"></i> ADD BLOOD CAMPS
      </a>
    </div>

    <!-- 100% Full-Width Search Input -->
    <div class="search-box hero-desc-animate">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="searchInput" placeholder="Search by camp name, organization name or location...">
    </div>

    <!-- Scrollable Cards Slider (Dynamic Content via JS) -->
    <div class="camps-carousel-wrap reveal-on-scroll">
      <button class="arrow-btn prev" id="prevBtn" title="Previous"><i class="fa-solid fa-chevron-left"></i></button>
      
      <!-- Cards will be dynamically injected here from MySQL Database -->
      <div class="camps-slider" id="campsSlider"></div>

      <button class="arrow-btn next" id="nextBtn" title="Next"><i class="fa-solid fa-chevron-right"></i></button>
    </div>

    <!-- Nearby Camps Map Section -->
    <div class="map-section reveal-on-scroll">
      <div class="map-header">
        <span class="vertical-bar"></span>
        <h2>NEARBY CAMPS</h2>
        <span class="sub-map-label">based on your location</span>
      </div>
      <div id="bloodLinkMap"></div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="footer-section">
    <div class="full-screen-container footer-content">
      <p>&copy; 2026 BloodLink. All Rights Reserved.</p>
    </div>
  </footer>

  <!-- Leaflet Map JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <!-- Custom JS -->
  <script src="js/map.js"></script>
  
  <script src="js/camps.js"></script>
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