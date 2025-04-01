<?php
function log_action($action, $details = '') {
    global $conn;
    $user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'System';
    
    $sql = "INSERT INTO logs (action, user, details, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $action, $user, $details);
    $stmt->execute();
}
