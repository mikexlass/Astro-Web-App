<?php
session_start();
$conn = new mysqli("localhost", "root", "", "astroo");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = $_SESSION['subscribe_id'] ?? null;
if (!$id) die("❌ Invalid or missing subscription ID.");

$query = $conn->query("SELECT * FROM subscriptions WHERE subscribe_id = $id");
if (!$query || $query->num_rows === 0) die("❌ Subscription not found.");
$subscription = $query->fetch_assoc();

$packageName = $subscription['package'];
$packageEsc = $conn->real_escape_string($packageName);
$pkgQuery = $conn->query("SELECT name, price FROM packages WHERE name = '$packageEsc'");
if (!$pkgQuery || $pkgQuery->num_rows === 0) die("❌ Package info not found.");
$pkg = $pkgQuery->fetch_assoc();
$base_price = floatval($pkg['price']);

$addonText = $subscription['addonpack'];
$addon = 0.00;
if (!empty($addonText)) {
  preg_match_all('/RM(\d+)/', $addonText, $matches);
  foreach ($matches[1] as $val) $addon += floatval($val);
}

$sst = ($base_price + $addon) * 0.06;
$grand_total = $base_price + $addon + $sst;
$payment_number = "PMT" . rand(10000, 99999);
$bill_date = date("Y-m-d");
$staff_id = "STF1001";
?>

<html>
<head>
  <title>Payment Page</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; padding: 20px; }
    .payment-box {
      background: white;
      padding: 30px;
      max-width: 600px;
      margin: auto;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 { color: #e6007e; }
    label { font-weight: bold; }
    p { margin: 5px 0; }
    button {
      background: #e6007e;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }
    select, input[type="hidden"] {
      padding: 8px;
      width: 100%;
      margin-bottom: 20px;
      box-sizing: border-box;
    }
  </style>
</head>
<body>
<script>
function confirmPayment() {
  if (confirm("Do you want to continue payment?")) {
    document.getElementById('paymentForm').submit();
  } else {
    window.location.href = "dashboard.html";
  }
}
</script>

<div class="payment-box">
  <h2>Payment Summary</h2>
  <p><label>Subscription ID:</label> <?= htmlspecialchars($id) ?></p>
  <p><label>Package:</label> <?= htmlspecialchars($packageName) ?></p>
  <p><label>Payment No:</label> <?= htmlspecialchars($payment_number) ?></p>
  <p><label>Date:</label> <?= htmlspecialchars($bill_date) ?></p>
  <p><label>Staff ID:</label> <?= htmlspecialchars($staff_id) ?></p>
  <p><label>Base Fee:</label> RM <?= number_format($base_price, 2) ?></p>
  <p><label>Add-ons:</label> RM <?= number_format($addon, 2) ?></p>
  <p><label>SST (6%):</label> RM <?= number_format($sst, 2) ?></p>
  <p><label>Total Payment:</label> <strong>RM <?= number_format($grand_total, 2) ?></strong></p>

  <form id="paymentForm" method="post" action="confirm_payment.php">
    <label for="payment_method">Choose Payment Method:</label>
    <select name="payment_method" required>
      <option value="Visa">Visa</option>
      <option value="Mastercard">Mastercard</option>
      <option value="FPX">FPX</option>
    </select>

    <input type="hidden" name="subscribe_id" value="<?= $id ?>">
    <input type="hidden" name="payment_number" value="<?= $payment_number ?>">
    <input type="hidden" name="payment_date" value="<?= $bill_date ?>">
    <input type="hidden" name="staff_id" value="<?= $staff_id ?>">
    <input type="hidden" name="total_fee" value="<?= $grand_total ?>">

    <button type="button" onclick="confirmPayment()">Pay Now</button>
  </form>
</div>
</body>
</html>
