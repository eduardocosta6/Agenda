<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/logger.php';

// Verify admin access (not moderator)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: admin_panel.php?page=users");
    exit();
}

$id = $_GET['id'];

// Get user data before deletion
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin_panel.php?page=users");
    exit();
}

$user = $result->fetch_assoc();

// Prevent deletion of admin@example.com
if ($user['email'] === 'admin@example.com') {
    header("Location: admin_panel.php?page=users");
    exit();
}

// Delete user
$delete_sql = "DELETE FROM users WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    // Log the action
    $details = "Deleted user: {$user['name']} (ID: $id, Email: {$user['email']})";
    log_action('User Deletion', $details);
    
    // Redirect with success message
    header("Location: admin_panel.php?page=users&message=User deleted successfully");
} else {
    // Redirect with error message
    header("Location: admin_panel.php?page=users&error=Error deleting user: " . $conn->error);
}
exit();
