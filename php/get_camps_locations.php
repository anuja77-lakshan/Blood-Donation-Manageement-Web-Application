<?php
error_reporting(0);
header('Content-Type: application/json');

require_once 'db.php';

if ($conn->connect_error) {
    echo json_encode([]);
    exit();
}

// If No Columns It's auto add
$conn->query("ALTER TABLE blood_camps ADD COLUMN IF NOT EXISTS latitude DECIMAL(10, 8) NULL");
$conn->query("ALTER TABLE blood_camps ADD COLUMN IF NOT EXISTS longitude DECIMAL(11, 8) NULL");

// Read Active camps and camps ended within 24 hours
$sql = "SELECT id, camp_name, org_name, camp_date, start_time, end_time, location, latitude, longitude,
        TIMESTAMPDIFF(MINUTE, NOW(), TIMESTAMP(camp_date, end_time)) AS minutes_left 
        FROM blood_camps 
        WHERE TIMESTAMP(camp_date, end_time) >= NOW() - INTERVAL 24 HOUR 
        ORDER BY camp_date ASC";

$result = $conn->query($sql);
$camps = [];

$cityMap = [
    'colombo' => [6.9271, 79.8612],
    'narahenpita' => [6.8941, 79.8776],
    'kandy' => [7.2906, 80.6337],
    'peradeniya' => [7.2600, 80.5977],
    'galle' => [6.0535, 80.2210],
    'karapitiya' => [6.0652, 80.2246],
    'matara' => [5.9549, 80.5550],
    'kurunegala' => [7.4863, 80.3623],
    'anuradhapura' => [8.3349, 80.4035],
    'jaffna' => [9.6647, 80.0167],
    'badulla' => [6.9895, 81.0557],
    'ratnapura' => [6.6961, 80.3956],
    'batticaloa' => [7.7170, 81.7001],
    'gampaha' => [7.0840, 79.9943],
    'negombo' => [7.2008, 79.8736],
    'kalutara' => [6.5854, 79.9607]
];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lat = !empty($row['latitude']) ? floatval($row['latitude']) : null;
        $lng = !empty($row['longitude']) ? floatval($row['longitude']) : null;
        $locationRaw = strtolower(trim($row['location']));

        if (!$lat || !$lng) {
            foreach ($cityMap as $city => $coords) {
                if (strpos($locationRaw, $city) !== false) {
                    $lat = $coords[0];
                    $lng = $coords[1];
                    break;
                }
            }
        }

        if (!$lat || !$lng) {
            $lat = 6.9271 + (mt_rand(-30, 30) / 1000);
            $lng = 79.8612 + (mt_rand(-30, 30) / 1000);
        }

        $campId = (int)$row['id'];
        $conn->query("UPDATE blood_camps SET latitude = $lat, longitude = $lng WHERE id = $campId");

        $row['latitude'] = $lat;
        $row['longitude'] = $lng;
        $camps[] = $row;
    }
}

echo json_encode($camps);
$conn->close();
?>