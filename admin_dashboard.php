<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BloodLink - Central Admin Terminal</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="css/admin_dashboard.css" />
</head>
<body>

  <div class="admin-wrapper">

    <!-- Navbar -->
    <header class="admin-topbar">
      <div class="topbar-left">
        <a href="admin_dashboard.php" class="brand-logo">
          <img src="images/bloodlink_logo.png" alt="BloodLink Logo" class="brand-logo-img">
          <span class="brand-logo-text">BLOODLINK <span class="admin-badge">ADMIN</span></span>
        </a>
      </div>

      <div class="topbar-right">
        <div class="user-greeting">
          <i class="fa-regular fa-circle-user"></i>
          <span>Administrator</span>
        </div>

        <button onclick="showToast('Logged out of administrator session securely.')" class="topbar-logout-btn">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
          <span>Logout</span>
        </button>
      </div>
    </header>

    <main class="dashboard-body">

      <!-- Page Header -->
      <div class="page-header">
        <div>
          <span class="section-badge">
            <i class="fa-solid fa-shield-halved"></i> CENTRAL COMMAND CONSOLE
          </span>
          <h1 class="main-heading">ADMIN TERMINAL</h1>
        </div>
        <div>
          <a href="camps.php" class="btn-portal btn-portal-red" style="padding: 12px 24px; font-size: 12.5px;">
            <i class="fa-solid fa-tent"></i> Public Camps View
          </a>
        </div>
      </div>

      <section class="instruction-card">
        <div class="instruction-head">
          <div class="instruction-title-wrap">
            <div class="instruction-icon">
              <i class="fa-solid fa-circle-info"></i>
            </div>
            <h2>Admin Operating Instructions & Portal Overview</h2>
          </div>
        </div>
        <p class="instruction-desc">
          Use this centralized terminal to coordinate blood donation operations across regional hospitals and campus donation drives. The three primary modules below handle real-time field workflows:
        </p>

        <div class="instruction-steps">
          <div class="step-box">
            <div class="step-top-row">
              <span class="step-number">PORTAL 01</span>
            </div>
            <h4>Users History & Records</h4>
            <p>Audit donor profiles, track lifetime units donated, check past medical deferrals, and view certificate eligibility.</p>
          </div>

          <div class="step-box">
            <div class="step-top-row">
              <span class="step-number">PORTAL 02</span>
            </div>
            <h4>Live Blood Stock Updater</h4>
            <p>Directly adjust reserves for all 8 blood groups (A+, B+, O+, AB+ and Rh-) after drives or emergency hospital cross-matches.</p>
          </div>

          <div class="step-box">
            <div class="step-top-row">
              <span class="step-number">PORTAL 03</span>
            </div>
            <h4>QR Code Arrival Scanner</h4>
            <p>Instantly scan a donor's digital BloodLink Pass via mobile camera or webcam to log on-site attendance and approve donations.</p>
          </div>
        </div>
      </section>

      <!-- 3 Cards -->
      <section class="portal-cards-grid">
        
        <div class="portal-card">
          <div>
            <div class="card-top-badge">
              <div class="portal-icon-circle icon-history">
                <i class="fa-solid fa-clock-rotate-left"></i>
              </div>
              <div class="card-meta-tags">
                <span class="card-tag">DATABASE</span>
              </div>
            </div>
            <div class="card-content">
              <h3>Users History</h3>
              <p>
                Access comprehensive donor transaction records, NIC registries, blood group records, and medical screening logs across Sri Lanka.
              </p>
              <ul class="card-features-list">
                <li><i class="fa-solid fa-check"></i> Search donors by User ID </li>
                <li><i class="fa-solid fa-check"></i> View past donation logs </li>
              </ul>
            </div>
          </div>
          <a href="user_history.php" class="btn-portal btn-portal-dark">
            <span>Open Users History</span>
            <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>

        <div class="portal-card card-featured">
          <div>
            <div class="card-top-badge">
              <div class="portal-icon-circle icon-stock">
                <i class="fa-solid fa-droplet"></i>
              </div>
              <div class="card-meta-tags">
                <span class="card-tag tag-stock">INVENTORY</span>
              </div>
            </div>
            <div class="card-content">
              <h3>Blood Stock System</h3>
              <p>
                Interactive management portal allowing administrators to add new collected units or log critical emergency hospital cross-matches instantly.
              </p>
              <ul class="card-features-list">
                <li><i class="fa-solid fa-check"></i> Monitor all 8 blood groups in real time</li>
                <li><i class="fa-solid fa-check"></i> Add or deduct unit quantities immediately</li>
              </ul>
            </div>
          </div>
          <a href="php/admin_stock.php" class="btn-portal btn-portal-red">
            <span>Open Stock Portal</span>
            <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>

        <div class="portal-card">
          <div>
            <div class="card-top-badge">
              <div class="portal-icon-circle icon-qr">
                <i class="fa-solid fa-qrcode"></i>
              </div>
              <div class="card-meta-tags">
                <span class="tag-live-status" style="background: #f3e8ff; color: #7e22ce;">
                  <i class="fa-solid fa-camera"></i> Ready
                </span>
              </div>
            </div>
            <div class="card-content">
              <h3>QR Code Scanner</h3>
              <p>
                Scan the digital BloodLink Pass presented on a donor's smartphone or printed card during on-site donation drives for instant processing.
              </p>
              <ul class="card-features-list">
                <li><i class="fa-solid fa-check"></i> Scan passes via device camera or scanner </li>
                <li><i class="fa-solid fa-check"></i> Instant verification and clearance status</li>
              </ul>
            </div>
          </div>
          <a href="scanner.php" class="btn-portal btn-portal-purple">
            <span>Launch QR Scanner</span>
            <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>

      </section>

    </main>
  </div>

  <div id="adminToast">
    <i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 16px;"></i>
    <span id="toastMsg">Action completed successfully.</span>
  </div>

  <script>
    function showToast(message) {
      const toast = document.getElementById('adminToast');
      const toastMsg = document.getElementById('toastMsg');
      if (!toast || !toastMsg) return;
      toastMsg.textContent = message;
      toast.classList.add('show-toast');

      setTimeout(() => {
        toast.classList.remove('show-toast');
      }, 3200);
    }
  </script>
</body>
</html>