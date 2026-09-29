<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) {
    echo json_encode(["status" => "error"]);
    exit;
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$user_ip = $_SERVER['REMOTE_ADDR'];

if ($post_id <= 0) {
    echo json_encode(["status" => "error"]);
    exit;
}
$check = $conn->prepare("SELECT id FROM post_likes WHERE post_id=? AND user_ip=? LIMIT 1");
$check->bind_param("is", $post_id, $user_ip);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    $ins = $conn->prepare("INSERT INTO post_likes (post_id, user_ip) VALUES (?, ?)");
    $ins->bind_param("is", $post_id, $user_ip);
    $ins->execute();
}
$q = $conn->prepare("SELECT COUNT(*) AS total FROM post_likes WHERE post_id=?");
$q->bind_param("i", $post_id);
$q->execute();
$res = $q->get_result()->fetch_assoc();

echo json_encode([
    "status" => "success",
    "likes"  => (int)$res['total']
]);
