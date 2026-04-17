<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $code = $_POST['code'];
  $description = $_POST['description'];
  $price = floatval($_POST['price']);
  $contract = $_POST['contract'];
  $categoryArr = $_POST['category'] ?? [];
  $category = implode(", ", $categoryArr);
  $channels = $_POST['channels'];

  $target_dir = "uploads/";
  if (!is_dir($target_dir)) {
    mkdir($target_dir);
  }

  $image_url = "";
  if (!empty($_FILES["image"]["name"])) {
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . time() . "_" . $image_name;
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
      $image_url = $target_file;
    }
  }

  $sql = "INSERT INTO packages (name, code, description, price, contract, category, image_url, channels)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = mysqli_prepare($dbconn, $sql);
  mysqli_stmt_bind_param($stmt, "sssdssss", $name, $code, $description, $price, $contract, $category, $image_url, $channels);

  if (mysqli_stmt_execute($stmt)) {
    echo "<script>
    alert('✅ Package successfully added.');
    window.location.href = 'admin_dashboard.html';
    </script>";
  } else {
    echo "<script>
    alert('❌ Failed to save package.');
    window.location.href = 'admin_dashboard.html';
    </script>";
  }

  mysqli_stmt_close($stmt);
}
?>
