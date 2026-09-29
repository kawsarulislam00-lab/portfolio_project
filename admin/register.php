<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio_db");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($name === "" || $email === "" || $password === "") {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $chk = $stmt->get_result()->num_rows;
        $stmt->close();

        if ($chk > 0) {
            $error = "Email already exists!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users(name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $name, $email, $hash);
            $stmt->execute();
            $stmt->close();

            $_SESSION["user"] = $email;
            $_SESSION["role"] = "user";
            $_SESSION["user_name"] = $name;

            header("Location: ../index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<style>
body{
    font-family:Arial;
    background:#07101c;
    color:white;
}
.form-box{
    max-width:400px;
    margin:80px auto;
    padding:25px;
    background:#0d1829;
    border-radius:12px;
}
input{
    width:100%;
    padding:12px;
    margin-bottom:10px;
    border:none;
    border-radius:6px;
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
    border-radius:6px;
    cursor:pointer;
}
button:hover{
    background:#0080d4;
}
a{
    color:#66c0ff;
    text-decoration:none;
}
a:hover{
    text-decoration:underline;
}
.error{
    color:#ff8080;
    margin-bottom:10px;
    font-size:14px;
}
</style>
</head>
<body>

<div class="form-box">
<h2>Create Account</h2>

<?php if($error): ?>
    <div class="error"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="login.php">Login</a></p>
</div>

</body>
</html>
