<?php
require 'admin_auth.php';
header('Content-Type: application/json');

$unread = 0;
$q = $conn->query("SELECT COUNT(*) AS c FROM contact_messages WHERE is_read=0");
if ($q) {
    $unread = (int)$q->fetch_assoc()['c'];
}

echo json_encode(['unread' => $unread]);
