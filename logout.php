<?php
require_once 'config/database.php';
require_once 'includes/session.php';

// Remove from online users
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $session_id = session_id();
    $sql = "DELETE FROM user_sessions WHERE user_id = ? AND session_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_id, $session_id);
    $stmt->execute();
}

session_destroy();
header("Location: index.php");
exit();
