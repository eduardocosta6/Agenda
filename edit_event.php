<?php
    require_once 'config/database.php';
    require_once 'components/navbar.php';
    require_once 'includes/session.php';

    requireLogin(); // Redirect if not logged in

    if (! isset($_GET['id'])) {
        header("Location: index.php");
        exit();
    }

    $id = $_GET['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title       = $_POST['title'];
        $description = $_POST['description'];
        $event_date  = $_POST['event_date'];

        $user_id = $_SESSION['user_id']; // Get the current user's ID

        $sql  = "UPDATE events SET title = ?, description = ?, event_date = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $title, $description, $event_date, $id, $user_id);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        }
    }

    $user_id = $_SESSION['user_id']; // Get the current user's ID

    $sql  = "SELECT * FROM events WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event  = $result->fetch_assoc();

    if (! $event) {
        // Either the event doesn't exist or it doesn't belong to the current user
        header("Location: index.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - My Agenda</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php renderNavbar(''); ?>

    <div class="container">
        <main>
            <div class="event-form">
                <h2 class="form-title">Edit Event</h2>

                <form method="POST">
                    <div class="form-group">
                        <label for="title"><i class="fas fa-heading"></i> Title:</label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="event_date"><i class="fas fa-calendar-alt"></i> Date and Time:</label>
                        <input type="datetime-local" id="event_date" name="event_date"
                               value="<?php echo date('Y-m-d\TH:i', strtotime($event['event_date'])); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description"><i class="fas fa-align-left"></i> Description:</label>
                        <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($event['description']); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn light"><i class="fas fa-arrow-left"></i> Cancel</a>
                        <button type="submit" class="btn submit"><i class="fas fa-save"></i> Update Event</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

