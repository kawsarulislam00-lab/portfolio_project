<?php
require __DIR__ . '/admin_auth.php';

$conn = new mysqli("localhost", "root", "", "portfolio_db");

$tab = $_GET['tab'] ?? 'posts';

if (isset($_POST['reply_id'])) {
    $id = (int)$_POST['reply_id'];
    $reply = $conn->real_escape_string($_POST['reply_text']);
    if ($id > 0) {
        $conn->query("UPDATE post_comments SET admin_reply='$reply', is_read=1 WHERE id=$id");
    }
    header("Location: admin_comments.php?tab=posts");
    exit;
}

if (isset($_POST['reply_photo_id'])) {
    $id = (int)$_POST['reply_photo_id'];
    $reply = $conn->real_escape_string($_POST['reply_photo_text']);
    if ($id > 0) {
        $conn->query("UPDATE photo_comments SET admin_reply='$reply', is_read=1 WHERE id=$id");
    }
    header("Location: admin_comments.php?tab=photos");
    exit;
}

if (isset($_GET['delete_post'])) {
    $id = (int)$_GET['delete_post'];
    if ($id > 0) {
        $conn->query("DELETE FROM post_comments WHERE id=$id");
    }
    header("Location: admin_comments.php?tab=posts");
    exit;
}

if (isset($_GET['delete_photo'])) {
    $id = (int)$_GET['delete_photo'];
    if ($id > 0) {
        $conn->query("DELETE FROM photo_comments WHERE id=$id");
    }
    header("Location: admin_comments.php?tab=photos");
    exit;
}

$post_comments  = $conn->query("SELECT * FROM post_comments ORDER BY created_at DESC");
$photo_comments = $conn->query("SELECT * FROM photo_comments ORDER BY created_at DESC");

require 'admin_layout_top.php';
?>
<style>
.tab-menu a{padding:10px 15px;background:#2a2d45;border-radius:8px;color:#fff;text-decoration:none;margin-right:10px;}
.tab-menu a.active{background:#ff4b81;}
.reply-box{margin-top:10px;background:#1f2235;padding:10px;border-radius:8px;}
.reply-text{width:100%;padding:8px;border-radius:6px;border:none;}
.reply-btn{background:#4caf50;padding:6px 10px;border:none;border-radius:6px;margin-top:6px;color:#fff;cursor:pointer;}
.delete-btn{background:#ff4b81;color:#fff;padding:5px 10px;border-radius:6px;}
.unread{background:#ffb347;padding:3px 7px;border-radius:6px;font-size:12px;margin-left:5px;}
</style>

<div class="header">
    <h1>Comments Management</h1>
</div>

<div class="tab-menu">
    <a href="?tab=posts" class="<?php echo $tab==='posts'?'active':''; ?>">Post Comments</a>
    <a href="?tab=photos" class="<?php echo $tab==='photos'?'active':''; ?>">Photo Comments</a>
</div>

<div class="box">
<?php if ($tab === 'posts'): ?>
    <h2>Post Comments</h2>
    <table>
        <tr><th>ID</th><th>Post ID</th><th>Comment</th><th>Reply</th><th>Date</th><th>Action</th></tr>

        <?php if ($post_comments && $post_comments->num_rows): ?>
            <?php while ($c = $post_comments->fetch_assoc()): ?>
            <tr>
                <td><?php echo $c['id']; ?></td>
                <td><?php echo $c['post_id']; ?></td>
                <td>
                    <?php echo nl2br(htmlspecialchars($c['comment'])); ?>
                    <?php if ($c['is_read']==0): ?><span class="unread">new</span><?php endif; ?>
                </td>
                <td>
                    <?php if ($c['admin_reply']): ?>
                        <?php echo nl2br(htmlspecialchars($c['admin_reply'])); ?>
                    <?php else: ?>
                        <form method="POST" class="reply-box">
                            <input type="hidden" name="reply_id" value="<?php echo $c['id']; ?>">
                            <textarea name="reply_text" class="reply-text" required></textarea>
                            <button class="reply-btn">Send Reply</button>
                        </form>
                    <?php endif; ?>
                </td>
                <td><?php echo $c['created_at']; ?></td>
                <td><a class="delete-btn" href="?delete_post=<?php echo $c['id']; ?>">Delete</a></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No comments.</td></tr>
        <?php endif; ?>
    </table>

<?php else: ?>

    <h2>Photo Comments</h2>
    <table>
        <tr><th>ID</th><th>Album</th><th>Photo</th><th>Comment</th><th>Reply</th><th>Date</th><th>Action</th></tr>

        <?php if ($photo_comments && $photo_comments->num_rows): ?>
            <?php while ($c = $photo_comments->fetch_assoc()): ?>
            <tr>
                <td><?php echo $c['id']; ?></td>
                <td><?php echo htmlspecialchars($c['album']); ?></td>
                <td><?php echo htmlspecialchars($c['photo']); ?></td>
                <td>
                    <?php echo nl2br(htmlspecialchars($c['comment'])); ?>
                    <?php if ($c['is_read']==0): ?><span class="unread">new</span><?php endif; ?>
                </td>
                <td>
                    <?php if ($c['admin_reply']): ?>
                        <?php echo nl2br(htmlspecialchars($c['admin_reply'])); ?>
                    <?php else: ?>
                        <form method="POST" class="reply-box">
                            <input type="hidden" name="reply_photo_id" value="<?php echo $c['id']; ?>">
                            <textarea name="reply_photo_text" class="reply-text" required></textarea>
                            <button class="reply-btn">Send Reply</button>
                        </form>
                    <?php endif; ?>
                </td>
                <td><?php echo $c['created_at']; ?></td>
                <td><a class="delete-btn" href="?delete_photo=<?php echo $c['id']; ?>">Delete</a></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No comments.</td></tr>
        <?php endif; ?>
    </table>

<?php endif; ?>
</div>

<?php require 'admin_layout_bottom.php'; ?>
