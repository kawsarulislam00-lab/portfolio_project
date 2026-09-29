<?php
require __DIR__ . '/admin_auth.php';
require __DIR__ . '/admin_layout_top.php';

$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) {
    die("DB Error");
}

$about = $conn->query("SELECT * FROM about_me LIMIT 1")->fetch_assoc();

if (isset($_POST['update'])) {
    $short  = $conn->real_escape_string($_POST['short_bio']);
    $full   = $conn->real_escape_string($_POST['full_bio']);
    $skills = $conn->real_escape_string($_POST['skills']);

    $imgPath = $about['profile_image'];

    if (!empty($_FILES['image']['name'])) {
        $file = time() . "_" . basename($_FILES['image']['name']);
        $save = "uploads/" . $file;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../' . $save);
        $imgPath = $save;
    }

    $conn->query("
        UPDATE about_me SET 
        short_bio='$short',
        full_bio='$full',
        skills='$skills',
        profile_image='$imgPath'
        LIMIT 1
    ");

    header("Location: edit_about.php?success=1");
    exit;
}
?>

<div class="box">
    <h2>Edit About Section</h2>

    <?php if (isset($_GET['success'])): ?>
        <p style="color:#4caf50;font-weight:bold;">✔ Updated Successfully</p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Short Bio:</label>
        <textarea name="short_bio" rows="3" class="input-full"><?php echo htmlspecialchars($about['short_bio']); ?></textarea>

        <label>Full Bio:</label>
        <textarea name="full_bio" rows="8" class="input-full"><?php echo htmlspecialchars($about['full_bio']); ?></textarea>

        <label>Skills:</label>
        <input type="text" name="skills" class="input-full" value="<?php echo htmlspecialchars($about['skills']); ?>">

        <label>Profile Image:</label>
        <?php if (!empty($about['profile_image'])): ?>
            <img src="../<?php echo $about['profile_image']; ?>" width="160" style="border-radius:8px;margin-bottom:8px;">
        <?php endif; ?>
        <input type="file" name="image" class="input-full">

        <button type="submit" name="update" class="btn">Save</button>

    </form>
</div>

<?php require 'admin_layout_bottom.php'; ?>
