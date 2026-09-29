<?php
$conn = new mysqli("localhost","root","","portfolio_db");
$aboutRes = $conn->query("SELECT * FROM about_me LIMIT 1");
$about = $aboutRes ? $aboutRes->fetch_assoc() : null;
$settingsRes = $conn->query("SELECT * FROM settings LIMIT 1");
$settings = $settingsRes ? $settingsRes->fetch_assoc() : null;

// SESSION FOR LOGIN CHECK
session_start();
$isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

function countTableSafe($conn,$table){
    $c = $conn->query("SHOW TABLES LIKE '".$conn->real_escape_string($table)."'");
    if(!$c || $c->num_rows==0) return 0;
    $q = $conn->query("SELECT COUNT(*) AS c FROM $table");
    if(!$q) return 0;
    $r = $q->fetch_assoc();
    return (int)$r['c'];
}

$travelsCount  = countTableSafe($conn,'travels');
$postsCount    = countTableSafe($conn,'travel_posts');
$messagesCount = countTableSafe($conn,'contact_messages');
$yearsTravel   = 3;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>About | Kawsarul Islam</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
body {
    margin: 0;
    font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    background: #050816;
    color: #f5f5f5;
}

.about-hero {
    height: 260px;
    background: linear-gradient(135deg, #1f1c2c 0%, #3a1c71 40%, #4e2fa5 65%, #2b5876 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 0 20px;
    position: relative;
    overflow: hidden;
}

.about-hero::before {
    content: "";
    position: absolute;
    width: 420px;
    height: 420px;
    background: rgba(120, 90, 255, 0.15);
    filter: blur(150px);
    top: -120px;
    left: -60px;
}

.about-hero::after {
    content: "";
    position: absolute;
    width: 350px;
    height: 350px;
    background: rgba(0, 150, 255, 0.12);
    filter: blur(160px);
    bottom: -100px;
    right: -40px;
}


.about-hero-inner {
    position: relative;
    z-index: 2;
}

.about-hero h1 {
    font-size: 36px;
    margin: 0 0 8px;
    color: #fff;
}

.about-hero h1 span {
    color: #ffb347;
}

.about-hero p {
    margin: 0;
    opacity: .9;
}

.about-wrapper {
    max-width: 1150px;
    margin: 30px auto 60px;
    padding: 0 20px;
}

.about-top-layout {
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    margin-bottom: 30px;
}

.about-profile-card {
    flex: 0 0 260px;
    background: linear-gradient(145deg, #101428, #1a1f35);
    border-radius: 24px;
    padding: 16px;
    box-shadow: 0 18px 40px rgba(0,0,0,.45);
}

.about-profile-card-inner {
    background: radial-gradient(circle at top, rgba(255,255,255,.07), transparent 55%);
    border-radius: 18px;
    padding: 14px;
}

.about-avatar {
    width: 100%;
    border-radius: 18px;
    object-fit: cover;
    margin-bottom: 14px;
}

.about-name {
    font-size: 20px;
    margin: 0 0 4px;
}

.about-role {
    font-size: 14px;
    opacity: .8;
    margin-bottom: 10px;
}

.about-badge-row {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 10px;
}

.about-badge {
    font-size: 11px;
    padding: 4px 9px;
    border-radius: 999px;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.09);
}

.about-contact-mini {
    font-size: 12px;
    opacity: .85;
    line-height: 1.5;
}

.about-main-text {
    flex: 1;
    min-width: 260px;
    background: linear-gradient(145deg, #101528, #141a32);
    border-radius: 24px;
    padding: 20px 22px;
    box-shadow: 0 18px 40px rgba(0,0,0,.4);
}

.about-main-text h2 {
    margin: 0 0 10px;
    font-size: 22px;
}

.about-main-text p {
    margin: 0 0 16px;
    font-size: 14px;
    line-height: 1.7;
}

.about-skills-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.about-skill-pill {
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.09);
}

.about-counters {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(160px,1fr));
    gap: 18px;
    margin-bottom: 35px;
}

