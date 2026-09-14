<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'db.php';

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get input values
    $campName     = isset($_POST['campName']) ? trim($_POST['campName']) : '';
    $orgName      = isset($_POST['orgName']) ? trim($_POST['orgName']) : '';
    $campDate     = isset($_POST['campDate']) ? $_POST['campDate'] : '';
    $startTime    = isset($_POST['startTime']) ? $_POST['startTime'] : '';
    $endTime      = isset($_POST['endTime']) ? $_POST['endTime'] : '';
    $campLocation = isset($_POST['campLocation']) ? trim($_POST['campLocation']) : '';

    // Default fallback image
    $imagePath = "../images/card1.png";

    // Handle image upload
    if (isset($_FILES['coverImage']) && $_FILES['coverImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        
        // Create upload folder if not exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['coverImage']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        // Validate image file type
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $targetPath  = $uploadDir . $newFileName;

            // Move uploaded file to uploads folder
            if (move_uploaded_file($_FILES['coverImage']['tmp_name'], $targetPath)) {
                $imagePath = $targetPath;
            }
        }
    }

    // Insert record into database using prepared statement
    $sql = "INSERT INTO blood_camps (camp_name, org_name, camp_date, start_time, end_time, location, cover_image) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Query prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssssss", $campName, $orgName, $campDate, $startTime, $endTime, $campLocation, $imagePath);

    // Execute query and redirect
    if ($stmt->execute()) {
        header("Location: ../camps.php");
        exit();
    } else {
        die("Data insert failed: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method!";
}
?>