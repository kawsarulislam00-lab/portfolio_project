<?php
require __DIR__ . '/admin_auth.php';

if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];

    if ($id > 0) {
        $action = ($_GET['action'] === 'approve') ? 'approved' : 'rejected';

        $stmt = $conn->prepare("UPDATE travel_posts SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: admin_post_approval.php");
    exit;
}

$pending  = $conn->query("SELECT * FROM travel_posts WHERE status='pending' ORDER BY created_at DESC");
$approved = $conn->query("SELECT * FROM travel_posts WHERE status='approved' ORDER BY created_at DESC LIMIT 10");

require 'admin_layout_top.php';
?>

<div class="header">
    <h1>Community Posts Moderation</h1>
    <input type="text" placeholder="Search (UI only)">
</div>

<div class="box">
    <h2>Pending Posts</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>By</th>
            <th>Image</th>
            <th>Date</th>
            <th>Action</th>
        </tr>

        <?php if ($pending && $pending->num_rows): ?>
            <?php while ($p = $pending->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo htmlspecialchars($p['title']); ?></td>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>

                    <td>
                        <?php if (!empty($p['image_path'])): ?>
                            <img src="../<?php echo $p['image_path']; ?>" width="80" height="60" style="object-fit:cover;border-radius:6px;">
                        <?php endif; ?>
                    </td>

                    <td><?php echo $p['created_at']; ?></td>

                    <td>
                        <a class="btn btn-approve btn-sm" href="?action=approve&id=<?php echo $p['id']; ?>">Approve</a>
                        <a class="btn btn-reject btn-sm" href="?action=reject&id=<?php echo $p['id']; ?>">Reject</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center;opacity:.7;">No pending posts.</td></tr>
        <?php endif; ?>
    </table>
</div>

<div class="box">
    <h2>Recently Approved</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>By</th>
            <th>Status</th>
            <th>Date</th>
        </tr>

        <?php if ($approved && $approved->num_rows): ?>
            <?php while ($p = $approved->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo htmlspecialchars($p['title']); ?></td>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><span class="badge approved">approved</span></td>
                    <td><?php echo $p['created_at']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;opacity:.7;">No approved posts yet.</td></tr>
        <?php endif; ?>
    </table>
</div>

<?php require 'admin_layout_bottom.php'; ?>
