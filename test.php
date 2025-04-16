<?php
// Start the session
session_start();

// Include database connection
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Validate email format
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format. Please enter a valid email address."]);
        exit();
    }

    // Validate password strength
    if (strlen($newPassword) < 8 || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/\d/', $newPassword)) {
        echo json_encode(["status" => "error", "message" => "Password must be at least 8 characters long, contain at least one uppercase letter, and at least one digit."]);
        exit();
    }

    // Check if passwords match
    if ($newPassword !== $confirmPassword) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match. Please try again."]);
        exit();
    }

    // Prepare statements to check if username or email already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE username = ?");
    $stmt->bind_param("s", $newUsername);
    $stmt->execute();
    $stmt->bind_result($usernameCount);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
    $stmt->bind_param("s", $newEmail);
    $stmt->execute();
    $stmt->bind_result($emailCount);
    $stmt->fetch();
    $stmt->close();

    // Determine which error message to return
    if ($usernameCount > 0 && $emailCount > 0) {
        echo json_encode(["status" => "error", "message" => "Username and Email are already taken."]);
    } elseif ($usernameCount > 0) {
        echo json_encode(["status" => "error", "message" => "Username is already taken."]);
    } elseif ($emailCount > 0) {
        echo json_encode(["status" => "error", "message" => "Email is already used."]);
    } else {
        // Insert the new user
        $stmt = $conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
        if ($stmt === false) {
            echo json_encode(["status" => "error", "message" => "Error preparing statement: " . $conn->error]);
            exit();
        }

        $stmt->bind_param("sss", $newUsername, $newEmail, $newPassword);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Sign Up Successful!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error executing statement: " . $stmt->error]);
        }

        $stmt->close();
    }
}

$conn->close();
?>
