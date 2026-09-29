<?php
require __DIR__ . '/admin_auth.php';
require 'admin_layout_top.php';

$q1 = $conn->query("
    SELECT DATE_FORMAT(created_at,'%Y-%m') AS m, COUNT(*) AS c
    FROM travels
    GROUP BY m ORDER BY m
");

$travel_labels = [];
$travel_data = [];
if ($q1) {
    while ($r = $q1->fetch_assoc()) {
        $travel_labels[] = $r['m'];
        $travel_data[] = (int)$r['c'];
    }
}

$q2 = $conn->query("
    SELECT DATE_FORMAT(created_at,'%Y-%m') AS m, COUNT(*) AS c
    FROM travel_posts
    WHERE status='approved'
    GROUP BY m ORDER BY m
");

$post_labels = [];
$post_data = [];
if ($q2) {
    while ($r = $q2->fetch_assoc()) {
        $post_labels[] = $r['m'];
        $post_data[] = (int)$r['c'];
    }
}

$q3 = $conn->query("
    SELECT DATE_FORMAT(created_at,'%Y-%m') AS m, COUNT(*) AS c
    FROM contact_messages
    GROUP BY m ORDER BY m
");

$msg_labels = [];
$msg_data = [];
if ($q3) {
    while ($r = $q3->fetch_assoc()) {
        $msg_labels[] = $r['m'];
        $msg_data[] = (int)$r['c'];
    }
}
?>
<div class="header">
    <h1>Analytics Overview</h1>
    <input type="text" placeholder="Search (UI only)">
</div>

<style>
.chart-box{background:#1f2235;padding:20px;border-radius:12px;margin-bottom:30px;}
.chart-box h2{margin-top:0;margin-bottom:15px;}
</style>

<div class="chart-box">
    <h2>📌 Travels per Month</h2>
    <canvas id="travelChart" height="120"></canvas>
</div>

<div class="chart-box">
    <h2>📝 Approved Community Posts per Month</h2>
    <canvas id="postChart" height="120"></canvas>
</div>

<div class="chart-box">
    <h2>✉ Messages per Month</h2>
    <canvas id="messageChart" height="120"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById("travelChart"), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($travel_labels); ?>,
        datasets: [{
            label: 'Travels',
            data: <?php echo json_encode($travel_data); ?>,
            borderColor:'#ff4b81',
            backgroundColor:'rgba(255,75,129,.2)',
            tension:.3,
            fill:true
        }]
    }
});

new Chart(document.getElementById("postChart"), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($post_labels); ?>,
        datasets: [{
            label: 'Posts',
            data: <?php echo json_encode($post_data); ?>,
            backgroundColor:'#6dd5ed',
            borderColor:'#2193b0'
        }]
    }
});

new Chart(document.getElementById("messageChart"), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($msg_labels); ?>,
        datasets: [{
            label: 'Messages',
            data: <?php echo json_encode($msg_data); ?>,
            borderColor:'#8e2de2',
            backgroundColor:'rgba(142,45,226,.3)',
            tension:.3,
            fill:true
        }]
    }
});
</script>

<?php require 'admin_layout_bottom.php'; ?>
