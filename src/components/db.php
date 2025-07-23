<?php
$servername = "feenix-mariadb.swin.edu.au";
$username = "s104507107";
$password = "020704";
$dbname = "s104507107_db";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
  die();
}

 ?>