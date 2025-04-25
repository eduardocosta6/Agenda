<?php
require_once 'config/database.php';
require_once 'includes/session.php';

requireLogin(); // Redirect if not logged in

if (isset($_GET['id'])) {
    $id      = $_GET['id'];
    $user_id = $_SESSION['user_id']; // Get the current user's ID

    // Only delete the event if it belongs to the current user
    $sql  = "DELETE FROM events WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $user_id);

    $stmt->execute();
}

header("Location: index.php");
exit();
