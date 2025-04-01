<?php
require_once 'config/database.php';

$name = 'Admin User';
$email = 'admin@example.com';
$password = password_hash('admin', PASSWORD_DEFAULT); // Hash the password
$role = 'admin';

$sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $password, $role);

if ($stmt->execute()) {
    echo "Admin user created successfully";
} else {
    echo "Error creating user: " . $conn->error;
}