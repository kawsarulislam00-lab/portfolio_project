<?php
if (!isset($conn) || !$conn instanceof mysqli) {
    $conn = new mysqli("localhost", "root", "", "portfolio_db");
    if ($conn->connect_error) {
        die("DB Failed");
    }
}

$settingsRes = $conn->query("SELECT * FROM settings LIMIT 1");
$settings = $settingsRes ? $settingsRes->fetch_assoc() : null;
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.social-icons {
    margin-top: 20px;
    margin-bottom: 15px;
    display: flex;
    gap: 14px;
    justify-content: center;
}
.social-icons a {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: rgba(255,255,255,0.10);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 19px;
    text-decoration: none;
    transition: .25s ease;
    border: 1px solid rgba(255,255,255,0.18);
}
.social-icons a:hover {
    background: #ffb347;
    color: #111;
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.4);
}
footer {
    text-align:center;
    padding:15px;
    background:#111;
    color:white;
    margin-top:35px;
    font-size:15px;
}
</style>

<div class="social-icons">
    <?php if (!empty($settings['facebook'])): ?>
        <a href="<?php echo htmlspecialchars($settings['facebook']); ?>" target="_blank">
            <i class="fab fa-facebook-f"></i>
        </a>
    <?php endif; ?>

    <?php if (!empty($settings['instagram'])): ?>
        <a href="<?php echo htmlspecialchars($settings['instagram']); ?>" target="_blank">
            <i class="fab fa-instagram"></i>
        </a>
    <?php endif; ?>

    <?php if (!empty($settings['youtube'])): ?>
        <a href="<?php echo htmlspecialchars($settings['youtube']); ?>" target="_blank">
            <i class="fab fa-youtube"></i>
        </a>
    <?php endif; ?>

    <?php if (!empty($settings['linkedin'])): ?>
        <a href="<?php echo htmlspecialchars($settings['linkedin']); ?>" target="_blank">
            <i class="fab fa-linkedin-in"></i>
        </a>
    <?php endif; ?>
</div>

<footer>
    <?php
        if ($settings && !empty($settings['footer_text'])) {
            echo htmlspecialchars($settings['footer_text']);
        } else {
            echo "© " . date("Y") . " Kawsarul Islam — All Rights Reserved";
        }
    ?>
</footer>

</body>
</html>
