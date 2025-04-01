<?php
require_once 'config/database.php';
require_once 'components/navbar.php';
require_once 'includes/session.php';

requireLogin(); // Redirect if not logged in

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];

    $sql = "INSERT INTO events (title, description, event_date) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $title, $description, $event_date);
    
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - My Agenda</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
</head>
<body>
    <?php renderNavbar('add'); ?>
    
    <div class="container">
        <main>
            <form method="POST" class="event-form">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="event_date">Date and Time:</label>
                    <input type="datetime-local" id="event_date" name="event_date" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>

                <button type="submit" class="btn submit">Add Event</button>
            </form>
        </main>
    </div>
</body>
</html>



