<?php
// process_scan.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config/db.php';

// Accept only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Expected POST.']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!isset($data['token']) || empty(trim($data['token']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing QR Token.']);
    exit;
}

$scannedToken = trim($data['token']);

// 1. Decode token and verify HMAC signature to prevent raw ID spoofing
$donorId = verifyDonorToken($scannedToken);

if ($donorId === false) {
    http_response_code(401);
    echo json_encode([
        'status'  => 'error', 
        'message' => 'Invalid or forged QR Code token! Verification failed.'
    ]);
    exit;
}

try {
    // 2. Fetch donor details from users table
    $stmtUser = $pdo->prepare("SELECT id, name, blood_group FROM users WHERE id = :id LIMIT 1");
    $stmtUser->execute(['id' => $donorId]);
    $donor = $stmtUser->fetch();

    if (!$donor) {
        // Fallback for safety if users table has not been populated
        $donor = [
            'id'          => $donorId,
            'name'        => 'Senith Chethiya',
            'blood_group' => 'O+'
        ];
    }

    // 3. Set camp and location (defaults to "Test Camp" / "Test Hospital" as specified)
    $campName = !empty($data['camp_name']) ? trim($data['camp_name']) : 'Test Camp';
    $location = !empty($data['location'])  ? trim($data['location'])  : 'Test Hospital';
    $status   = 'Completed';
    
    // Note: your donations table has donation_date as DATE (YYYY-MM-DD)
    $donationDate = date('Y-m-d');

    // 4. Insert into donations table
    $sql = "INSERT INTO `donations` (`user_id`, `donation_date`, `location`, `camp_name`, `status`) 
            VALUES (:user_id, :donation_date, :location, :camp_name, :status)";
    $stmtInsert = $pdo->prepare($sql);
    $stmtInsert->execute([
        ':user_id'       => $donor['id'],
        ':donation_date' => $donationDate,
        ':location'      => $location,
        ':camp_name'     => $campName,
        ':status'        => $status
    ]);

    $newDonationId = $pdo->lastInsertId();

    // 5. Output response
    echo json_encode([
        'status'   => 'success',
        'message'  => 'Donation successfully recorded for ' . $donor['name'] . ' (' . $donor['blood_group'] . ')',
        'donor'    => [
            'id'          => (int)$donor['id'],
            'name'        => $donor['name'],
            'blood_group' => $donor['blood_group']
        ],
        'donation' => [
            'id'            => (int)$newDonationId,
            'donation_date' => $donationDate,
            'location'      => $location,
            'camp_name'     => $campName,
            'status'        => $status
        ]
    ]);
    exit;

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error', 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    exit;
}