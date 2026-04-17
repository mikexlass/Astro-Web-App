<?php
session_start();

// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "astroo";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$astro_id = $_POST['astro_id'] ?? '';
$password_plain = $_POST['password'] ?? '';
$email = $_POST['email'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$plan = $_POST['plan'] ?? 'standard';

// Check required fields
if (empty($astro_id) || empty($password_plain)) {
    die("Astro ID and Password are required! <a href='sign_up_new.html'>Go back</a>");
}

// Hash the password for security
$hashed_password = password_hash($password_plain, PASSWORD_DEFAULT);

// Insert customer into database
$stmt = $conn->prepare("INSERT INTO customer (astro_id, password, email, full_name) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $astro_id, $hashed_password, $email, $full_name);

if ($stmt->execute()) {
    // Get the new customer ID
    $customer_id = $stmt->insert_id;
    
    // Map plan name to package_id
    $package_id = 2; // default standard
    if ($plan == 'basic') $package_id = 1;
    if ($plan == 'premium') $package_id = 3;
    
    // Calculate end date (1 month from now)
    $end_date = date('Y-m-d', strtotime('+1 month'));
    
    // Add subscription
    $sub_stmt = $conn->prepare("INSERT INTO subscriptions (customer_id, package_id, start_date, end_date, status) VALUES (?, ?, NOW(), ?, 'active')");
    $sub_stmt->bind_param("iis", $customer_id, $package_id, $end_date);
    $sub_stmt->execute();
    
    // Set session (keep user logged in)
    $_SESSION['user_id'] = $customer_id;
    $_SESSION['astro_id'] = $astro_id;
    
    // Success - redirect to main page
    header("Location: mainpage.html?subscribed=1");
    exit();
    
} else {
    // Error - probably duplicate Astro ID
    if ($conn->errno == 1062) {
        die("This Astro ID already exists! <a href='sign_up_new.html'>Try another</a>");
    } else {
        die("Error: " . $stmt->error);
    }
}

$stmt->close();
$conn->close();
?>