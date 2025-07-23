<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
  exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['user_id']) || empty($input['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'User ID is required.']);
  exit();
}

$conn = mysqli_connect('feenix-mariadb.swin.edu.au', 's104507107', '020704', 's104507107_db');

if (!$conn) {
  echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
  exit();
}

mysqli_set_charset($conn, 'utf8');

$user_id = mysqli_real_escape_string($conn, $input['user_id']);

// Fetch liked anime by this user
$userLikesQuery = "SELECT anime_id FROM user_likes WHERE user_id = '$user_id'";
$userLikesResult = mysqli_query($conn, $userLikesQuery);

if ($userLikesResult === false) {
  echo json_encode(['success' => false, 'message' => 'Query failed: ' . mysqli_error($conn)]);
  mysqli_close($conn);
  exit();
}

$userLikes = [];
while ($row = mysqli_fetch_assoc($userLikesResult)) {
  $userLikes[] = ['anime_id' => $row['anime_id']];
}

// Fetch like counts for all anime
$likeCountsQuery = "SELECT anime_id, COUNT(*) AS like_count FROM user_likes GROUP BY anime_id";
$likeCountsResult = mysqli_query($conn, $likeCountsQuery);

$likeCounts = [];
if ($likeCountsResult) {
  while ($row = mysqli_fetch_assoc($likeCountsResult)) {
    $likeCounts[$row['anime_id']] = (int)$row['like_count'];
  }
}

echo json_encode([
  'success' => true,
  'likes' => $userLikes,
  'likeCounts' => $likeCounts
]);

mysqli_close($conn);
?>
