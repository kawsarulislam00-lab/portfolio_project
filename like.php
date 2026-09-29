<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) {
    echo json_encode(['status' => 'error']);
    exit;
}

$album = isset($_POST['album']) ? trim($_POST['album']) : '';
$photo = isset($_POST['photo']) ? trim($_POST['photo']) : '';
$user_ip = $_SERVER['REMOTE_ADDR'];

if ($album === '' || $photo === '') {
    echo json_encode(['status' => 'error']);
    exit;
}

$albumEsc = $conn->real_escape_string($album);
$photoEsc = $conn->real_escape_string($photo);

$insert = $conn->prepare("INSERT IGNORE INTO photo_likes (album, photo, user_ip) VALUES (?, ?, ?)");
$insert->bind_param("sss", $albumEsc, $photoEsc, $user_ip);
$insert->execute();
$insert->close();

$count = $conn->prepare("SELECT COUNT(*) AS total FROM photo_likes WHERE album=? AND photo=?");
$count->bind_param("ss", $albumEsc, $photoEsc);
$count->execute();
$row = $count->get_result()->fetch_assoc();
$count->close();

echo json_encode([
    'status' => 'success',
    'likes'  => isset($row['total']) ? (int)$row['total'] : 0
]);
