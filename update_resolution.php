<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "astroo";

$conn = new mysqli($servername, $username, $password, $dbname);

// Get POST data
$customer_id = intval($_POST['customer_id'] ?? 0);
$max_resolution = $_POST['max_resolution'] ?? '1080';

// Validate resolution
$allowed = ['720', '1080', '4k'];
if (!in_array($max_resolution, $allowed)) {
    $max_resolution = '1080';
}

// Update database
$stmt = $conn->prepare("UPDATE subscriptions SET max_resolution = ? WHERE customer_id = ?");
$stmt->bind_param("si", $max_resolution, $customer_id);
$stmt->execute();

$stmt->close();
$conn->close();

// Redirect back to dashboard
header("Location: admin_dashboard.php");
exit();
?>