<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

if (!empty($_GET['album']) && !empty($_GET['photo'])) {
    $album = $conn->real_escape_string($_GET['album']);
    $photo = $conn->real_escape_string($_GET['photo']);

    $result = $conn->query("
        SELECT id,
               COALESCE(user_name,'') AS user_name,
               comment,
               COALESCE(admin_reply,'') AS admin_reply,
               COALESCE(created_at,'') AS created_at
        FROM photo_comments
        WHERE album='$album' AND photo='$photo'
        ORDER BY created_at DESC
    ");

    $list = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($row['created_at'] === '' || $row['created_at'] === null) {
                $row['created_at'] = date('Y-m-d H:i:s');
            }
            $list[] = $row;
        }
    }

    echo json_encode($list);
    exit;
}

if (!empty($_GET['post_id'])) {
    $post_id = (int)$_GET['post_id'];

    $stmt = $conn->prepare("
        SELECT id, comment, created_at
        FROM post_comments
        WHERE post_id=?
        ORDER BY created_at DESC
    ");

    if ($stmt) {
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $res = $stmt->get_result();

        $list = [];
        while ($row = $res->fetch_assoc()) {
            if ($row['created_at'] === '' || $row['created_at'] === null) {
                $row['created_at'] = date('Y-m-d H:i:s');
            }
            $list[] = $row;
        }

        $stmt->close();
        echo json_encode($list);
        exit;
    }

    echo json_encode([]);
    exit;
}

echo json_encode([]);
?>
