<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";

// Create connections for both databases
$conn1 = new mysqli($servername, $username, $password, "login1");
$conn2 = new mysqli($servername, $username, $password, "login2");

// Check connections
if ($conn1->connect_error) {
    die("Connection to login1 failed: " . $conn1->connect_error);
}
if ($conn2->connect_error) {
    die("Connection to login2 failed: " . $conn2->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $hashed_confirm_password = password_hash($confirm_password, PASSWORD_DEFAULT);

    // Insert into users table in login1
    $sql1 = "INSERT INTO users (username, password, name, confirm_password) VALUES (?, ?, ?, ?)";
    $stmt1 = $conn1->prepare($sql1);
    $stmt1->bind_param("ssss", $email, $hashed_password, $name, $hashed_confirm_password);

    // Insert into users table in login2
    $sql2 = "INSERT INTO users (username, password, name, confirm_password) VALUES (?, ?, ?, ?)";
    $stmt2 = $conn2->prepare($sql2);
    $stmt2->bind_param("ssss", $email, $hashed_password, $name, $hashed_confirm_password);

    // Execute both statements
    if ($stmt1->execute() && $stmt2->execute()) {
        echo "New account created successfully in both databases.";
    } else {
        echo "Error: " . $conn1->error . "<br>" . $conn2->error;
    }

    $stmt1->close();
    $stmt2->close();
}

$conn1->close();
$conn2->close();
?>
