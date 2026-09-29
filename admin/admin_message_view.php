<?php
require __DIR__ . '/admin_auth.php';

if (!isset($conn)) {
    $conn = new mysqli("localhost", "root", "", "portfolio_db");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: admin_messages.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $replyText = trim($_POST["reply_text"] ?? "");

    if ($replyText !== "") {
        $stmt = $conn->prepare("INSERT INTO message_replies (message_id, reply_text, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $id, $replyText);
        $stmt->execute();

        $conn->query("UPDATE contact_messages SET is_read = 1 WHERE id = $id");

        $msgRes = $conn->query("SELECT name, email, subject FROM contact_messages WHERE id = $id LIMIT 1");
        $msgRow = $msgRes ? $msgRes->fetch_assoc() : null;

        if ($msgRow) {
            $emailTo = $msgRow["email"];
            $nameTo = $msgRow["name"];
            $subjectOrig = $msgRow["subject"];

            $mailSub = "Reply: " . $subjectOrig;
            $mailBody = "Hello " . $nameTo . ",\n\n" . $replyText . "\n\nBest regards,\nKawsarul Islam";

            $settings = $conn->query("SELECT contact_email FROM settings LIMIT 1")->fetch_assoc();
            $fromEmail = $settings && !empty($settings["contact_email"]) ? $settings["contact_email"] : "kawsarulislam.00@gmail.com";

            $headers = "From: $fromEmail\r\nReply-To: $fromEmail\r\n";
            @mail($emailTo, $mailSub, $mailBody, $headers);
        }

        header("Location: admin_message_view.php?id=$id&sent=1");
        exit;
    }
}

$conn->query("UPDATE contact_messages SET is_read = 1 WHERE id = $id");

$msgRes = $conn->query("SELECT * FROM contact_messages WHERE id = $id LIMIT 1");
if (!$msgRes || !$msgRes->num_rows) {
    header("Location: admin_messages.php");
    exit;
}

$message = $msgRes->fetch_assoc();
$repliesRes = $conn->query("SELECT * FROM message_replies WHERE message_id = $id ORDER BY created_at ASC");

require 'admin_layout_top.php';
?>

<div class="header">
    <h1>Message #<?php echo $message['id']; ?></h1>
    <a href="admin_messages.php" class="btn">Back to Messages</a>
</div>

<style>
.msg-detail-label{font-size:13px;opacity:.7;margin-bottom:4px;}
.msg-detail-value{font-size:15px;margin-bottom:12px;}
.reply-list{list-style:none;padding:0;margin:0;}
.reply-item{border-bottom:1px solid #333;padding:8px 0;font-size:14px;}
.reply-item-time{font-size:12px;opacity:.7;margin-bottom:3px;}
.reply-form textarea{width:100%;min-height:100px;border:none;border-radius:8px;padding:10px;background:#1d2033;color:#fff;}
.reply-form button{margin-top:10px;background:#4caf50;color:#fff;}
.msg-toast{background:#2e7d32;padding:8px 12px;border-radius:8px;font-size:13px;margin-bottom:10px;display:inline-block;}
</style>

<div class="box">
    <div class="msg-detail-label">From</div>
    <div class="msg-detail-value">
        <?php echo htmlspecialchars($message["name"]); ?> &lt;<?php echo htmlspecialchars($message["email"]); ?>&gt;
    </div>

    <div class="msg-detail-label">Subject</div>
    <div class="msg-detail-value"><?php echo htmlspecialchars($message["subject"]); ?></div>

    <div class="msg-detail-label">Message</div>
    <div class="msg-detail-value"><?php echo nl2br(htmlspecialchars($message["message"])); ?></div>

    <div class="msg-detail-label">Received</div>
    <div class="msg-detail-value"><?php echo $message["created_at"]; ?></div>
</div>

<div class="box">
    <?php if (isset($_GET["sent"]) && $_GET["sent"] == "1"): ?>
        <div class="msg-toast">Reply sent and email delivered.</div>
    <?php endif; ?>

    <h2>Replies</h2>

    <ul class="reply-list">
        <?php if ($repliesRes && $repliesRes->num_rows): ?>
            <?php while ($r = $repliesRes->fetch_assoc()): ?>
                <li class="reply-item">
                    <div class="reply-item-time"><?php echo $r["created_at"]; ?></div>
                    <div><?php echo nl2br(htmlspecialchars($r["reply_text"])); ?></div>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="reply-item">No replies yet.</li>
        <?php endif; ?>
    </ul>

    <h3 style="margin-top:18px;">Send a Reply</h3>
    <form method="POST" class="reply-form">
        <textarea name="reply_text" placeholder="Type your reply..." required></textarea>
        <button type="submit" class="btn">Send Reply</button>
    </form>
</div>

<?php require 'admin_layout_bottom.php'; ?>
