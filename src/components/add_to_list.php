<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
  exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['user_id']) || !isset($input['mal_id']) || !isset($input['status'])) {
  echo json_encode(['success' => false, 'message' => 'User ID, anime ID, and status are required.']);
  exit();
}

$conn = mysqli_connect('feenix-mariadb.swin.edu.au', 's104507107', '020704', 's104507107_db');

if (!$conn) {
  echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
  exit();
}

mysqli_set_charset($conn, 'utf8');

// Fetch the anime title from Jikan API
$anime_id = $input['mal_id'];
$apiUrl = "https://api.jikan.moe/v4/anime/{$anime_id}";
$apiResponse = @file_get_contents($apiUrl); // Suppress warnings
$title = "Anime ID {$anime_id}"; // Fallback title
if ($apiResponse) {
  $apiData = json_decode($apiResponse, true);
  if (isset($apiData['data']['title'])) {
    $title = $apiData['data']['title'];
  }
}

$user_id = mysqli_real_escape_string($conn, $input['user_id']);
$anime_id = mysqli_real_escape_string($conn, $input['mal_id']);
$status = mysqli_real_escape_string($conn, $input['status']);
$title = mysqli_real_escape_string($conn, $title); // Escape the title

if (!in_array($status, ['watching', 'completed', 'plan_to_watch'])) {
  $status = 'plan_to_watch'; // Fallback to default
}

$query = "INSERT INTO user_anime (user_id, anime_id, status, title) VALUES ('$user_id', '$anime_id', '$status', '$title')";
if (mysqli_query($conn, $query)) {
  echo json_encode(['success' => true, 'message' => 'Anime added to list.']);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to add anime: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>