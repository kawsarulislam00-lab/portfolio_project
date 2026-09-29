<?php
session_start();

$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) { die("DB connection failed."); }

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm'] ?? '');

    if ($name === '' || $email === '' || $password === '' || $confirm === '') {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';
            $ins = $conn->prepare("INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)");
            $ins->bind_param("ssss", $name, $email, $hash, $role);
            if ($ins->execute()) {
                $_SESSION['user']      = true;
                $_SESSION['user_id']   = $ins->insert_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email']= $email;
                $_SESSION['role']      = $role;
                header("Location: index.php");
                exit;
            } else {
                $error = "Registration failed. Try again.";
            }
            $ins->close();
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Register - Kawsarul Islam</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="css/style.css">
<style>
.auth-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0b1a2a;color:#fff;font-family:system-ui,Arial;}
.auth-box{background:#111b2a;padding:26px 26px 30px;border-radius:16px;box-shadow:0 18px 40px rgba(0,0,0,.6);width:100%;max-width:380px;}
.auth-box h2{margin-top:0;margin-bottom:8px;text-align:center;}
.auth-box p{margin-top:0;margin-bottom:18px;text-align:center;font-size:13px;opacity:.8;}
.auth-box input{width:100%;margin-bottom:10px;padding:10px 12px;border-radius:8px;border:1px solid #333;background:#060a10;color:#fff;box-sizing:border-box;}
.auth-box button{width:100%;padding:10px 12px;border:none;border-radius:8px;background:#00c853;color:#fff;font-weight:600;cursor:pointer;margin-top:4px;}
.auth-box .alt{margin-top:12px;font-size:13px;text-align:center;}
.err{background:#ff5252;padding:8px 10px;border-radius:8px;font-size:13px;margin-bottom:10px;text-align:center;}
</style>
</head>
<body>
<div class="auth-wrap">
    <form class="auth-box" method="post" action="register.php">
        <h2>Create Account</h2>
        <p>Join the community to share travels and more.</p>

        <?php if($error): ?>
            <div class="err"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <input type="text" name="name" placeholder="Full Name" required value="<?php echo isset($name)?htmlspecialchars($name):''; ?>">
        <input type="email" name="email" placeholder="Email" required value="<?php echo isset($email)?htmlspecialchars($email):''; ?>">
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm" placeholder="Confirm Password" required>

        <button type="submit">Register</button>
        <div class="alt">
            Already have an account? <a href="login.php" style="color:#ffc107;">Login</a>
        </div>
    </form>
</div>
</body>
</html>
