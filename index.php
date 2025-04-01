<?php
require_once 'config/database.php';
require_once 'components/navbar.php';
require_once 'includes/session.php';

// Redirect admin users to admin panel
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header("Location: admin_panel.php");
    exit();
}

$sql = "SELECT * FROM events ORDER BY event_date ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Agenda</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
</head>
<body>
    <?php renderNavbar('home'); ?>
    
    <div class="container">
        <main>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='event-card'>";
                    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                    echo "<p class='date'>" . date('F j, Y, g:i a', strtotime($row['event_date'])) . "</p>";
                    echo "<p class='description'>" . htmlspecialchars($row['description']) . "</p>";
                    echo "<div class='actions'>";
                    echo "<a href='edit_event.php?id=" . $row['id'] . "' class='btn edit'>Edit</a>";
                    echo "<a href='delete_event.php?id=" . $row['id'] . "' class='btn delete'>Delete</a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p class='no-events'>No events found</p>";
            }
            ?>
        </main>
    </div>
</body>
</html>




