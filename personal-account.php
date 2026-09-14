<?php
session_start();
require_once 'php/db.php'; // DB connection

// Restrict unauthorized access
if (!isset($_SESSION['user_email'])) {
    header("Location: login-register.php");
    exit();
}

// Fetch current user details
$email  = mysqli_real_escape_string($conn, $_SESSION['user_email']);
$sql    = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    session_destroy();
    header("Location: login-register.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Account - BloodLink</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/personal-account.css">
</head>
<body>

  <!-- Header & Navbar -->
  <header class="header-section">
    <div class="full-screen-container navbar">
      <a href="home.php" class="brand-logo">
        <img src="images/bloodlink_logo.png" alt="BloodLink Logo" class="brand-logo-img">
        <span class="brand-logo-text">BLOODLINK</span>
      </a>
      <ul class="nav-links">
        <li><a href="home.php">Home</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="camps.php">Camps</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li>
          <a href="personal-account.php" class="user-greeting">
            <i class="fa-regular fa-circle-user"></i>
            <span>Hi, <?php echo htmlspecialchars($user['full_name']); ?></span>
          </a>
        </li>
      </ul>
    </div>
  </header>

  <!-- Main Content Layout -->
  <main class="full-screen-container">
    <div class="dashboard-container">
      
      <!-- Welcome Header -->
      <div class="header">
        <div class="profile-pic hero-badge-animate">
          <i class="fa-solid fa-user"></i>
        </div>
        <div class="welcome-text">
          <h2 class="main-heading hero-title-animate">Hello, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>
          <p class="sub-text hero-desc-animate">Welcome to your LifeSaver dashboard</p>
        </div>
      </div>

      <!-- Info Cards Grid -->
      <div class="info-cards-wrapper hero-actions-animate">
        
        <!-- Donor Personal Details -->
        <div class="user-card">
          <div class="personal-details">
            <h3>Donor Information</h3>
            <p><span>Full Name:</span> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p><span>Date of Birth:</span> <?php echo htmlspecialchars($user['dob']); ?></p>
            <p><span>Gender:</span> <?php echo htmlspecialchars($user['gender']); ?></p>
            <p><span>NIC/Passport Number:</span> <?php echo htmlspecialchars($user['nic']); ?></p>
            <p><span>Phone:</span> <?php echo htmlspecialchars($user['phone']); ?></p>
            <p><span>Email address:</span> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><span>Location:</span> <?php echo htmlspecialchars($user['location']); ?></p>
            <p><span>District:</span> <?php echo htmlspecialchars($user['district']); ?></p>
          </div>
        </div>

        <!-- Blood Donation Information -->
        <div class="user-card">
          <div class="personal-details">
            <h3>Blood Donation Information</h3>
            <p><span>Blood Group:</span> <strong style="color: var(--primary-color); font-size: 16px;"><?php echo htmlspecialchars($user['blood_group']); ?></strong></p>
            <p><span>Weight:</span> <?php echo htmlspecialchars($user['weight']); ?> kg</p>
            <p><span>Last Donation Date:</span> <?php echo !empty($user['last_donation_date']) ? htmlspecialchars($user['last_donation_date']) : 'None Recorded'; ?></p>
            <p><span>Total Donations:</span> <?php echo isset($user['total_donations']) ? htmlspecialchars($user['total_donations']) : '0'; ?></p>
          </div>
        </div>

      </div>

      <!-- Action Shortcut Buttons -->
      <div class="action-grid hero-actions-animate">
        <a href="#" class="action-btn">
          <div class="btn-icon"><i class="fa-solid fa-droplet"></i></div>
          Donor Form
        </a>
        <a href="#" class="action-btn">
          <div class="btn-icon"><i class="fa-solid fa-hospital"></i></div>
          Patient Form
        </a>
        <a href="history.php" class="action-btn">
          <div class="btn-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
          History
        </a>
        <a href="#" class="action-btn">
          <div class="btn-icon"><i class="fa-solid fa-qrcode"></i></div>
          QR Code
        </a>
      </div>

      <!-- Logout button link -->
        <div class="button-container hero-actions-animate" style="margin-top: 30px;">
        <a href="php/logout.php" class="button logout-btn">Log Out</a>
        </div>

    </div>
  </main>

  <!-- Footer Section -->
  <footer class="footer-section">
    <div class="full-screen-container footer-content">
      <p>&copy; 2026 BloodLink. All Rights Reserved.</p>
    </div>
  </footer>

</body>
</html>