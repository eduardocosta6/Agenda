<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/logger.php';

// Verify admin/moderator access
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'moderator'])) {
    header("Location: index.php");
    exit();
}

// Check if ID and status are provided
if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: admin_panel.php?page=users");
    exit();
}

$id = $_GET['id'];
$new_status = $_GET['status'];

// Validate status
if (!in_array($new_status, ['active', 'inactive'])) {
    header("Location: admin_panel.php?page=users");
    exit();
}

// Get user data before update
$sql = "SELECT name, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin_panel.php?page=users");
    exit();
}

$user = $result->fetch_assoc();

// Prevent status change for admin@example.com
if ($user['email'] === 'admin@example.com') {
    header("Location: admin_panel.php?page=users");
    exit();
}

// Check if moderator is trying to change admin/moderator status
$is_moderator = $_SESSION['user_role'] === 'moderator';
if ($is_moderator && $user['role'] !== 'user') {
    header("Location: admin_panel.php?page=users");
    exit();
}

// Update user status
$update_sql = "UPDATE users SET status = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("si", $new_status, $id);

if ($update_stmt->execute()) {
    // Log the action
    $details = "Changed status of user: {$user['name']} (ID: $id) to $new_status";
    log_action('User Status Change', $details);
    
    // Redirect with success message
    header("Location: admin_panel.php?page=users&message=User status updated successfully");
} else {
    // Redirect with error message
    header("Location: admin_panel.php?page=users&error=Error updating user status: " . $conn->error);
}
exit();
