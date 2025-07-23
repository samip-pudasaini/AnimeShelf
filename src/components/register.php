<?php
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode([
    'success' => false,
    'message' => 'Invalid request method.'
  ]);
  exit();
}

// Decode JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['username']) || !isset($input['password'])) {
  echo json_encode([
    'success' => false,
    'message' => 'Username and password are required.'
  ]);
  exit();
}

$username = $input['username'];
$password = $input['password'];
$email = $input['email'];
$dob = $input['dob'];

// DB connection
$conn = mysqli_connect(
  'feenix-mariadb.swin.edu.au',
  's104507107',
  '020704',
  's104507107_db'
);

if (!$conn) {
  echo json_encode([
    'success' => false,
    'message' => 'Database connection failed.'
  ]);
  exit();
}

mysqli_set_charset($conn, 'utf8');

// Escape user inputs
$username = mysqli_real_escape_string($conn, $username);
$password = mysqli_real_escape_string($conn, $password);
$email = mysqli_real_escape_string($conn, $email);
$dob = mysqli_real_escape_string($conn, $dob);

// Check if username already exists
$checkQuery = "SELECT COUNT(*) FROM users WHERE username = '$username'";
$checkResult = mysqli_query($conn, $checkQuery);
$count = mysqli_fetch_array($checkResult)[0];

if ($count > 0) {
  echo json_encode([
    'success' => false,
    'message' => 'Username already exists.'
  ]);
  mysqli_close($conn);
  exit();
}

// Insert new user
$insertQuery = "INSERT INTO users (username, password, email, dob) VALUES ('$username', '$password', '$email', '$dob')";
if (mysqli_query($conn, $insertQuery)) {
  echo json_encode([
    'success' => true,
    'message' => 'Registration successful.'
  ]);
} else {
  echo json_encode([
    'success' => false,
    'message' => 'Failed to register user.'
  ]);
}

mysqli_close($conn);
