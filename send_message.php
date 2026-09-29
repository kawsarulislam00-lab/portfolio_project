<?php
$conn = new mysqli("localhost","root","","portfolio_db");
if ($conn->connect_error) {
    die("DB error");
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php#contact");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$messageText = trim($_POST["message"] ?? "");
$subject = "Website contact message";

if ($name === "" || $email === "" || $messageText === "") {
    header("Location: index.php#contact");
    exit;
}

$stmt = $conn->prepare("INSERT INTO contact_messages (name,email,subject,message,is_read,created_at) VALUES (?,?,?,?,0,NOW())");
$stmt->bind_param("ssss",$name,$email,$subject,$messageText);
$stmt->execute();
$messageId = $stmt->insert_id;

$settingsRes = $conn->query("SELECT contact_email FROM settings LIMIT 1");
$settings = $settingsRes ? $settingsRes->fetch_assoc() : null;
$adminEmail = ($settings && !empty($settings["contact_email"])) ? $settings["contact_email"] : "kawsarulislam.00@gmail.com";

$mailSubject = "New message from ".$name;
$mailBody = "Name: ".$name."\nEmail: ".$email."\n\nMessage:\n".$messageText."\n\nMessage ID: ".$messageId;
$mailHeaders = "From: ".$adminEmail."\r\nReply-To: ".$email."\r\n";

@mail($adminEmail,$mailSubject,$mailBody,$mailHeaders);

header("Location: index.php?sent=1#contact");
exit;
