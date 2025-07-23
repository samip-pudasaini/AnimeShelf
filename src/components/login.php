<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode([
    'success' => false,
    'message' => 'Invalid request method.'
  ]);
  exit();
}

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

$username = mysqli_real_escape_string($conn, $username);
$password = mysqli_real_escape_string($conn, $password);

$query = "SELECT id FROM users WHERE username = '$username' AND password = '$password'";
$checkResult = mysqli_query($conn, $query);

if (mysqli_num_rows($checkResult) === 0) {
  echo json_encode([
    'success' => false,
    'message' => 'Invalid username or password.'
  ]);
  mysqli_close($conn);
  exit();
}

$user = mysqli_fetch_assoc($checkResult);
$token = bin2hex(openssl_random_pseudo_bytes(16));

echo json_encode([
  'success' => true,
  'message' => 'Login successful.',
  'token' => $token,
  'user_id' => $user['id']
]);

mysqli_close($conn);
?>