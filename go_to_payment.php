<!-- go_to_payment.php -->
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if (!empty($_POST['subscribe_id'])){
$_SESSION['subscribe_id'] = intval($_POST['subscribe_id']);
header("Location: payment.php");
exit();
} else {
die(❌ " Invalid request: Missing subscription ID.");
}
} else {
die(❌ " Invalid access method.");
}
?>
