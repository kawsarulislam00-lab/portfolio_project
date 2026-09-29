<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "portfolio_db";

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if($conn->connect_error){
    die("Connection Failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];

// Insert into database
$sql = "INSERT INTO messages (name, email, message) VALUES ('$name', '$email', '$message')";

if($conn->query($sql) === TRUE){
    echo "<script>alert('Message sent successfully!'); window.location='../index.php';</script>";
}else{
    echo "<script>alert('Error: Message not sent'); window.location='../index.php';</script>";
}

$conn->close();
?>