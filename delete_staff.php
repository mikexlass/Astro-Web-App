<!-- delete_staff.php -->
<?php
include 'db.php';

if (!isset($_GET['id'])){
echo "<script>alert('Invalid staff ID'); window.location.href = 
'staff_list.html';</script>";
exit();
}

$staff_id = $_GET['id'];
$sql = "DELETE FROM staff WHERE staff_id = ?";
$stmt = $dbconn->prepare($sql);
$stmt->bind_param("s", $staff_id);

if ($stmt->execute()) {
echo "<script>alert('Staff deleted successfully.'); window.location.href = 
'staff_list.html';</script>";
} else {
echo "<script>alert('Failed to delete staff: {$stmt->error}'); window.location.href 
= 'staff_list.html';</script>";
}

$stmt->close();
$dbconn->close();
?>
