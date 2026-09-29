<?php
session_start();

$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) {
    die("Database connection failed.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please fill all fields.";
    } else {

        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $error = "Incorrect email or password!";
        } else {

            $stored = $user["password"];
            $valid = false;

            if (password_verify($password, $stored)) $valid = true;
            elseif (strlen($stored) === 32 && md5($password) === $stored) $valid = true;
            elseif ($password === $stored) $valid = true;

            if (!$valid) {
                $error = "Incorrect email or password!";
            } else {

                $_SESSION["user_id"]   = $user["id"];
                $_SESSION["user"]      = $user["email"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["role"]      = $user["role"];

                if ($user["role"] === "admin") {
                    $_SESSION["admin_logged_in"] = true;
                    header("Location: index.php");
                    exit;
                }

                header("Location: ../index.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<style>
    body{
        margin:0;
        font-family:system-ui,Arial;
        background:#07101c;
        color:#fff;
        display:flex;
        justify-content:center;
        align-items:center;
        min-height:100vh;
    }
    .form-box{
        width:100%;
        max-width:420px;
        background:#0d1829;
        border-radius:16px;
        padding:26px 28px 30px;
        box-shadow:0 18px 40px rgba(0,0,0,.7);
    }
    h2{ margin-top:0; margin-bottom:18px; }
    input{
        width:100%;
        padding:12px;
        margin-bottom:12px;
        border-radius:8px;
        border:none;
        background:#111d30;
        color:#fff;
    }
    button{
        width:100%;
        padding:12px;
        background:#0099ff;
        border:none;
        color:white;
        font-weight:bold;
        border-radius:8px;
        cursor:pointer;
    }
    button:hover{ background:#0080d4; }
    a{ color:#66c0ff; text-decoration:none; }
    a:hover{ text-decoration:underline; }
    .error{
        color:#ff8080;
        margin-bottom:10px;
        font-size:14px;
    }
    p{
        font-size:13px;
        margin-top:14px;
        text-align:center;
    }
</style>
</head>
<body>

<div class="form-box">
    <h2>Login</h2>

    <?php if($error): ?>
        <div class="error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>

</body>
</html>
