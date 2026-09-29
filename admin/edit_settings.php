<?php
require __DIR__ . '/admin_auth.php';
require 'admin_layout_top.php';

$conn = new mysqli("localhost", "root", "", "portfolio_db");
$s = $conn->query("SELECT * FROM settings LIMIT 1")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_title      = $conn->real_escape_string($_POST['site_title']);
    $footer_text     = $conn->real_escape_string($_POST['footer_text']);
    $contact_email   = $conn->real_escape_string($_POST['contact_email']);
    $contact_phone   = $conn->real_escape_string($_POST['contact_phone']);
    $contact_address = $conn->real_escape_string($_POST['contact_address']);
    $facebook        = $conn->real_escape_string($_POST['facebook']);
    $instagram       = $conn->real_escape_string($_POST['instagram']);
    $theme_color     = $conn->real_escape_string($_POST['theme_color']);
    $hero_title      = $conn->real_escape_string($_POST['hero_title']);
    $hero_subtitle   = $conn->real_escape_string($_POST['hero_subtitle']);

    $logo = $s['logo_path'];
    if (!empty($_FILES['logo']['name'])) {
        $safe = preg_replace("/[^A-Za-z0-9_\-\.]/", "_", $_FILES['logo']['name']);
        $newname = time() . "_" . $safe;
        move_uploaded_file($_FILES['logo']['tmp_name'], "../uploads/" . $newname);
        $logo = "uploads/" . $newname;
    }

    $stmt = $conn->prepare("
        UPDATE settings SET
        site_title=?, footer_text=?, logo_path=?, contact_email=?, contact_phone=?,
        contact_address=?, facebook=?, instagram=?, theme_color=?, hero_title=?, hero_subtitle=?
        WHERE id=1
    ");

    $stmt->bind_param(
        "sssssssssss",
        $site_title, $footer_text, $logo, $contact_email, $contact_phone,
        $contact_address, $facebook, $instagram, $theme_color,
        $hero_title, $hero_subtitle
    );

    $stmt->execute();
    header("Location: edit_settings.php?success=1");
    exit;
}
?>

<style>
.settings-box {
    background: #1b1f36;
    border-radius: 14px;
    padding: 30px;
    max-width: 1000px;
    margin: auto;
    box-shadow: 0 10px 35px rgba(0,0,0,.45);
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

.settings-box label {
    font-size: 14px;
    opacity: .85;
    margin-bottom: 6px;
    display: block;
}

.settings-box input,
.settings-box textarea {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,.15);
    background: #0f1324;
    color: #fff;
    font-size: 14px;
}

.settings-box input[type=color] {
    padding: 2px;
    height: 40px;
    cursor: pointer;
}

.settings-box textarea {
    resize: vertical;
}

.save-btn {
    margin-top: 20px;
    padding: 12px 22px;
    background: #ffb347;
    color: #111;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: .3s;
}

.save-btn:hover {
    background: #ff9500;
}
</style>

<h1>Site Settings</h1>

<form method="POST" enctype="multipart/form-data" class="settings-box">

    <?php if (isset($_GET['success'])): ?>
        <p style="color:#4caf50;font-weight:bold;">✔ Settings updated successfully</p>
    <?php endif; ?>

    <div class="settings-grid">
        <div>
            <label>Website Title</label>
            <input type="text" name="site_title" value="<?php echo $s['site_title']; ?>" required>
        </div>

        <div>
            <label>Footer Text</label>
            <textarea name="footer_text" rows="3"><?php echo $s['footer_text']; ?></textarea>
        </div>

        <div>
            <label>Upload Logo</label>
            <?php if ($s['logo_path']): ?>
                <img src="../<?php echo $s['logo_path']; ?>" width="120" style="border-radius:6px;margin-bottom:8px;">
            <?php endif; ?>
            <input type="file" name="logo">
        </div>

        <div>
            <label>Contact Email</label>
            <input type="text" name="contact_email" value="<?php echo $s['contact_email']; ?>">
        </div>

        <div>
            <label>Contact Phone</label>
            <input type="text" name="contact_phone" value="<?php echo $s['contact_phone']; ?>">
        </div>

        <div>
            <label>Address</label>
            <input type="text" name="contact_address" value="<?php echo $s['contact_address']; ?>">
        </div>

        <div>
            <label>Facebook</label>
            <input type="text" name="facebook" value="<?php echo $s['facebook']; ?>">
        </div>

        <div>
            <label>Instagram</label>
            <input type="text" name="instagram" value="<?php echo $s['instagram']; ?>">
        </div>

        <div>
            <label>Theme Color</label>
            <input type="color" name="theme_color" value="<?php echo $s['theme_color']; ?>">
        </div>

        <div>
            <label>Hero Title</label>
            <input type="text" name="hero_title" value="<?php echo $s['hero_title']; ?>">
        </div>

        <div style="grid-column:1 / -1">
            <label>Hero Subtitle</label>
            <textarea name="hero_subtitle" rows="3"><?php echo $s['hero_subtitle']; ?></textarea>
        </div>
    </div>

    <button type="submit" class="save-btn">Save Settings</button>

</form>

<?php require 'admin_layout_bottom.php'; ?>
