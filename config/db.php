<?php
// config/db.php

$host    = 'localhost';
$db      = 'bloodlink_db';
$user    = 'root';
$pass    = ''; // Default XAMPP MySQL password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

// Server Secret Key for cryptographic HMAC token signing
define('BLOODLINK_APP_SECRET', 'BL00DL1NK_S3CUR3_HMAC_K3Y_2026_@RJT');

/**
 * Generates a signed tamper-proof token for a donor.
 * Output: base64(json({uid, ts, nonce})).hmac_sha256_signature
 */
function generateDonorToken(int $userId): string {
    $payload = [
        'uid'   => $userId,
        'ts'    => time(),
        'nonce' => bin2hex(random_bytes(6))
    ];
    $encodedPayload = base64_encode(json_encode($payload));
    $signature = hash_hmac('sha256', $encodedPayload, BLOODLINK_APP_SECRET);
    
    return $encodedPayload . '.' . $signature;
}

/**
 * Decodes and verifies the scanned QR token.
 * Prevents raw ID spoofing. Returns int user_id or false if tampered.
 */
function verifyDonorToken(string $token) {
    $parts = explode('.', trim($token));
    if (count($parts) !== 2) {
        return false;
    }

    [$encodedPayload, $signature] = $parts;

    // Verify HMAC signature in constant time
    $expectedSignature = hash_hmac('sha256', $encodedPayload, BLOODLINK_APP_SECRET);
    if (!hash_equals($expectedSignature, $signature)) {
        return false; // Signature mismatch! Tampered QR Code.
    }

    $data = json_decode(base64_decode($encodedPayload), true);
    if (!is_array($data) || empty($data['uid'])) {
        return false;
    }

    return (int)$data['uid'];
}