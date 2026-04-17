<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'not_logged_in', 'max_resolution' => null]);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "astroo";

$conn = new mysqli($servername, $username, $password, $dbname);

$stmt = $conn->prepare("SELECT status, max_resolution FROM subscriptions WHERE customer_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $sub = $result->fetch_assoc();
    $response = [
        'status' => $sub['status'],
        'max_resolution' => $sub['max_resolution'] ?? '1080'
    ];
    
    if ($sub['status'] == 'blocked') {
        $response['status'] = 'blocked';
    } elseif ($sub['status'] != 'active') {
        $response['status'] = 'expired';
    }
    
    echo json_encode($response);
} else {
    echo json_encode(['status' => 'no_subscription', 'max_resolution' => null]);
}

$stmt->close();
$conn->close();
?>