.counter-card {
    background: radial-gradient(circle at top, rgba(255,255,255,.08), transparent 60%);
    border-radius: 20px;
    padding: 16px 18px;
    border: 1px solid rgba(255,255,255,.06);
}

.counter-label {
    font-size: 13px;
    opacity: .75;
    margin-bottom: 6px;
}

.counter-value {
    font-size: 26px;
    font-weight: 700;
}

.counter-suffix {
    font-size: 16px;
    opacity: .85;
}

.about-timeline {
    margin-bottom: 35px;
}

.about-section-title {
    font-size: 20px;
    margin: 0 0 12px;
}

.timeline {
    position: relative;
    padding-left: 18px;
    border-left: 2px solid rgba(255,255,255,.12);
}

.timeline-item {
    margin-bottom: 18px;
    position: relative;
    padding-left: 12px;
}

.timeline-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #ffb347;
    position: absolute;
    left: -6px;
    top: 6px;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
}

.timeline-sub {
    font-size: 12px;
    opacity: .75;
}

.timeline-desc {
    font-size: 13px;
    opacity: .9;
}

.about-bottom-grid {
    display: grid;
    grid-template-columns: minmax(0,1.4fr) minmax(0,1fr);
    gap: 22px;
}

.about-card {
    background: linear-gradient(145deg, #101528, #151b33);
    border-radius: 20px;
    padding: 16px 18px;
    border: 1px solid rgba(255,255,255,.06);
}

.about-card h3 {
    margin: 0 0 8px;
    font-size: 17px;
}

.about-card p {
    margin: 0 0 10px;
    font-size: 14px;
    opacity: .9;
}

.about-list {
    padding-left: 18px;
    margin: 4px 0 0;
    font-size: 13px;
}

.about-list li {
    margin-bottom: 4px;
}

@media(max-width: 860px){
    .about-top-layout {
        flex-direction: column;
    }
}
</style>
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">Kawsarul<span>Islam</span></div>
        <div class="menu-toggle">☰</div>
        <ul class="nav-links">
            <li><a href="index.php#home">Home</a></li>
            <li><a href="about.php" class="active">About</a></li>
            <li><a href="travels.php">Travels</a></li>
            <li><a href="index.php#contact">Contact</a></li>
            <?php if($isAdmin): ?>
                <li><a href="admin/index.php" style="color:#ffb347;font-weight:bold;">Admin Panel</a></li>
                <li><a href="logout.php" style="color:#ff4d4d;">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" style="color:#ffb347;">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<section class="about-hero">
    <div class="about-hero-inner">
        <h1>About <span>Kawsarul Islam</span></h1>
        <p>A web developer, traveler and lifelong learner</p>
    </div>
</section>

<div class="about-wrapper">

    <div class="about-top-layout">
        <div class="about-profile-card">
            <div class="about-profile-card-inner">

                <?php if($about && !empty($about['profile_image'])): ?>
                    <img src="<?php echo htmlspecialchars($about['profile_image']); ?>" class="about-avatar">
                <?php else: ?>
                    <img src="images/profile-default.jpg" class="about-avatar">
                <?php endif; ?>

                <h2 class="about-name">Kawsarul Islam</h2>
                <div class="about-role">Computer Science Student • Travel Enthusiast</div>

                <div class="about-badge-row">
                    <span class="about-badge">Web Development</span>
                    <span class="about-badge">PHP & MySQL</span>
                    <span class="about-badge">Travel Photography</span>
                </div>

                <div class="about-contact-mini">
                    <?php if($settings && !empty($settings['email'])): ?>
                        Email: <?php echo htmlspecialchars($settings['email']); ?><br>
                    <?php endif; ?>
                    <?php if($settings && !empty($settings['phone'])): ?>
                        Phone: <?php echo htmlspecialchars($settings['phone']); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="about-main-text">
            <h2>My Story</h2>
            <p>
                <?php
                if($about && !empty($about['short_bio'])){
                    echo nl2br(htmlspecialchars($about['short_bio']));
                } else {
                    echo "I am a Computer Science student who loves building web applications and exploring new places.";
                }
                ?>
            </p>

            <div style="font-size:14px; line-height:1.7; margin-bottom:18px;">
                <?php
                if($about && !empty($about['full_bio'])){
                    echo nl2br(htmlspecialchars($about['full_bio']));
                } else {
                    echo "Traveling and coding are two journeys that shaped who I am today.";
                }
                ?>
            </div>

            <h3 style="font-size:16px;margin-bottom:8px;">Skills & Technologies</h3>
            <div class="about-skills-row">
                <?php
                if($about && !empty($about['skills'])){
                    foreach(explode(',', $about['skills']) as $skill){
                        $skill = trim($skill);
                        if($skill === '') continue;
                        echo '<span class="about-skill-pill">'.htmlspecialchars($skill).'</span>';
                    }
                } else {
                    echo '<span class="about-skill-pill">HTML</span>';
                    echo '<span class="about-skill-pill">CSS</span>';
                    echo '<span class="about-skill-pill">JavaScript</span>';
                    echo '<span class="about-skill-pill">PHP</span>';
                    echo '<span class="about-skill-pill">MySQL</span>';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="about-counters">
        <div class="counter-card">
            <div class="counter-label">Travels Logged</div>
            <div class="counter-value"><span class="count-number" data-target="<?php echo $travelsCount; ?>">0</span>+</div>
        </div>

        <div class="counter-card">
            <div class="counter-label">Community Stories</div>
            <div class="counter-value"><span class="count-number" data-target="<?php echo $postsCount; ?>">0</span>+</div>
        </div>

        <div class="counter-card">
            <div class="counter-label">Messages Received</div>
            <div class="counter-value"><span class="count-number" data-target="<?php echo $messagesCount; ?>">0</span>+</div>
        </div>

        <div class="counter-card">
            <div class="counter-label">Years of Exploring</div>
            <div class="counter-value"><span class="count-number" data-target="<?php echo $yearsTravel; ?>">0</span>+</div>
        </div>
    </div>

    <div class="about-timeline">
        <h3 class="about-section-title">Journey Timeline</h3>

        <div class="timeline">

            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-title">Started Computer Science Degree</div>
                <div class="timeline-sub">Nanjing Tech University</div>
                <div class="timeline-desc">Began my journey into algorithms, programming, and software development.</div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-title">Built My First Portfolio Website</div>
                <div class="timeline-sub">Web Development Project</div>
                <div class="timeline-desc">Combined travel and coding into a dynamic portfolio site.</div>
            </div>

            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-title">Exploring Cities Across China</div>
                <div class="timeline-sub">Travel & Photography</div>
                <div class="timeline-desc">Capturing landscapes, food, and cultures.</div>
            </div>

        </div>
    </div>

    <div class="about-bottom-grid">

        <div class="about-card">
            <h3>What I’m Focusing On Now</h3>
            <p>Improving my skills in full-stack web development.</p>
            <ul class="about-list">
                <li>Improving PHP and MySQL backend logic</li>
                <li>Designing better UI/UX</li>
                <li>Learning deployment and maintenance</li>
            </ul>
        </div>

        <div class="about-card">
            <h3>Outside of Coding</h3>
            <p>I explore new streets, try food, and plan trips.</p>
            <ul class="about-list">
                <li>Travel photography</li>
                <li>Meeting people from different cultures</li>
                <li>Writing travel notes</li>
            </ul>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){
    var counters = document.querySelectorAll(".count-number");
    counters.forEach(function(el){
        var target = parseInt(el.getAttribute("data-target")) || 0;
        var duration = 1200;
        var start = null;
        function animate(ts){
            if(!start) start = ts;
            var progress = ts - start;
            var ratio = Math.min(progress / duration, 1);
            el.textContent = Math.floor(ratio * target);
            if(ratio < 1) requestAnimationFrame(animate);
        }
        requestAnimationFrame(animate);
    });
});

</script>
<footer style="text-align:center;padding:20px;color:white;background:#07101c;">
    <?php echo htmlspecialchars($settings['footer_text'] ?? "© 2025 All Rights Reserved"); ?>
</footer>
</body>
</html>
