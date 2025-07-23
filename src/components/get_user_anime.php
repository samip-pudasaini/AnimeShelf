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

// Fetch username from users table
$username_query = "SELECT username FROM users WHERE id = '$user_id'";
$username_result = mysqli_query($conn, $username_query);

if (!$username_result || mysqli_num_rows($username_result) === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    mysqli_close($conn);
    exit();
}

$user = mysqli_fetch_assoc($username_result);
$username = $user['username'];

// Fetch anime list
$query = "SELECT anime_id, status, title FROM user_anime WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Query failed: ' . mysqli_error($conn)]);
    mysqli_close($conn);
    exit();
}

$anime = [];
while ($row = mysqli_fetch_assoc($result)) {
    $anime[] = [
        'anime_id' => $row['anime_id'],
        'status' => $row['status'] ?: 'plan_to_watch',
        'title' => $row['title'] ?: "Anime ID {$row['anime_id']}"
    ];
}

echo json_encode([
    'success' => true,
    'username' => $username, // Include username in response
    'anime' => $anime
]);

mysqli_close($conn);
?>