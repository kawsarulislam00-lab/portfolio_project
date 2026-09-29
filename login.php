<?php
session_start();

$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) {
    die("DB connection failed.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please fill all fields.";
    } else {

        // Fetch user
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $error = "Incorrect email or password!";
        } else {

            $dbPass = $user["password"];
            $ok = false;

            if (password_verify($password, $dbPass)) {
                $ok = true;
            } elseif (strlen($dbPass) === 32 && md5($password) === $dbPass) {
                $ok = true;
            } elseif ($password === $dbPass) {
                $ok = true;
            }

            if (!$ok) {
                $error = "Incorrect email or password!";
            } else {
                // SUCCESS LOGIN
                $_SESSION["user_id"]   = $user["id"];
                $_SESSION["user"]      = $user["email"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["role"]      = $user["role"];

                if ($user["role"] === "admin") {
                    $_SESSION["admin_logged_in"] = true;
                    header("Location: admin/index.php");
                    exit;
                }

                header("Location: index.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<style>
body{font-family:Arial;background:#07101c;color:white;}
.form-box{max-width:400px;margin:80px auto;padding:25px;background:#0d1829;border-radius:12px;}
input{width:100%;padding:12px;margin-bottom:10px;border:none;border-radius:6px;background:#111d30;color:white;}
button{width:100%;padding:12px;background:#0099ff;border:none;color:white;font-weight:bold;border-radius:6px;}
a{color:#66c0ff;}
.error{color:#ff8080;margin-bottom:10px;font-size:14px;}
</style>
</head>
<body>

<div class="form-box">
<h2>Login</h2>

<?php if(!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>
    <button name="login">Login</button>
</form>

<p>Don't have an account? <a href="register.php">Register</a></p>
</div>

</body>
</html>
