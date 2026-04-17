<?php
include 'db.php';

header('Content-Type: application/json');

$result = mysqli_query($dbconn, "SELECT * FROM packages");

$packages = [];

while ($row = mysqli_fetch_assoc($result)) {
  $packages[] = [
    "id" => $row['id'],
    "name" => $row['name'],
    "code" => $row['code'],
    "description" => $row['description'],
    "price" => $row['price'],
    "contract" => $row['contract'],
    "category" => array_map('trim', explode(",", $row['category'])), // jadi array
    "image_url" => $row['image_url'],
    "channels" => array_map('trim', explode("-", $row['channels'])) // jadi array
  ];
}

echo json_encode($packages);
?>
