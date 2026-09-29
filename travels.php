<?php 
include 'header.php';

$conn = new mysqli("localhost", "root", "", "portfolio_db");
$travels = $conn->query("SELECT * FROM travels ORDER BY id DESC");
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<style>
body{background:#0b0f1a;color:#fff;font-family:system-ui;}
.travel-hero{height:85vh;background:linear-gradient(rgba(0,0,0,.65),rgba(0,0,0,.65)),url('images/hero.jpg')center/cover;display:flex;justify-content:center;align-items:center;flex-direction:column;text-align:center;padding:0 20px;color:white;}
.travel-hero h1{font-size:52px;font-weight:800;}
.travel-hero p{font-size:18px;max-width:580px;opacity:.9;line-height:1.6;}
.travel-buttons{margin-top:25px;display:flex;gap:15px;flex-wrap:wrap;justify-content:center;}
.travel-btn{padding:12px 22px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:8px;font-weight:600;text-decoration:none;color:white;transition:.25s;}
.travel-btn:hover{background:#ff9900;border-color:#ff9900;}
.section-title{text-align:center;font-size:28px;font-weight:700;margin-top:55px;margin-bottom:15px;color:#ffb347;}
#travelMap{width:92%;height:420px;border-radius:20px;margin:0 auto 40px;box-shadow:0 10px 25px rgba(0,0,0,.45);overflow:hidden;}
.travel-grid{width:92%;max-width:1250px;margin:0 auto 70px;display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:28px;}
.travel-card{background:#111629;border:1px solid rgba(255,255,255,.06);padding:16px;border-radius:18px;transition:.3s;box-shadow:0 12px 20px rgba(0,0,0,.4);cursor:pointer;}
.travel-card:hover{transform:translateY(-6px);box-shadow:0 20px 35px rgba(0,0,0,.55);}
.travel-card img{width:100%;height:180px;border-radius:14px;object-fit:cover;cursor:zoom-in;}
.travel-card h3{margin:12px 0 6px;font-size:20px;color:#ffb347;}
.travel-card p{font-size:14px;opacity:.85;}
.highlight{border:2px solid #ffb347;}
</style>

<section class="travel-hero">
    <h1>Kawsarul Islam</h1>
    <p>Every city I explore teaches me a new story — this is my travel journey across China & beyond.</p>
    <div class="travel-buttons">
        <a href="my_gallery.php" class="travel-btn">My Gallery</a>
        <a href="share_form.php" class="travel-btn">Share Experience</a>
        <a href="community_posts.php" class="travel-btn">Community Posts</a>
    </div>
</section>

<h2 class="section-title">Travel Map</h2>
<div id="travelMap"></div>

<h2 class="section-title">My Travel Destinations</h2>

<div class="travel-grid">
<?php while($t = $travels->fetch_assoc()): ?>
    <div class="travel-card" id="travel-<?php echo $t['id']; ?>">

        <a href="<?php echo htmlspecialchars($t['image_path']); ?>" data-lightbox="travel-gallery">
            <img src="<?php echo htmlspecialchars($t['image_path']); ?>" alt="">
        </a>

        <h3><?php echo htmlspecialchars($t['title']); ?></h3>
        <p><?php echo htmlspecialchars($t['description']); ?></p>
        <small>📍 <?php echo htmlspecialchars($t['latitude']); ?> , <?php echo htmlspecialchars($t['longitude']); ?></small>

    </div>
<?php endwhile; ?>
</div>

<script>
var map = L.map("travelMap").setView([30, 100], 4);
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{maxZoom:18}).addTo(map);

var markers = {};

<?php
$mm = $conn->query("SELECT * FROM travels");
while($x = $mm->fetch_assoc()):
    $popupText = htmlspecialchars($x['title'], ENT_QUOTES);
?>
var m<?php echo $x['id']; ?> = L.marker([<?php echo $x['latitude']; ?>, <?php echo $x['longitude']; ?>])
    .addTo(map)
    .bindPopup("<b><?php echo $popupText; ?></b>");

markers[<?php echo $x['id']; ?>] = m<?php echo $x['id']; ?>;

m<?php echo $x['id']; ?>.on("click", function(){
    let c = document.getElementById("travel-<?php echo $x['id']; ?>");
    c.classList.add("highlight");
    c.scrollIntoView({ behavior: "smooth", block: "center" });
    setTimeout(() => c.classList.remove("highlight"), 2000);
});
<?php endwhile; ?>

document.querySelectorAll(".travel-card").forEach(card => {
    card.addEventListener("click", function(){
        let id = this.id.replace("travel-", "");
        markers[id].openPopup();
        map.setView(markers[id].getLatLng(), 8);
        this.classList.add("highlight");
        setTimeout(() => this.classList.remove("highlight"), 2000);
    });
});
</script>

<?php include 'footer.php'; ?>
