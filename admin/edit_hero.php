<?php
require __DIR__ . '/admin_auth.php';
require __DIR__ . '/admin_layout_top.php';

function safeFetch($r){
    return ($r && $r->num_rows > 0) ? $r->fetch_assoc() : [];
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $big_title   = $conn->real_escape_string($_POST['big_title'] ?? '');
    $headline    = $conn->real_escape_string($_POST['headline'] ?? '');
    $subheadline = $conn->real_escape_string($_POST['subheadline'] ?? '');

    $exist  = safeFetch($conn->query("SELECT * FROM homepage_hero LIMIT 1"));
    $bgPath = $exist['background'] ?? 'images/hero.jpg';

    if (!empty($_FILES['background']['name'])) {
        $dirFs  = __DIR__ . '/../uploads/hero/';
        $dirWeb = 'uploads/hero/';

        if (!is_dir($dirFs)) mkdir($dirFs, 0777, true);

        $file = time() . "_" . basename($_FILES['background']['name']);
        $toFs  = $dirFs . $file;
        $toWeb = $dirWeb . $file;

        if (move_uploaded_file($_FILES['background']['tmp_name'], $toFs)) {
            $bgPath = $toWeb;
        }
    }

    if ($exist) {
        $stmt = $conn->prepare("UPDATE homepage_hero SET big_title=?, headline=?, subheadline=?, background=? WHERE id=?");
        $id = $exist['id'];
        $stmt->bind_param("ssssi", $big_title, $headline, $subheadline, $bgPath, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO homepage_hero (big_title, headline, subheadline, background) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $big_title, $headline, $subheadline, $bgPath);
    }

    if ($stmt->execute()) {
        $msg = "✔ Hero updated successfully.";
    } else {
        $msg = "Error: " . $conn->error;
    }
}

$hero = safeFetch($conn->query("SELECT * FROM homepage_hero LIMIT 1"));
?>

<div class="header">
    <h1>Edit Hero Section</h1>
</div>

<div class="box">
    <?php if ($msg): ?>
        <p style="margin-top:0;color:#7CFC00;"><?php echo $msg; ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Page Title</label>
        <input type="text" name="big_title" class="input-full"
               value="<?php echo htmlspecialchars($hero['big_title'] ?? ''); ?>">

        <label>Main Headline</label>
        <input type="text" name="headline" class="input-full"
               value="<?php echo htmlspecialchars($hero['headline'] ?? ''); ?>">

        <label>Subheadline</label>
        <input type="text" name="subheadline" class="input-full"
               value="<?php echo htmlspecialchars($hero['subheadline'] ?? ''); ?>">

        <label>Background Image</label>
        <?php if (!empty($hero['background'])): ?>
            <img src="../<?php echo $hero['background']; ?>" style="max-width:100%;max-height:180px;border-radius:8px;margin:8px 0;">
        <?php endif; ?>

        <input type="file" name="background" accept="image/*" style="margin-bottom:15px;">

        <button type="submit" class="btn" style="background:#ff4b81;color:#fff;">Save Hero</button>
    </form>
</div>

<?php require 'admin_layout_bottom.php'; ?>
