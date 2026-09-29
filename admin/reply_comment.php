<?php
session_start();
header("Content-Type: application/json");

if (empty($_SESSION["admin_logged_in"])) {
    echo json_encode(["status" => "error"]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) {
    echo json_encode(["status" => "error"]);
    exit;
}

$id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
$reply = trim($_POST["reply"] ?? "");

if ($id <= 0 || $reply === "") {
    echo json_encode(["status" => "error"]);
    exit;
}

$stmt = $conn->prepare("UPDATE photo_comments SET admin_reply=?, is_read=1 WHERE id=?");
$stmt->bind_param("si", $reply, $id);
$stmt->execute();
$stmt->close();

echo json_encode(["status" => "success"]);
