<?php
require_once 'config/database.php';
require_once 'components/navbar.php';
require_once 'includes/session.php';

requireLogin(); // Redirect if not logged in

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];

    $sql = "UPDATE events SET title = ?, description = ?, event_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $description, $event_date, $id);
    
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    }
}

$sql = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
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
</head>
<body>
    <?php renderNavbar(''); ?>
    
    <div class="container">
        <main>
            <form method="POST" class="event-form">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="event_date">Date and Time:</label>
                    <input type="datetime-local" id="event_date" name="event_date" 
                           value="<?php echo date('Y-m-d\TH:i', strtotime($event['event_date'])); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>

                <button type="submit" class="btn submit">Update Event</button>
            </form>
        </main>
    </div>
</body>
</html>

