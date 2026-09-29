<?php
require __DIR__ . '/admin_auth.php';

$messages = null;

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $conn->query("DELETE FROM contact_messages WHERE id = $id");
    }
    header("Location: admin_messages.php");
    exit;
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    if ($id > 0) {
        $conn->query("UPDATE contact_messages SET is_read = 1 - is_read WHERE id = $id");
    }
    header("Location: admin_messages.php");
    exit;
}

$messages = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");

require 'admin_layout_top.php';
?>
<div class="header">
    <h1>Contact Messages</h1>
    <input type="text" placeholder="Search (UI only)">
</div>

<style>
.msg-status-read{background:#2e7d32;color:#fff;padding:3px 8px;border-radius:10px;font-size:12px;}
.msg-status-unread{background:#fbc02d;color:#111;padding:3px 8px;border-radius:10px;font-size:12px;}
.msg-actions a{color:#ffb347;text-decoration:none;margin-right:8px;font-size:13px;}
.msg-actions a.delete-link{color:#ff6b81;}
.msg-actions a.reply-link{color:#6dd5ed;}
</style>

<div class="box">
    <h2 style="margin-top:0;">All Messages</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Message</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>

        <?php if($messages && $messages->num_rows): ?>
            <?php while($m = $messages->fetch_assoc()): ?>
                <?php $isRead = (int)$m['is_read'] === 1; ?>
                <tr>
                    <td><?= $m['id']; ?></td>
                    <td><?= htmlspecialchars($m['name']); ?></td>
                    <td><?= htmlspecialchars($m['email']); ?></td>
                    <td><?= htmlspecialchars($m['subject']); ?></td>
                    <td>
                        <?php if($isRead): ?>
                            <span class="msg-status-read">Read</span>
                        <?php else: ?>
                            <span class="msg-status-unread">Unread</span>
                        <?php endif; ?>
                    </td>
                    <td><?= nl2br(htmlspecialchars($m['message'])); ?></td>
                    <td><?= $m['created_at']; ?></td>
                    <td class="msg-actions">
                        <a class="reply-link" href="admin_message_view.php?id=<?= $m['id']; ?>">View / Reply</a>
                        <a href="?toggle=<?= $m['id']; ?>"><?= $isRead ? 'Mark Unread' : 'Mark Read'; ?></a>
                        <a class="delete-link" href="?delete=<?= $m['id']; ?>" onclick="return confirm('Delete this message?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>

        <?php else: ?>
            <tr><td colspan="8">No messages.</td></tr>
        <?php endif; ?>

    </table>
</div>

<?php require 'admin_layout_bottom.php'; ?>
