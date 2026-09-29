<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) { die("DB connection failed."); }

function safeFetch($res){
    return ($res && $res->num_rows > 0) ? $res->fetch_assoc() : [];
}

$hero     = safeFetch($conn->query("SELECT * FROM homepage_hero LIMIT 1"));
$about    = safeFetch($conn->query("SELECT * FROM about_me LIMIT 1"));
$settings = safeFetch($conn->query("SELECT * FROM settings LIMIT 1"));

$isLoggedIn = isset($_SESSION['user']);
$userRole   = $_SESSION['role'] ?? 'user';
$userName   = $_SESSION['user_name'] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $settings['site_title'] ?? "Kawsarul Islam"; ?></title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">Kawsarul<span>Islam</span></div>
        <div class="menu-toggle">☰</div>

        <ul class="nav-links">
            <li><a href="#home"> Home</a></li>
            <li><a href="#about"> About</a></li>
            <li><a href="travels.php"> Travels</a></li>
            <li><a href="#contact"> Contact</a></li>

            <?php if ($isLoggedIn): ?>
                <?php if ($userRole === 'admin'): ?>
                    <li><a href="admin/index.php" style="color:#ffd54f;font-weight:600;">Admin Panel</a></li>
                <?php else: ?>
                    <li><a href="my_gallery.php">My Gallery</a></li>
                <?php endif; ?>
                <li><a href="logout.php" style="color:#ff6666;">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<section id="home" class="hero"
style="background:url('<?php echo $hero['background'] ?? 'images/hero.jpg'; ?>') center/cover;">
    <div class="hero-content">
        <h1><?php echo htmlspecialchars($hero['headline'] ?? "Welcome to My Portfolio"); ?></h1>
        <p><?php echo htmlspecialchars($hero['subheadline'] ?? "Explore my travel adventures"); ?></p>
    </div>
</section>

<section id="about" class="about">
    <div class="container">
        <h2>About Me</h2>

        <p>
            <?php 
            echo !empty($about['short_bio']) 
                ? nl2br(htmlspecialchars($about['short_bio'])) 
                : "I’m a Computer Science student and travel lover.";
            ?>
        </p>

        <div class="skills">
            <h3>My Skills</h3>
            <ul>
                <?php
                if (!empty($about['skills'])) {
                    foreach (explode(",", $about['skills']) as $s) {
                        echo "<li>" . htmlspecialchars(trim($s)) . "</li>";
                    }
                } else {
                    echo "<li>HTML, CSS, JavaScript, PHP</li>";
                    echo "<li>MySQL & Database Design</li>";
                    echo "<li>Responsive Web Design</li>";
                }
                ?>
            </ul>
        </div>

        <a href="about.php" class="btn" style="margin-top:15px;display:inline-block;">
            Read full story →
        </a>
    </div>
</section>

<section id="contact" style="padding:70px 20px;background:#0b1a2a;color:white;text-align:center;">
    <h2 style="font-size:32px;margin-bottom:15px;">Contact Me</h2>

    <p>Email: <?php echo htmlspecialchars($settings['contact_email'] ?? "Not set"); ?></p>
    <p>Phone: <?php echo htmlspecialchars($settings['contact_phone'] ?? "Not set"); ?></p>

    <div style="margin:25px 0;">
        <?php if (!empty($settings['facebook'])): ?>
            <a href="<?php echo $settings['facebook']; ?>" target="_blank"
               style="color:#fff;margin:0 12px;font-size:30px;">
                <i class="fa-brands fa-facebook"></i>
            </a>
        <?php endif; ?>

        <?php if (!empty($settings['instagram'])): ?>
            <a href="<?php echo $settings['instagram']; ?>" target="_blank"
               style="color:#fff;margin:0 12px;font-size:30px;">
                <i class="fa-brands fa-instagram"></i>
            </a>
        <?php endif; ?>
    </div>

    <form action="send_message.php" method="POST" style="max-width:500px;margin:auto;">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" placeholder="Your Message" rows="6" required></textarea>
        <button type="submit">Send Message</button>
    </form>
</section>

<footer style="text-align:center;padding:20px;color:white;background:#07101c;">
    <?php echo htmlspecialchars($settings['footer_text'] ?? "© 2025 All Rights Reserved"); ?>
</footer>

</body>
</html>
