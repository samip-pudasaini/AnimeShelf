<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
  exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['user_id']) || !isset($input['anime_id']) || !isset($input['status'])) {
  echo json_encode(['success' => false, 'message' => 'User ID, anime ID, and status are required.']);
  exit();
}

$conn = mysqli_connect('feenix-mariadb.swin.edu.au', 's104507107', '020704', 's104507107_db');

if (!$conn) {
  echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
  exit();
}

mysqli_set_charset($conn, 'utf8');

$user_id = mysqli_real_escape_string($conn, $input['user_id']);
$anime_id = mysqli_real_escape_string($conn, $input['anime_id']);
$status = mysqli_real_escape_string($conn, $input['status']);

if (!in_array($status, ['watching', 'completed', 'plan_to_watch'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
  mysqli_close($conn);
  exit();
}

$query = "UPDATE user_anime SET status = '$status' WHERE user_id = '$user_id' AND anime_id = '$anime_id'";
if (mysqli_query($conn, $query)) {
  echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to update status: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>