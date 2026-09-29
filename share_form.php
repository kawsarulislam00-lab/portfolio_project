<?php include "header.php"; ?>

<style>
.share-container{
    max-width:650px;
    margin:40px auto;
    background:#ffffff;
    padding:25px;
    border-radius:12px;
    box-shadow:0 4px 25px rgba(0,0,0,0.12);
    font-family:Arial, sans-serif;
}
.share-container h2{
    text-align:center;
    margin-bottom:10px;
    font-size:24px;
    color:#222;
}
.share-container input,
.share-container textarea{
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:15px;
}
.share-container button{
    background:#ff8800;
    padding:14px 18px;
    border:none;
    color:white;
    border-radius:8px;
    cursor:pointer;
    font-weight:bold;
    width:100%;
    font-size:16px;
}
.share-container button:hover{
    background:#ff6f00;
}
.success-popup{
    width:100%;
    background:#d4ffe1;
    border-left:5px solid #1ecb71;
    padding:12px;
    margin-bottom:15px;
    border-radius:6px;
    color:#0d6b35;
    font-weight:bold;
    text-align:center;
}
</style>

<?php
$msg = "";

if(isset($_POST['submit'])){
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $title = $_POST['title'];
    $story = $_POST['story'];

    $imagePath = "";
    if (!empty($_FILES['image']['name'])) {
        $folder = "uploads/community/";
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $imgName = time() . "_" . basename($_FILES['image']['name']);
        $target = $folder . $imgName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $imagePath = $target;
        }
    }

    $conn = new mysqli("localhost", "root", "", "portfolio_db");

    $stmt = $conn->prepare("
        INSERT INTO travel_posts (name, email, title, story, image, status, created_at)
        VALUES (?, ?, ?, ?, ?, 'pending', NOW())
    ");

    $stmt->bind_param("sssss", $name, $email, $title, $story, $imagePath);
    $stmt->execute();

    $msg = "Your post has been submitted! Waiting for admin approval.";
}
?>

<div class="share-container">
    <h2>Share Your Travel Experience</h2>

    <?php if($msg != ""): ?>
        <div class="success-popup"><?php echo $msg; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <input type="text" name="title" placeholder="Travel Title" required>
        <textarea name="story" placeholder="Write your travel experience..." rows="5" required></textarea>

        <label><b>Upload Photo:</b></label>
        <input type="file" name="image" accept="image/*">

        <button type="submit" name="submit">Submit Post</button>
    </form>
</div>

<?php include "footer.php"; ?>
