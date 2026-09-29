<?php
require __DIR__ . '/admin_auth.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: admin_travels.php");
    exit;
}

$res = $conn->query("SELECT image_path FROM travels WHERE id=$id LIMIT 1");
if ($res && $res->num_rows) {
    $row = $res->fetch_assoc();

    $file = "../" . $row['image_path'];
    if (file_exists($file)) {
        unlink($file);
    }

    $conn->query("DELETE FROM travels WHERE id=$id");
}

header("Location: admin_travels.php?deleted=1");
exit;
?>
