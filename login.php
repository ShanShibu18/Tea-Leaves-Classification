<?php
session_start();
include 'db_connection.php'; // Ensure this file properly connects to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check user credentials
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Since passwords are NOT hashed, use direct comparison
    if ($user && $password === $user['password']) { 
        $_SESSION['username'] = $username; // Store user session
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid username or password"]);
    }
}
?>
