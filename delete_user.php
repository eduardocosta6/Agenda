<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/logger.php';

// Verify admin access (only admins can delete users, not moderators)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Check if trying to delete admin@example.com
    $check_sql = "SELECT email FROM users WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $user['email'] === 'admin@example.com') {
        header("Location: admin_panel.php?page=users&error=cannot_delete_admin");
        exit();
    }
    
    // Delete user
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        log_action('User Deletion', "Deleted user ID: $user_id");
        header("Location: admin_panel.php?page=users&success=user_deleted");
    } else {
        header("Location: admin_panel.php?page=users&error=delete_failed");
    }
} else {
    header("Location: admin_panel.php?page=users");
}
exit();