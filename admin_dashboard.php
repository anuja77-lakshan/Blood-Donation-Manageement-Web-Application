<?php
session_start();
if (isset($_GET['action']) && $_GET['action'] === 'logout') {$_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
    header("Location: home.php");
    exit();
}

// Check if user is logged in as admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Redirect unauthorized users back to login page
    header("Location: login.php#admin");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BloodLink - Central Admin Terminal</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" />
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

        <a href="admin_dashboard.php?action=logout" class="topbar-logout-btn">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
          <span>Logout</span>
        </a>
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
          Manage donors, blood stock, and check-ins from one place. Use the 3 tools below:
        </p>

        <div class="instruction-steps">
          <div class="step-box">
            <div class="step-top-row">
              <span class="step-number">PORTAL 01</span>
            </div>
            <h4>Users History & Records</h4>
            <p>Look up donors and see their past donations.</p>
          </div>

          <div class="step-box">
            <div class="step-top-row">
              <span class="step-number">PORTAL 02</span>
            </div>
            <h4>Live Blood Stock Updater</h4>
            <p>Add or remove blood units for all 8 blood groups.</p>
          </div>

          <div class="step-box">
            <div class="step-top-row">
              <span class="step-number">PORTAL 03</span>
            </div>
            <h4>QR Code Arrival Scanner</h4>
            <p>Scan donor QR passes to mark attendance.</p>
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
                Search donors and view their donation history.
              </p>
              <ul class="card-features-list">
                <li><i class="fa-solid fa-check"></i> Search donors by User ID </li>
                <li><i class="fa-solid fa-check"></i> View past donation logs </li>
              </ul>
            </div>
          </div>
          <button type="button" onclick="openHistoryModal()" class="btn-portal btn-portal-dark">
            <span>OPEN USERS HISTORY</span>
            <i class="fa-solid fa-arrow-right"></i>
          </button>
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
                Update blood stock instantly after donations or hospital use.
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
                Scan donor passes with your camera at donation camps.
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

  <footer class="footer-section reveal-on-scroll">
    <div class="full-screen-container footer-content">
      <p>&copy; 2026 BloodLink. All Rights Reserved.</p>
    </div>
  </footer>

  <!-- Search Modal for User History -->
  <div id="userHistoryModal" class="modal-backdrop">
    <div class="modal-dialog">
      <div class="modal-header">
        <div class="modal-title">
          <i class="fa-solid fa-clock-rotate-left"></i>
          <h3>Donor Last Donation Lookup</h3>
        </div>
        <button type="button" class="modal-close-btn" onclick="closeHistoryModal()">&times;</button>
      </div>

      <div class="modal-body">
        <p class="modal-subtitle">Search registered donor records to retrieve their most recent donation timestamp.</p>
        
        <div class="modal-search-box">
          <input type="text" inputmode="numeric" pattern="[0-9]*" id="donorUserIdInput" placeholder="Enter User ID (e.g. 11)" />
          <button type="button" onclick="searchUserHistory()" class="btn-portal btn-portal-dark modal-search-btn">
            <i class="fa-solid fa-magnifying-glass"></i> Search
          </button>
        </div>

        <div id="searchResultArea"></div>
      </div>
    </div>
  </div>

  <!-- Toast Notification -->
  <div id="adminToast">
    <i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 16px;"></i>
    <span id="toastMsg">Action completed successfully.</span>
  </div>

  <script>
    function openHistoryModal() {
      document.getElementById('userHistoryModal').classList.add('active');
      document.getElementById('donorUserIdInput').value = '';
      document.getElementById('searchResultArea').innerHTML = '';
      document.getElementById('donorUserIdInput').focus();
    }

    function closeHistoryModal() {
      document.getElementById('userHistoryModal').classList.remove('active');
    }

    window.onclick = function(event) {
      const modal = document.getElementById('userHistoryModal');
      if (event.target === modal) {
        closeHistoryModal();
      }
    };

    document.getElementById('donorUserIdInput').addEventListener('keydown', function(event) {
      if (event.key === 'Enter') {
        searchUserHistory();
      }
    });

    function searchUserHistory() {
      const userId = document.getElementById('donorUserIdInput').value.trim();
      const resultArea = document.getElementById('searchResultArea');

      if (!userId) {
        resultArea.innerHTML = '<div class="alert-box alert-error"><i class="fa-solid fa-circle-exclamation"></i> Please enter a User ID.</div>';
        return;
      }

      resultArea.innerHTML = '<div class="alert-box alert-loading"><i class="fa-solid fa-spinner fa-spin"></i> Retrieving donor records...</div>';

      fetch(`php/get_last_donation.php?user_id=${encodeURIComponent(userId)}`)
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            resultArea.innerHTML = `
              <div class="result-card">
                <div class="result-row">
                  <span class="result-label">Donor Name:</span>
                  <span class="result-value">${data.full_name}</span>
                </div>
                <div class="result-row">
                  <span class="result-label">Blood Group:</span>
                  <span class="result-badge">${data.blood_group}</span>
                </div>
                <div class="result-highlight-box">
                  <span class="highlight-title"><i class="fa-regular fa-calendar-check"></i> Last Donation Details</span>
                  <div class="highlight-item">
                    <span>Date:</span> <strong>${data.last_donation_date}</strong>
                  </div>
                  <div class="highlight-item">
                    <span>Time:</span> <strong>${data.last_donation_time}</strong>
                  </div>
                  <div class="highlight-item">
                    <span>Location:</span> <strong>${data.location} (${data.camp_name})</strong>
                  </div>
                </div>
              </div>
            `;
          } else if (data.status === 'warning') {
            resultArea.innerHTML = `
              <div class="alert-box alert-warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                  <strong>${data.full_name} (Blood Group: ${data.blood_group})</strong>
                  <p style="margin-top: 4px; font-size: 13px;">${data.message}</p>
                </div>
              </div>
            `;
          } else {
            resultArea.innerHTML = `<div class="alert-box alert-error"><i class="fa-solid fa-circle-xmark"></i> ${data.message}</div>`;
          }
        })
        .catch(() => {
          resultArea.innerHTML = '<div class="alert-box alert-error"><i class="fa-solid fa-circle-xmark"></i> An error occurred while retrieving data.</div>';
        });
    }

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

    // Reveal on scroll for footer
    document.addEventListener('DOMContentLoaded', function() {
      const reveals = document.querySelectorAll('.reveal-on-scroll');
      if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('is-visible');
              observer.unobserve(entry.target);
            }
          });
        }, { threshold: 0.1 });
        reveals.forEach(el => observer.observe(el));
      } else {
        reveals.forEach(el => el.classList.add('is-visible'));
      }
    });
  </script>
</body>
</html>