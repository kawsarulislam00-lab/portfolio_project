<?php
require __DIR__ . '/admin_auth.php';

if (!isset($_GET['id'])) {
    header("Location: admin_travels.php");
    exit;
}

$id = intval($_GET['id']);

$travelRes = $conn->query("SELECT * FROM travels WHERE id=$id LIMIT 1");
if (!$travelRes || !$travelRes->num_rows) {
    header("Location: admin_travels.php");
    exit;
}

$travel = $travelRes->fetch_assoc();

if (isset($_POST['update'])) {
    $title = trim($conn->real_escape_string($_POST['title']));
    $desc  = trim($conn->real_escape_string($_POST['description']));
    $cat   = trim($conn->real_escape_string($_POST['category']));
    $lat   = floatval($_POST['latitude']);
    $lng   = floatval($_POST['longitude']);

    if ($lat < -90 || $lat > 90) {
        header("Location: admin_travel_edit.php?id=$id&error=lat");
        exit;
    }

    if ($lng < -180 || $lng > 180) {
        header("Location: admin_travel_edit.php?id=$id&error=lng");
        exit;
    }

    $imgPath = $travel['image_path'];

    if (!empty($_FILES['image']['name'])) {
        $safe = preg_replace("/[^A-Za-z0-9_\-\.]/", "_", $_FILES['image']['name']);
        $file = time() . "_" . $safe;
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $file);
        $imgPath = "uploads/" . $file;
    }

    $conn->query("
        UPDATE travels SET
            title='$title',
            description='$desc',
            category='$cat',
            latitude='$lat',
            longitude='$lng',
            image_path='$imgPath'
        WHERE id=$id
    ");

    header("Location: admin_travels.php?updated=1");
    exit;
}

require 'admin_layout_top.php';
?>

<h1>Edit Travel</h1>

<div class="box">
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" value="<?= htmlspecialchars($travel['title']); ?>" required>

        <textarea name="description" rows="3" required><?= htmlspecialchars($travel['description']); ?></textarea>

        <input type="text" name="category" value="<?= htmlspecialchars($travel['category']); ?>" required>

        <label>Latitude:</label>
        <input type="number" step="0.000001" name="latitude" value="<?= $travel['latitude']; ?>" required>

        <label>Longitude:</label>
        <input type="number" step="0.000001" name="longitude" value="<?= $travel['longitude']; ?>" required>

        <label>Current Image:</label><br>
        <img src="../<?= $travel['image_path']; ?>" width="140" style="border-radius:8px;"><br><br>

        <input type="file" name="image">

        <button type="submit" name="update">Update</button>
    </form>
</div>

<?php require 'admin_layout_bottom.php'; ?>
