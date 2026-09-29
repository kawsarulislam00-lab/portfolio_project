<?php
require __DIR__ . '/admin_auth.php';

$conn = new mysqli("localhost", "root", "", "portfolio_db");

$unread_count = 0;
$r = $conn->query("SELECT COUNT(*) AS c FROM contact_messages WHERE is_read = 0");
if ($r) {
    $unread_count = (int)$r->fetch_assoc()["c"];
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Panel</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{margin:0;background:#181a2b;font-family:Poppins,system-ui,sans-serif;color:#fff;display:flex;height:100vh;}
.sidebar{width:240px;background:#1f2235;padding:25px;box-sizing:border-box;}
.sidebar .logo{font-size:22px;font-weight:bold;text-align:center;margin-bottom:25px;color:#ff4b81;}
.profile-pic{width:70px;height:70px;border-radius:50%;object-fit:cover;display:block;margin:0 auto 20px;border:2px solid #fff;}
.menu{list-style:none;padding:0;margin:0;}
.menu li{margin:10px 0;}
.menu .section-label{font-size:11px;opacity:.6;text-transform:uppercase;margin-top:16px;margin-bottom:4px;padding:0 2px;}
.menu a{text-decoration:none;color:#fff;padding:9px 10px;border-radius:8px;display:block;font-size:15px;position:relative;}
.menu a.active,.menu a:hover{background:#ff4b81;}
.main{flex:1;padding:25px;overflow-y:auto;box-sizing:border-box;}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
.header input{padding:8px 12px;border-radius:10px;border:none;background:#2a2d45;color:#fff;min-width:220px;}
.box{background:#2a2d45;border-radius:15px;padding:20px;margin-bottom:25px;}
table{width:100%;border-collapse:collapse;}
th,td{padding:10px;border-bottom:1px solid #333;font-size:14px;}
td img{width:80px;height:60px;object-fit:cover;border-radius:8px;}
.badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:12px;}
.badge.pending{background:#ffb347;}
.badge.approved{background:#4caf50;}
.badge.rejected{background:#f44336;}
button,.btn{border:none;border-radius:6px;padding:6px 10px;cursor:pointer;font-size:13px;}
.btn-approve{background:#4caf50;color:#fff;}
.btn-reject{background:#f44336;color:#fff;}
.btn-sm{padding:3px 7px;font-size:12px;}
.notif-badge{position:absolute;right:10px;top:7px;background:#ff4b81;color:#fff;border-radius:999px;padding:2px 8px;font-size:11px;display:none;}
.notif-badge.show{display:inline-block;animation:pulseBadge 1.2s infinite;}
@keyframes pulseBadge{0%{transform:scale(1);}50%{transform:scale(1.15);}100%{transform:scale(1);}}
.toast{position:fixed;right:20px;bottom:20px;background:#2e7d32;color:#fff;padding:10px 14px;border-radius:10px;font-size:13px;box-shadow:0 10px 25px rgba(0,0,0,.5);opacity:0;pointer-events:none;transform:translateY(20px);transition:opacity .3s,transform .3s;z-index:9999;}
.toast.show{opacity:1;transform:translateY(0);}
</style>
</head>

<body>
<div class="sidebar">
    <div class="logo">Kawsarul Islam</div>
    <img src="pp.png" class="profile-pic" alt="Admin">

    <ul class="menu">

    <li><a href="../index.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='home'?'active':''; ?>">🏡 Home</a></li>

    <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='index.php'?'active':''; ?>">📊 Dashboard</a></li>

        <li class="section-label">Homepage Content</li>
        <li><a href="edit_hero.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='edit_hero.php'?'active':''; ?>">🏠 Edit Hero</a></li>
        <li><a href="edit_about.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='edit_about.php'?'active':''; ?>">ℹ️ Edit About</a></li>
        <li><a href="edit_settings.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='edit_settings.php'?'active':''; ?>">⚙️ Site Settings</a></li>

        <li class="section-label">Content & Users</li>
        <li><a href="admin_travels.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='admin_travels.php'?'active':''; ?>">🗺 Travels</a></li>
        <li><a href="admin_post_approval.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='admin_post_approval.php'?'active':''; ?>">📝 Community Posts</a></li>

        <li>
            <a href="admin_messages.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='admin_messages.php'?'active':''; ?>">
                ✉ Messages
                <span id="msgBadge" class="notif-badge<?php echo $unread_count>0?' show':''; ?>">
                    <?php echo $unread_count>0?$unread_count:''; ?>
                </span>
            </a>
        </li>

        <li><a href="admin_users.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='admin_users.php'?'active':''; ?>">👤 Users</a></li>
        <li><a href="admin_analytics.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='admin_analytics.php'?'active':''; ?>">📈 Analytics</a></li>
        <li><a href="../logout.php">🚪 Logout</a></li>
    </ul>
</div>

<div class="main">

<div id="adminToast" class="toast">You have new unread messages</div>

<script>
(function(){
    var lastCount = <?php echo $unread_count; ?>;
    function updateBadge(count, showToast){
        var badge = document.getElementById('msgBadge');
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count;
            badge.classList.add('show');
        } else {
            badge.textContent = '';
            badge.classList.remove('show');
        }
        if (showToast && count > 0) {
            var t = document.getElementById('adminToast');
            t.classList.add('show');
            setTimeout(function(){ t.classList.remove('show'); }, 3000);
        }
    }
    function poll(){
        var xhr = new XMLHttpRequest();
        xhr.open('GET','admin_unread_count.php',true);
        xhr.onreadystatechange = function(){
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    var c = parseInt(data.unread) || 0;
                    if (c !== lastCount) {
                        var showToast = c > lastCount;
                        lastCount = c;
                        updateBadge(c, showToast);
                    }
                } catch(e){}
            }
        };
        xhr.send();
    }
    setInterval(poll,5000);
})();
</script>
