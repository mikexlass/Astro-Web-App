<!-- login.php -->
<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "astroo");

if (!$conn) {
die("Could not connect to database");
}

if (isset($_POST['Login'])){
$email = $_POST['email'];
$password = $_POST['password'];

$sql_admin = "SELECT * FROM admin WHERE admin_email = '$email'";
$result_admin = mysqli_query($conn, $sql_admin);

if (mysqli_num_rows($result_admin) > 0) {
$admin = mysqli_fetch_assoc($result_admin);
if ($password === $admin['admin_password']) {
$_SESSION['admin_email'] = $admin['admin_email'];
echo "<script>alert('Welcome, Admin!'); window.location.href = 
'admin_dashboard.html';</script>";
exit();
} else {
echo "<script>alert('Incorrect admin password.');
                    window.location.href = 'Login.html';</script>";
exit();
}
}

$sql_staff = "SELECT * FROM staff WHERE email = '$email'";
$result_staff = mysqli_query($conn, $sql_staff);

if (mysqli_num_rows($result_staff) > 0) {
$staff = mysqli_fetch_assoc($result_staff);
if ($password === $staff['password']) {
$_SESSION['staff_id'] = $staff['staff_id'];
$_SESSION['staff_email'] = $staff['email'];
echo "<script>alert('Welcome, Staff!'); window.location.href = 
'staff_dashboard.html';</script>";
exit();
} else {
echo "<script>alert('Incorrect staff password.');
                    window.location.href = 'Login.html';</script>";
exit();
}
}

$sql_customer = "SELECT * FROM customer WHERE cust_email = '$email'";
$result_customer = mysqli_query($conn, $sql_customer);

if (mysqli_num_rows($result_customer) > 0) {
$cust = mysqli_fetch_assoc($result_customer);
if ($password === $cust['cust_password']) {
$_SESSION['cust_email'] = $cust['cust_email'];
$_SESSION['cust_name'] = $cust['cust_name'];
echo "<script>alert('Welcome, Customer!'); window.location.href = 
'dashboard.html';</script>";
exit();
} else {
echo "<script>alert('Incorrect customer password.');
                    window.location.href = 'Login.html';</script>";
exit();
}
}

echo "<script>alert('No account found with this email.');
            window.location.href = 'Login.html';</script>";
}
?>
