<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'likes' => 0]);
    exit;
}

$album = isset($_GET['album']) ? trim($_GET['album']) : '';
$photo = isset($_GET['photo']) ? trim($_GET['photo']) : '';

if ($album === '' || $photo === '') {
    echo json_encode(['status' => 'error', 'likes' => 0]);
    exit;
}

$albumEsc = $conn->real_escape_string($album);
$photoEsc = $conn->real_escape_string($photo);

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM photo_likes WHERE album=? AND photo=?");
$stmt->bind_param("ss", $albumEsc, $photoEsc);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
$stmt->close();

$likes = isset($r['total']) ? (int)$r['total'] : 0;

echo json_encode(['status' => 'success', 'likes' => $likes]);
