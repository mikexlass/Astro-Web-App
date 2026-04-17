<!-- customer_subscription.php -->
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "astroo");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$cust_email = $_SESSION['cust_email'] ?? null;
if (!$cust_email) {
echo "<script>alert('Session not found. Please login again.'); 
window.location.href='Login.html';</script>";
exit();
}

$cust_email = $conn->real_escape_string($cust_email);

$sql = "SELECT s.*, p.name AS package_name, p.price AS base_price, py.payment_date, 
py.payment_status 
        FROM subscriptions s
        JOIN packages p ON s.package = p.name
        LEFT JOIN payments py ON s.subscribe_id = py.subscribe_id
        WHERE s.cust_email = '$cust_email'
        ORDER BY s.subscribe_id DESC";

$result = $conn->query($sql);
if (!$result) die("SQL Error: " . $conn->error);
?>


<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Subscriptions</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white font-sans p-6 min-h-screen">
<div class="flex justify-between items-center mb-6">
<div class="flex items-center space-x-4">
<img src="astro_logoo.png" alt="Astro Logo" class="h-12">
<h1 class="text-3xl font-bold text-pink-500">Your Subscriptions</h1>
</div>
<a href="dashboard.html" class="bg-pink-600 text-white px-4 py-2 rounded 
hover:bg-yellow-400 transition">🏠 Home</a>
</div>

<div class="max-w-5xl mx-auto">
<?php

$conn = new mysqli("localhost", "root", "", "astroo");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$cust_email = $_SESSION['cust_email'] ?? null;
if (!$cust_email) {
echo "<script>alert('Please login first'); 
window.location.href='login.html';</script>";
exit();
}

$result = $conn->query("SELECT * FROM subscriptions WHERE cust_email = '$cust_email' 
ORDER BY subscribe_id DESC");

while ($row = $result->fetch_assoc()):
$id = $row['subscribe_id'];
$status = $row['payment_status'];
$cardColor = $status === 'paid' ? 'bg-gray-800' : 'bg-pink-800';
?>
<div class="<?= $cardColor ?> rounded-lg p-5 mb-6 shadow-lg">
<p class="text-lg font-bold">📦 Package: <?= htmlspecialchars($row['package']) ?> (
<?= htmlspecialchars($row['package_type']) ?>)</p>
<p class="text-sm mb-2">🧾 Subscription ID: <?= $id ?></p>
<p class="mb-1">➕ Add-ons: <?= htmlspecialchars($row['addonpack'] ?: '-') ?></p>
<p class="mb-1">💡 Status: 
<?php if ($status == 'paid'): ?>
<span class="text-green-400 font-semibold">Paid</span>
<?php else: ?>
<span class="text-yellow-300 font-semibold">Pending</span>
<?php endif; ?>
</p>

<?php if ($status == 'paid'):
$pay = $conn->query("SELECT * FROM payments WHERE subscribe_id = $id ORDER BY 
payment_date DESC LIMIT 1");
if ($pay && $pay->num_rows > 0):
$p = $pay->fetch_assoc();
?>
<div class="text-sm mt-3">
<p>📅 Paid on: <?= $p['payment_date'] ?></p>
<p>🔢 Payment No: <?= $p['payment_number'] ?></p>
<p>💳 Method: <?= $p['payment_method'] ?></p>
<p>💰 Total Paid: <strong>RM <?= number_format($p['total_fee'], 2) ?>
</strong></p>
</div>
<?php endif; ?>

<?php else: ?>
<form action="update_subscribe.html" method="post" class="mt-3">
<input type="hidden" name="subscribe_id" value="<?= $id ?>">
<button type="submit" class="bg-yellow-400 text-black font-semibold px-4 py-2 
rounded hover:bg-yellow-300">Pay Now</button>
</form>
<?php endif; ?>
</div>
<?php endwhile; $conn->close(); ?>
</div>
</body>
</html>
