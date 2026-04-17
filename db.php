<?php
$servername = "localhost";
$username = "root";
$password = ""; // or your password
$dbname = "astroo";

$dbconn = new mysqli($servername, $username, $password, $dbname);

if ($dbconn->connect_error) {
  die("Connection failed: " . $dbconn->connect_error);
}
?>
