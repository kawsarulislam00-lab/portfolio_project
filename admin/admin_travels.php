<?php
require __DIR__ . '/admin_auth.php';

if (isset($_POST['save'])) {
    $title = trim($_POST['title']);
    $desc  = trim($_POST['description']);
    $cat   = trim($_POST['category']);
    $lat   = floatval($_POST['latitude']);
    $lng   = floatval($_POST['longitude']);

    if ($lat < -90 || $lat > 90) {
        header("Location: admin_travels.php?error=lat");
        exit;
    }

    if ($lng < -180 || $lng > 180) {
        header("Location: admin_travels.php?error=lng");
        exit;
    }

    $imgPath = "";
    if (!empty($_FILES['image']['name'])) {
        $safe = preg_replace("/[^A-Za-z0-9_\-\.]/", "_", $_FILES['image']['name']);
        $fname = time() . "_" . $safe;
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $fname);
        $imgPath = "uploads/" . $fname;
    }

    $conn->query("
        INSERT INTO travels (title, description, category, image_path, latitude, longitude, created_at)
        VALUES ('$title', '$desc', '$cat', '$imgPath', '$lat', '$lng', NOW())
    ");

    header("Location: admin_travels.php?success=1");
    exit;
}

$travels = $conn->query("SELECT * FROM travels ORDER BY id DESC");

require 'admin_layout_top.php';
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
#adminMap{width:100%;height:380px;border-radius:12px;margin:18px 0;background:#1e1e2e;border:2px solid #333;box-shadow:0 10px 25px rgba(0,0,0,.5);}
#locationSearch{width:100%;padding:12px;background:#1b1f32;border:1px solid #444;color:#fff;border-radius:8px 8px 0 0;}
#locationSearchList{position:absolute;width:95%;background:#1b1f32;border:1px solid #444;border-top:none;border-radius:0 0 8px 8px;max-height:220px;overflow-y:auto;display:none;z-index:9999;}
.search-item{padding:10px;color:#ccc;border-bottom:1px solid #333;cursor:pointer;}
.search-item:hover{background:#2a2f47;}
</style>

<h1>Manage Travels</h1>

<div class="box">
    <h2>Add New Travel</h2>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Title" required>
        <textarea name="description" placeholder="Description" rows="3" required></textarea>
        <input type="text" name="category" placeholder="Category" required>
        <input type="number" step="0.000001" name="latitude" placeholder="Latitude" required>
        <input type="number" step="0.000001" name="longitude" placeholder="Longitude" required>

        <input type="text" id="locationSearch" placeholder="Search location...">
        <div id="locationSearchList"></div>

        <div id="adminMap"></div>

        <input type="file" name="image" required>
        <button type="submit" name="save">Save</button>
    </form>
</div>

<div class="box">
    <h2>All Travels</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Image</th>
            <th>Lat</th>
            <th>Lng</th>
            <th>Date</th>
            <th>Action</th>
        </tr>

        <?php while($t = $travels->fetch_assoc()): ?>
        <tr>
            <td><?= $t['id']; ?></td>
            <td><?= htmlspecialchars($t['title']); ?></td>
            <td><img src="../<?= $t['image_path']; ?>" width="80"></td>
            <td><?= $t['latitude']; ?></td>
            <td><?= $t['longitude']; ?></td>
            <td><?= $t['created_at']; ?></td>
            <td>
                <a href="admin_travel_edit.php?id=<?= $t['id']; ?>" style="padding:6px 10px;background:#4caf50;color:white;border-radius:6px;text-decoration:none;">Edit</a>
                <a href="admin_travel_delete.php?id=<?= $t['id']; ?>" onclick="return confirm('Delete this travel?');" style="padding:6px 10px;background:#f44336;color:white;border-radius:6px;text-decoration:none;">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>
</div>

<script>
var map = L.map('adminMap').setView([32.0603, 118.7969], 11);
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{maxZoom:18}).addTo(map);

var marker;

map.on("click",function(e){
    let lat=e.latlng.lat.toFixed(6);
    let lng=e.latlng.lng.toFixed(6);
    document.querySelector("input[name='latitude']").value=lat;
    document.querySelector("input[name='longitude']").value=lng;
    if(marker) map.removeLayer(marker);
    marker=L.marker([lat,lng]).addTo(map);
});

const searchInput=document.getElementById("locationSearch");
const suggestionBox=document.getElementById("locationSearchList");
let searchTimeout=null;

searchInput.addEventListener("input",function(){
    let q=this.value.trim();
    if(q.length<2){suggestionBox.style.display="none";return;}
    clearTimeout(searchTimeout);
    searchTimeout=setTimeout(()=>fetchLoc(q),300);
});

function fetchLoc(q){
    fetch("https://nominatim.openstreetmap.org/search?format=json&q="+encodeURIComponent(q))
    .then(r=>r.json())
    .then(list=>{
        suggestionBox.innerHTML="";
        if(list.length===0){suggestionBox.style.display="none";return;}
        suggestionBox.style.display="block";
        list.slice(0,8).forEach(item=>{
            let div=document.createElement("div");
            div.className="search-item";
            div.textContent=item.display_name;
            div.onclick=function(){
                let lat=parseFloat(item.lat);
                let lon=parseFloat(item.lon);
                map.setView([lat,lon],14);
                if(marker) map.removeLayer(marker);
                marker=L.marker([lat,lon]).addTo(map).bindPopup(item.display_name).openPopup();
                searchInput.value=item.display_name;
                suggestionBox.style.display="none";
                document.querySelector("input[name='latitude']").value=lat;
                document.querySelector("input[name='longitude']").value=lon;
            };
            suggestionBox.appendChild(div);
        });
    });
}

document.addEventListener("click",function(e){
    if(e.target!==searchInput){suggestionBox.style.display="none";}
});

setTimeout(()=>map.invalidateSize(),600);
</script>

<?php require 'admin_layout_bottom.php'; ?>
