<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) {
    echo json_encode(['status' => 'error']);
    exit;
}

$album   = isset($_POST['album'])   ? trim($_POST['album'])   : '';
$photo   = isset($_POST['photo'])   ? trim($_POST['photo'])   : '';
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
$name    = isset($_POST['name'])    ? trim($_POST['name'])    : '';

if ($album !== '' && $photo !== '' && $comment !== '') {

    if ($name === '') $name = "Guest";

    $stmt = $conn->prepare("INSERT INTO photo_comments (album, photo, user_name, comment, is_read) VALUES (?, ?, ?, ?, 0)");
    if (!$stmt) {
        echo json_encode(['status' => 'error']);
        exit;
    }

    $stmt->bind_param("ssss", $album, $photo, $name, $comment);
    $ok = $stmt->execute();
    $stmt->close();

    echo json_encode(['status' => ($ok ? 'success' : 'error')]);
    exit;
}

echo json_encode(['status' => 'error']);
?>
