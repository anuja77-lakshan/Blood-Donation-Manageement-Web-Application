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
  <title>Add Blood Camp - BloodLink</title>

  <!-- Font Awesome Icons for UI Elements -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Leaflet Map CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

  <!-- Google Fonts: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Custom Stylesheet -->
  <link rel="stylesheet" href="css/add_camps.css">
</head>
<body>

  <!-- Top Full Width Navbar -->
  <header class="header-section">
    <div class="full-screen-container navbar">
      <a href="home.php" class="brand-logo">
        <img src="images/bloodlink_logo.png" alt="BloodLink Logo" class="brand-logo-img">
        <span class="brand-logo-text">BLOODLINK</span>
      </a>
      <ul class="nav-links">
        <li><a href="home.php">Home</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="camps.php" class="active">Camps</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li>
          <a href="personal-account.php" class="user-greeting">
            <i class="fa-regular fa-circle-user"></i>
            <span><?php echo htmlspecialchars($user_display_name); ?></span>
          </a>
        </li>
      </ul>
    </div>
  </header>

  <!-- Main Content Container -->
  <main class="full-screen-container form-main">
    
    <!-- Navigation Back Link -->
    <a href="camps.php" class="back-link">
      <i class="fa-solid fa-arrow-left"></i> Back to Camps
    </a>

    <div class="form-wrapper">
      <div class="form-header">
        <div class="section-tag hero-badge-animate">Camp Registration</div>
        <h1 class="main-heading hero-title-animate">ADD NEW BLOOD CAMP</h1>
        <p class="sub-text hero-desc-animate">Fill in the details below to register and publish a new blood donation camp event.</p>
      </div>

      <!-- Add Camp Form -->
      <form action="php/save_camp.php" method="POST" enctype="multipart/form-data">
        
        <div class="form-row">
          <div class="form-group">
            <label for="campName">Camp Name <span>*</span></label>
            <input type="text" id="campName" name="campName" placeholder="e.g., City Central Blood Drive" required>
          </div>
          <div class="form-group">
            <label for="orgName">Organization Name <span>*</span></label>
            <input type="text" id="orgName" name="orgName" placeholder="e.g., Red Cross Society" required>
          </div>
        </div>

        <div class="form-row three-cols">
          <div class="form-group">
            <label for="campDate">Date <span>*</span></label>
            <input type="date" id="campDate" name="campDate" required>
          </div>
          <div class="form-group">
            <label for="startTime">Start Time <span>*</span></label>
            <input type="time" id="startTime" name="startTime" required>
          </div>
          <div class="form-group">
            <label for="endTime">End Time <span>*</span></label>
            <input type="time" id="endTime" name="endTime" required>
          </div>
        </div>

        <div class="form-group full-width">
          <label for="campLocation">Location / Venue Address <span>*</span></label>
          <input type="text" id="campLocation" name="campLocation" placeholder="e.g., Community Hall, Main Street, Colombo" required>
        </div>

        <!-- Google Maps Location Link Input -->
        <div class="form-group full-width" style="margin-top: 10px; margin-bottom: 12px;">
          <label for="mapUrl">Google Maps Location Link <span style="font-weight: normal; color: #64748b;">(Link එක paste කරන්න හෝ map එකෙන් තෝරන්න)</span></label>
          <div style="position: relative;">
            <input type="text" id="mapUrl" placeholder="Paste Google Maps location link here (e.g., https://maps.app.goo.gl/... or https://maps.google.com/...)" 
                   style="width: 100%; padding-right: 36px;">
            <i class="fa-solid fa-location-crosshairs" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 16px;"></i>
          </div>
          <span id="mapStatusText" style="font-size: 12px; margin-top: 5px; display: block; font-weight: 600;"></span>
        </div>

        <!-- Hidden Coordinates for save_camps.php-->
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">

        <!-- Map Preview-->
        <div class="form-group full-width" style="margin-top: 0; margin-bottom: 25px;">
          <label style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
            <span>Selected Location Preview</span>
            <span style="font-size: 11px; font-weight: normal; color: #64748b;">Drag pin or click on the map</span>
          </label>
          <div id="campPickerMap" style="width: 100%; height: 280px; border-radius: 12px; border: 1.5px solid #cbd5e1; z-index: 1;"></div>
        </div> 
        
        <div class="form-group full-width">
          <label>Cover Image (Upload)</label>
          <div class="file-dropzone">
            <input type="file" id="coverImage" name="coverImage" accept="image/*">
            <div class="dropzone-content">
              <i class="fa-solid fa-cloud-arrow-up dropzone-icon"></i>
              <span class="dropzone-title">Click to browse or drag and drop image here</span>
              <span class="dropzone-hint">PNG, JPG, JPEG, WEBP up to 5MB</span>
            </div>
          </div>
        </div>

        <div class="form-actions hero-actions-animate">
          <a href="camps.php" class="btn-cancel">Cancel</a>
          <button type="submit" class="btn-submit">
            <i class="fa-solid fa-check"></i> Publish Blood Camp
          </button>
        </div>

      </form>
    </div>

  </main>

  <!-- Leaflet Map JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <!-- Custom JS -->
  <script src="js/add_camps.js"></script>
</body>
</html>