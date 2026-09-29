<?php
session_start();
$isLoggedIn = isset($_SESSION['user']);
$userRole   = $_SESSION['role'] ?? 'user';
$userName   = $_SESSION['user_name'] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kawsarul Islam</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<header class="site-header">
<div class="header-container">
    <div class="logo">Kawsarul<span>Islam</span></div>

    <nav class="menu">
        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>">Home</a>
        <a href="about.php" class="<?= basename($_SERVER['PHP_SELF'])=='about.php'?'active':'' ?>">About</a>
        <a href="travels.php" class="<?= basename($_SERVER['PHP_SELF'])=='travels.php'?'active':'' ?>">Travels</a>
        <a href="index.php#contact">Contact</a>

        <?php if($isLoggedIn): ?>

            <?php if($userRole === 'admin'): ?>
                <a href="admin/index.php" style="color:#ffd54f;font-weight:600;">Admin Panel</a>
            <?php else: ?>
                <a href="my_gallery.php">My Gallery</a>
            <?php endif; ?>

            <a href="logout.php" style="color:#ff6666;">Logout</a>

        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>

    </nav>
</div>
</header>
