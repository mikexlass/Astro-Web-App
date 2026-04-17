<?php
$conn = new mysqli("localhost", "root", "", "astroo");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id']) && is_numeric($_GET['id'])){
  $id = (int) $_GET['id'];

  $stmt = $conn->prepare("DELETE FROM subscriptions WHERE subscribe_id = ?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()){
    echo "<script>alert('Customer removed successfully!'); 
window.location.href='customer_list_staff.html';</script>";
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
} else {
  echo "<script>alert('Invalid or missing ID.'); 
window.location.href='customer_list_staff.html';</script>";
}

$conn->close();
?>
