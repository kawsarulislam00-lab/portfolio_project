<?php
require 'admin_auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $conn->query("UPDATE contact_messages SET is_read=1 WHERE id=$id");
}

header("Location: admin_messages.php");
exit;
?>
