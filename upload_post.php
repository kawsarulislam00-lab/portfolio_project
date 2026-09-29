<?php
$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) { die("Database connection failed"); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = $_POST['name'] ?? "";
    $email = $_POST['email'] ?? "";
    $title = $_POST['title'] ?? "";
    $story = $_POST['description'] ?? "";

    $imagePath = "";

    if (!empty($_FILES['image']['name'])) {
        $folder = "uploads/posts/";
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $target = $folder . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $imagePath = $target;
        }
    }

    $stmt = $conn->prepare("
        INSERT INTO travel_posts (name, email, title, story, image, status, created_at)
        VALUES (?, ?, ?, ?, ?, 'pending', NOW())
    ");

    $stmt->bind_param("sssss", $name, $email, $title, $story, $imagePath);
    $stmt->execute();

    echo "<script>
            alert('Your post has been submitted for admin approval!');
            window.location.href='travels.php';
          </script>";
}
?>
