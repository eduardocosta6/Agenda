<?php
    require_once 'config/database.php';
    require_once 'components/navbar.php';
    require_once 'includes/session.php';

    requireLogin(); // Redirect if not logged in

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title       = $_POST['title'];
        $description = $_POST['description'];
        $user_id     = $_SESSION['user_id']; // Get the current user's ID

        // Determine the event date based on form inputs
        if (isset($_POST['selected_date']) && isset($_POST['event_time'])) {
            // Combine date and time
            $event_date = $_POST['selected_date'] . ' ' . $_POST['event_time'] . ':00';
        } else {
            // Use the datetime-local input
            $event_date = $_POST['event_date'];
        }

        $sql  = "INSERT INTO events (user_id, title, description, event_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $user_id, $title, $description, $event_date);

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php renderNavbar('add'); ?>

    <div class="container">
        <main>
            <div class="event-form">
                <h2 class="form-title">Add New Event</h2>

                <form method="POST">
                    <div class="form-group">
                        <label for="title"><i class="fas fa-heading"></i> Title:</label>
                        <input type="text" id="title" name="title" placeholder="Enter event title" required>
                    </div>

                    <?php
                        // Check if date parameter is provided
                        if (isset($_GET['date'])) {
                                                            // Only show time input when date is provided
                            $selected_date = $_GET['date']; // Format: YYYY-MM-DD
                            $default_time  = '12:00';       // Default to noon

                            // Hidden input for the date
                            echo '<input type="hidden" name="selected_date" value="' . htmlspecialchars($selected_date) . '">';

                            // Display the selected date
                            echo '<div class="form-group">';
                            echo '<label><i class="fas fa-calendar-alt"></i> Selected Date:</label>';
                            echo '<div class="selected-date">' . date('l, F j, Y', strtotime($selected_date)) . '</div>';
                            echo '</div>';

                            // Show only time input
                            echo '<div class="form-group">';
                            echo '<label for="event_time"><i class="fas fa-clock"></i> Time:</label>';
                            echo '<input type="time" id="event_time" name="event_time" value="' . $default_time . '" required>';
                            echo '</div>';
                        } else {
                            // Show full datetime input when no date is provided
                            echo '<div class="form-group">';
                            echo '<label for="event_date"><i class="fas fa-calendar-alt"></i> Date and Time:</label>';
                            echo '<input type="datetime-local" id="event_date" name="event_date" required>';
                            echo '</div>';
                        }
                    ?>

                    <div class="form-group">
                        <label for="description"><i class="fas fa-align-left"></i> Description:</label>
                        <textarea id="description" name="description" rows="4" placeholder="Enter event details (optional)"></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="calendar.php" class="btn light"><i class="fas fa-arrow-left"></i> Cancel</a>
                        <button type="submit" class="btn submit"><i class="fas fa-plus"></i> Add Event</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>



