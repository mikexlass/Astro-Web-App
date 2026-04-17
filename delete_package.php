<!-- delete_package.php -->
<?php
include 'db.php';
$id = $_GET['id'];
mysqli_query($dbconn, "DELETE FROM packages WHERE id = $id");
header("Location: staff_dashboard.html");
?>
