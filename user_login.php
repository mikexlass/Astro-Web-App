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
$astro_id = $_POST['astro_id'] ?? '';
$password = $_POST['password'] ?? '';

// Check user in database
$stmt = $conn->prepare("SELECT c.*, s.status as sub_status, s.end_date 
                        FROM customer c 
                        LEFT JOIN subscriptions s ON c.id = s.customer_id 
                        WHERE c.astro_id = ?");
$stmt->bind_param("s", $astro_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Check password
    if (password_verify($password, $user['password'])) {
        
        // Check if blocked
        if ($user['sub_status'] == 'blocked') {
            echo "<script>alert('Your account has been blocked. Please contact admin.'); window.location.href = 'Login.html';</script>";
            exit();
        }
        
        // Check if subscription expired
        if ($user['sub_status'] == 'expired' || ($user['end_date'] && strtotime($user['end_date']) < time())) {
            echo "<script>alert('Your subscription has expired. Please renew.'); window.location.href = 'choose_plan.html';</script>";
            exit();
        }
        
        // Login success
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['astro_id'] = $user['astro_id'];
        $_SESSION['full_name'] = $user['full_name'];
        
        echo "<script>alert('Welcome, " . $user['full_name'] . "!'); window.location.href = 'mainpage.html';</script>";
        exit();
        
    } else {
        // Wrong password
        echo "<script>alert('Incorrect password.'); window.location.href = 'Login.html';</script>";
        exit();
    }
    
} else {
    // User not found
    echo "<script>alert('Astro ID not found.'); window.location.href = 'Login.html';</script>";
    exit();
}

$stmt->close();
$conn->close();
?>