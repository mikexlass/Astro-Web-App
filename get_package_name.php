<!-- get_package_name.php -->
<?php
include 'db.php';

header('Content-Type: application/json');

$result = mysqli_query($dbconn, "SELECT name, price, description, contract, channels FROM packages");

$packages = [];

while ($row = mysqli_fetch_assoc($result)) {
$packages[] = [
"name" => $row['name'],
"price" => $row['price'],
"description" => $row['description'],
"contract" => $row['contract'],
"channels" => $row['channels']
];
}

echo json_encode($packages);
?>
