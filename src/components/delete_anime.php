<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
  exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['user_id']) || !isset($input['anime_id'])) {
  echo json_encode(['success' => false, 'message' => 'User ID and anime ID are required.']);
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

$query = "DELETE FROM user_anime WHERE user_id = '$user_id' AND anime_id = '$anime_id'";
if (mysqli_query($conn, $query)) {
  echo json_encode(['success' => true, 'message' => 'Anime removed successfully.']);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to remove anime: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>