<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "astroo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get login data
$input_user = $_POST['username'] ?? '';
$input_pass = $_POST['password'] ?? '';

// Check admin in database
$stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->bind_param("s", $input_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    
    // Check password (plain text for now, or use password_verify if hashed)
    if ($input_pass === $admin['password']) {
        $_SESSION['admin'] = true;
        $_SESSION['admin_user'] = $input_user;
        header("Location: admin_dashboard.php");
        exit();
    }
}

// Login failed
header("Location: admin_login.html?error=1");
exit();
?>