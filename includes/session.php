<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Add this new function to update user activity
function updateUserActivity() {
    global $conn;
    
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $session_id = session_id();
        
        $sql = "INSERT INTO user_sessions (user_id, session_id, last_activity) 
                VALUES (?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE last_activity = NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $user_id, $session_id);
        $stmt->execute();
        
        // Clean old sessions (older than 15 minutes)
        $conn->query("DELETE FROM user_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
    }
}

// Call this function on every page load
if (isLoggedIn()) {
    updateUserActivity();
}
