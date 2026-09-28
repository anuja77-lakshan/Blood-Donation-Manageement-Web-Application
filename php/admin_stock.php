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
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-box { background: #ffffff; padding: 35px; border-radius: 12px; border: 1px solid #e2e8f0; width: 100%; max-width: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; }
        h2 { color: #ef3446; margin-bottom: 8px; font-weight: 800; font-size: 1.5rem; }
        p { color: #64748b; font-size: 0.9rem; margin-bottom: 24px; }
        input[type="password"] { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; margin-bottom: 16px; outline: none; }
        input[type="password"]:focus { border-color: #ef3446; }
        .btn-submit { width: 100%; padding: 12px; background: #ef3446; color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; }
        .btn-submit:hover { background: #d92636; }
        .error { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 6px; font-size: 0.85rem; margin-bottom: 16px; font-weight: 600; }
    </style>
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
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: #f8fafc; padding: 40px 20px; color: #0f1115; }
        .admin-container { max-width: 750px; margin: 0 auto; background: #ffffff; padding: 35px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #e2e8f0; }
        .user-tag { font-size: 0.9rem; color: #64748b; }
        .logout-btn { background: #fee2e2; color: #ef3446; text-decoration: none; padding: 6px 14px; border-radius: 6px; font-weight: 700; font-size: 0.85rem; }
        .logout-btn:hover { background: #fca5a5; }
        h2 { font-size: 1.6rem; color: #ef3446; margin-bottom: 8px; font-weight: 800; }
        p { color: #64748b; font-size: 0.95rem; margin-bottom: 25px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .stock-control { background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; }
        .stock-header { display: flex; justify-content: space-between; font-weight: 700; margin-bottom: 10px; font-size: 1.1rem; }
        .stock-slider { width: 100%; accent-color: #ef3446; cursor: pointer; }
        .btn-save { margin-top: 25px; width: 100%; padding: 14px; background: #ef3446; color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: 0.2s ease; }
        .btn-save:hover { background: #d92636; }
        .success-msg { display: none; margin-top: 15px; padding: 12px; background: #dcfce7; color: #166534; border-radius: 6px; text-align: center; font-weight: 600; font-size: 0.95rem; }
    </style>
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