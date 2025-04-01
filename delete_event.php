<?php
require_once 'config/database.php';
require_once 'includes/session.php';

requireLogin(); // Redirect if not logged in

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "DELETE FROM events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    $stmt->execute();
}

header("Location: index.php");
exit();
