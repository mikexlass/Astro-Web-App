<!-- subscribe.php -->
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "astroo");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
$name = $_POST['name'];
$ic = $_POST['ic'];
$contact = $_POST['contact'];
$email = $_POST['email'];
$gender = $_POST['gender'];
$birth = $_POST['dob'];
$address = $_POST['address'];
$package = $_POST['package'];
$type = $_POST['package_type'];
$accNo = $_POST['acc_number'];
$existing = $_POST['existing_subscriber'];
$addons = isset($_POST['addons']) ? implode(", ", $_POST['addons']) : "";

$status = "pending";

$stmt = $conn->prepare("INSERT INTO subscriptions (cust_name, cust_ic, cust_ctc, 
cust_email, cust_gender, cust_dob, cust_address, package, package_type, astro_id, 
subscribe_status, addonpack, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
?, ?, ?)");
$stmt->bind_param("sssssssssssss", $name, $ic, $contact, $email, $gender, $birth,
$address, $package, $type, $accNo, $existing, $addons, $status);

if ($stmt->execute())
{
$_SESSION['subscribe_id'] = $stmt->insert_id;
echo "<script>alert('Subscription successful!'); window.location.href = 
'payment.php';</script>";
}
else
{
echo "<script>alert('Database error: " . $conn->error . "'); 
window.history.back();</script>";
}

$stmt->close();
}
$conn->close();
?>
