<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $staff_id = $_POST['staff_id'];
    $staff_name = $_POST['staff_name'];
    $ic_passport = $_POST['ic_passport'];
    $position_id = $_POST['position_id'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob_year'] . '-' . $_POST['dob_month'] . '-' . $_POST['dob_day'];
    $password = $_POST['password'];

    $sql = "INSERT INTO staff (staff_id, staff_name, ic_passport, position_id, contact, address, email, gender, dob, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param("ssssssssss", $staff_id, $staff_name, $ic_passport, $position_id, $contact, $address, $email, $gender, $dob, $password);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Staff added successfully.'); window.location.href='staff_dashboard.html';</script>";
    } else {
        echo "<script>alert('❌ Failed to add staff.'); window.location.href='add_admin.html';</script>";
    }

    $stmt->close();
    $dbconn->close();
}
?>
