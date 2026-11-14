<?php
session_start();

// Admin Staff Password Entering
define('STAFF_PASSCODE', 'admin123');

// Logout Action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['staff_logged_in']);
    header("Location: admin_stock.php");
    exit();
}

// Login Process
$error_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['passcode'])) {
    if ($_POST['passcode'] === STAFF_PASSCODE) {
        $_SESSION['staff_logged_in'] = true;
    } else {
        $error_msg = 'Incorrect passcode. Access denied.';
    }
}

// If you not login you can see passcode screen
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Staff Authentication - BloodLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_stock_login.css">
</head>
<body>
    <div class="login-box">
        <h2>Hospital Staff Portal</h2>
        <p>Enter your authorization passcode to manage blood reserve stocks.</p>
        <?php if ($error_msg): ?>
            <div class="error"><?= $error_msg ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="password" name="passcode" placeholder="Enter Staff Passcode" required autofocus>
            <button type="submit" class="btn-submit">Authenticate</button>
        </form>
    </div>
</body>
</html>
<?php
exit();
endif;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BloodLink - Hospital Stock Management (Admin)</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin_stock_page.css">
</head>
<body>

<div class="admin-container">
    <div class="top-bar">
        <span class="user-tag">Access Granted: <strong>Hospital Staff</strong></span>
        <a href="admin_stock.php?action=logout" class="logout-btn">Lock & Logout &rarr;</a>
    </div>

    <h2>Blood Bank Stock Management</h2>
    <p>Authorized Staff Portal. Adjust reserve percentages below to update the live public dashboard.</p>
    
    <div class="grid" id="controls-grid">
        <p>Loading current stock data from database...</p>
    </div>

    <button class="btn-save" onclick="updateStockLevels()">Save & Update Public Dashboard</button>
    <div class="success-msg" id="successMsg">Stock levels successfully updated in Database!</div>
</div>

<script>
let bloodList = [];

async function loadStockControls() {
    try {
        const res = await fetch('get_blood_stocks.php');
        bloodList = await res.json();
        const container = document.getElementById('controls-grid');
        container.innerHTML = '';
        
        bloodList.forEach((item, index) => {
            container.innerHTML += `
                <div class="stock-control">
                    <div class="stock-header">
                        <span>${item.type}</span>
                        <span id="val-${index}">${item.val}%</span>
                    </div>
                    <input type="range" class="stock-slider" min="0" max="100" value="${item.val}" 
                        oninput="document.getElementById('val-${index}').innerText = this.value + '%'" id="slider-${index}">
                </div>
            `;
        });
    } catch (e) {
        console.error("Error fetching stocks:", e);
    }
}

async function updateStockLevels() {
    const updated = bloodList.map((item, index) => ({
        type: item.type,
        val: parseInt(document.getElementById(`slider-${index}`).value)
    }));

    try {
        const res = await fetch('update_blood_stock.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ stocks: updated })
        });
        const result = await res.json();
        if (result.status === 'success') {
            const msg = document.getElementById('successMsg');
            msg.style.display = 'block';
            setTimeout(() => { msg.style.display = 'none'; }, 3000);
        }
    } catch (e) {
        alert("Failed to update database.");
    }
}

document.addEventListener('DOMContentLoaded', loadStockControls);
</script>
</body>
</html>