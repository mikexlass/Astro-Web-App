<?php
include 'db.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])){
  echo json_encode(["error" => "ID not provided"]);
  exit;
}

$id = intval($_GET['id']);
$query = mysqli_query($dbconn, "SELECT * FROM packages WHERE id = $id");

if (mysqli_num_rows($query) === 0) {
  echo json_encode(["error" => "Package not found"]);
  exit;
}

$row = mysqli_fetch_assoc($query);
echo json_encode($row);
?>
