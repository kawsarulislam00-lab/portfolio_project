<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['admin_logged_in'])) {
    echo json_encode(['status' => 'error']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) {
    echo json_encode(['status' => 'error']);
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['status' => 'error']);
    exit;
}

$conn->query("DELETE FROM photo_comments WHERE id=$id");
echo json_encode(['status' => 'success']);
