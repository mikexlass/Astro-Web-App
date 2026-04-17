<?php
$conn = new mysqli("localhost", "root", "", "astroo");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$subscribe_id = $_GET['subscribe_id'] ?? '';
$subscribe_id = $conn->real_escape_string($subscribe_id);

$sql = "SELECT s.*, p.name AS package_name, p.price AS base_price, py.payment_date, 
py.payment_method, py.payment_status
          FROM subscriptions s
          JOIN packages p ON s.package = p.name
          LEFT JOIN payments py ON s.subscribe_id = py.subscribe_id
          WHERE s.subscribe_id = '$subscribe_id'";

$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
echo "<p>No receipt found for this subscription.</p>";
exit();
}

$row = $result->fetch_assoc();

$addon_total = 0;
$addon_list = "-";
if (!empty($row['addonpack'])){
$addon_list = $row['addonpack'];
$addons = explode(", ", $row['addonpack']);
foreach ($addons as $a) {
if (preg_match('/RM(\d+)/', $a, $m)) {
$addon_total += floatval($m[1]);
}
}
}

$sst = ($row['base_price'] + $addon_total) * 0.06;
$total = $row['base_price'] + $addon_total + $sst;
?>


<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Receipt</title>
<style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f9f9f9;
      padding: 30px;
    }

    .container {
      max-width: 700px;
      margin: auto;
    }

    .top-buttons {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .btn {
      padding: 10px 18px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }

    .btn-print {
      background-color: #e6007e;
      color: white;
    }

    .btn-home {
      background-color: #e6007e;
      color: white;
    }

    .receipt {
      background: #fff0f6;
      border: 1px solid #e6cce0;
      border-radius: 12px;
      padding: 30px;
    }

    .receipt h2 {
      text-align: center;
      color: #cc0066;
      margin-bottom: 25px;
    }

    .receipt table {
      width: 100%;
      border-collapse: collapse;
    }

    .receipt td {
      padding: 10px 0;
      vertical-align: top;
    }

    .label {
      width: 45%;
      font-weight: bold;
      color: #444;
    }

    .value {
      width: 55%;
      color: #222;
    }

    .total-row {
      border-top: 1px dashed #999;
      margin-top: 10px;
      padding-top: 10px;
    }
</style>
</head>
<body>
<div class="container">
<div class="top-buttons">
<a href="customer_list_staff.html"><button class="btn btn-home">🏠 Home</button></a>
<button class="btn btn-print" onclick="window.print()">🖨 Print Receipt</button>
</div>

<div class="receipt">
<h2>📄 Astro Subscription Receipt</h2>
<table>
<tr><td class="label">Customer Name:</td><td class="value"><?= htmlspecialchars(
$row['cust_name']) ?></td></tr>
<tr><td class="label">Email:</td><td class="value"><?= $row['cust_email'] ?>
</td></tr>
<tr><td class="label">Subscription ID:</td><td class="value"><?= $row[
'subscribe_id'] ?></td></tr>
<tr><td class="label">Package:</td><td class="value"><?= $row['package_name'] ?>
(RM <?= number_format($row['base_price'], 2) ?>)</td></tr>
<tr><td class="label">Package Type:</td><td class="value"><?= $row['package_type'
] ?></td></tr>
<tr><td class="label">Add-on:</td><td class="value"><?= $addon_list ?></td></tr>
<tr><td class="label">Add-on Total:</td><td class="value">RM <?= number_format(
$addon_total, 2) ?></td></tr>
<tr><td class="label">SST (6%):</td><td class="value">RM <?= number_format($sst,
2) ?></td></tr>
<tr class="total-row"><td class="label">Total Amount:</td><td class="value"
><strong>RM <?= number_format($total, 2) ?></strong></td></tr>
<tr><td class="label">Payment Date:</td><td class="value"><?= $row['payment_date'
] ?: '-' ?></td></tr>
<tr><td class="label">Payment Method:</td><td class="value"><?= $row[
'payment_method'] ?: '-' ?></td></tr>
<tr><td class="label">Payment Status:</td><td class="value"><?= strtoupper($row[
'payment_status']) ?></td></tr>
</table>
</div>
</div>
</body>
</html>
