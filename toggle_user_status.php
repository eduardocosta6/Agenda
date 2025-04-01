<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/logger.php';

// Verify admin/moderator access
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'moderator'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $user_id = $_GET['id'];
    $new_status = $_GET['status'];
    $admin_id = $_SESSION['user_id'];
    $admin_role = $_SESSION['user_role'];
    
    // Validate status value
    if (!in_array($new_status, ['active', 'inactive'])) {
        log_action('Security Alert', json_encode([
            'event' => 'Invalid Status Attempt',
            'admin_id' => $admin_id,
            'admin_role' => $admin_role,
            'target_user' => $user_id,
            'attempted_status' => $new_status
        ]));
        header("Location: admin_panel.php?page=users&error=invalid_status");
        exit();
    }
    
    // Check user permissions and target user role
    $check_sql = "SELECT email, role, status, name FROM users WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        header("Location: admin_panel.php?page=users&error=user_not_found");
        exit();
    }
    
    // Prevent deactivating admin@example.com
    if ($user['email'] === 'admin@example.com') {
        header("Location: admin_panel.php?page=users&error=cannot_deactivate_admin");
        exit();
    }
    
    // If moderator, can only modify regular users
    if ($_SESSION['user_role'] === 'moderator' && $user['role'] !== 'user') {
        header("Location: admin_panel.php?page=users&error=insufficient_permissions");
        exit();
    }
    
    // Update user status
    $sql = "UPDATE users SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $user_id);
    
    if ($stmt->execute()) {
        log_action('User Status Change', "Changed user ID: $user_id status to: $new_status");
        header("Location: admin_panel.php?page=users&success=status_updated");
    } else {
        error_log("MySQL Error: " . $stmt->error);
        header("Location: admin_panel.php?page=users&error=update_failed");
    }
} else {
    header("Location: admin_panel.php?page=users");
}
exit();





