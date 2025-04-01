<?php
require_once 'config/database.php';
require_once 'components/navbar.php';
require_once 'includes/session.php';

// Redirect admin/moderator users to admin panel
if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'moderator'])) {
    header("Location: admin_panel.php");
    exit();
}

// Only fetch events if user is logged in
if (isLoggedIn()) {
    $sql = "SELECT * FROM events 
            WHERE event_date >= CURDATE() 
            ORDER BY event_date ASC";
    $result = $conn->query($sql);
}
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
            <?php if (isLoggedIn()): ?>
                <h1 class="page-title">Upcoming Events</h1>
                
                <?php if ($result && $result->num_rows > 0): ?>
                    <div class="events-timeline">
                        <?php 
                        $current_date = null;
                        while($row = $result->fetch_assoc()): 
                            $event_date = date('Y-m-d', strtotime($row['event_date']));
                            
                            if ($event_date !== $current_date):
                                if ($current_date !== null) {
                                    echo "</div>";
                                }
                                $current_date = $event_date;
                                $date_display = date('l, F j, Y', strtotime($event_date));
                                echo "<div class='date-group'>";
                                echo "<h2 class='date-header'>$date_display</h2>";
                            endif;
                        ?>
                            <div class="event-card">
                                <div class="event-time">
                                    <?php echo date('g:i A', strtotime($row['event_date'])); ?>
                                </div>
                                <div class="event-content">
                                    <h3 class="event-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                    <p class="event-description"><?php echo htmlspecialchars($row['description']); ?></p>
                                    <div class="event-actions">
                                        <a href="edit_event.php?id=<?php echo $row['id']; ?>" class="btn edit">Edit</a>
                                        <a href="delete_event.php?id=<?php echo $row['id']; ?>" class="btn delete">Delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        </div><!-- Close last date group -->
                    </div>
                <?php else: ?>
                    <div class="no-events">
                        <p>No upcoming events</p>
                        <p>Visit the <a href="calendar.php">calendar</a> to add new events.</p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="welcome-section">
                    <h1 class="welcome-title">Welcome to My Agenda</h1>
                    <p class="welcome-text">Please login to view and manage your events.</p>
                    <div class="welcome-actions">
                        <a href="login.php" class="btn login">Login</a>
                        <a href="register.php" class="btn register">Register</a>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>





