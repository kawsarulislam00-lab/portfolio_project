<?php
require __DIR__ . '/admin_auth.php';

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $conn->query("DELETE FROM contact_messages WHERE id=$id");
}

header("Location: admin_messages.php");
exit;
