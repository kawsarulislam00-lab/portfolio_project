<?php
require __DIR__ . '/admin_auth.php';
require __DIR__ . '/admin_layout_top.php';

$conn = new mysqli("localhost", "root", "", "portfolio_db");

function countTable($conn, $table) {
    $chk = $conn->query("SHOW TABLES LIKE '$table'");
    if (!$chk || $chk->num_rows == 0) return 0;
    $q = $conn->query("SELECT COUNT(*) AS c FROM $table");
    return ($q ? (int)$q->fetch_assoc()['c'] : 0);
}

$travels_count  = countTable($conn, 'travels');
$messages_count = countTable($conn, 'contact_messages');
$posts_count    = countTable($conn, 'travel_posts');
$users_count    = countTable($conn, 'users');

$uploads = $conn->query("SELECT * FROM travels ORDER BY id DESC LIMIT 5");
?>

<div class="header">
    <h1>Admin Dashboard</h1>
    <input type="text" placeholder="Search (UI only)">
</div>

<div class="stats">
    <div class="stat-card">
        <h3>Total Travels</h3>
        <p><?php echo $travels_count; ?></p>
    </div>

    <div class="stat-card">
        <h3>Messages</h3>
        <p><?php echo $messages_count; ?></p>
    </div>

    <div class="stat-card">
        <h3>Community Posts</h3>
        <p><?php echo $posts_count; ?></p>
    </div>

    <div class="stat-card">
        <h3>Users</h3>
        <p><?php echo $users_count; ?></p>
    </div>
</div>

<div class="box">
    <h2>Recent Travels</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Image</th>
            <th>Date</th>
        </tr>

        <?php if ($uploads && $uploads->num_rows): ?>
            <?php while ($u = $uploads->fetch_assoc()): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['title']); ?></td>
                <td><img src="../<?php echo $u['image_path']; ?>" width="70"></td>
                <td><?php echo $u['created_at']; ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align:center; opacity:.7;">No uploads yet.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<?php require __DIR__ . '/admin_layout_bottom.php'; ?>
