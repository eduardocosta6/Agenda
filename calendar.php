<?php
require_once 'config/database.php';
require_once 'components/navbar.php';
require_once 'includes/session.php';

// Redirect admin/moderator users to admin panel
if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'moderator'])) {
    header("Location: admin_panel.php");
    exit();
}

// Get current month and year
$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
$year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

// Get events for the current month
$start_date = "$year-$month-01";
$end_date = date('Y-m-t', strtotime($start_date));

$sql = "SELECT id, title, description, event_date FROM events 
        WHERE event_date BETWEEN ? AND ?
        ORDER BY event_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Store events in an array
$events = [];
while ($row = $result->fetch_assoc()) {
    $day = date('j', strtotime($row['event_date']));
    if (!isset($events[$day])) {
        $events[$day] = [];
    }
    $events[$day][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - My Agenda</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/calendar.css">
</head>
<body>
    <?php renderNavbar('calendar'); ?>
    
    <div class="container">
        <div class="calendar-header">
            <h1><?php echo date('F Y', strtotime("$year-$month-01")); ?></h1>
            <div class="calendar-nav">
                <?php
                $prev_month = $month - 1;
                $prev_year = $year;
                if ($prev_month < 1) {
                    $prev_month = 12;
                    $prev_year--;
                }
                
                $next_month = $month + 1;
                $next_year = $year;
                if ($next_month > 12) {
                    $next_month = 1;
                    $next_year++;
                }
                ?>
                <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" class="nav-btn">&lt; Previous</a>
                <a href="?month=<?php echo date('m'); ?>&year=<?php echo date('Y'); ?>" class="nav-btn">Today</a>
                <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="nav-btn">Next &gt;</a>
            </div>
        </div>

        <div class="calendar">
            <div class="calendar-weekdays">
                <?php
                $weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                foreach ($weekdays as $day) {
                    echo "<div class='weekday'>$day</div>";
                }
                ?>
            </div>

            <div class="calendar-days">
                <?php
                $first_day = date('w', strtotime("$year-$month-01"));
                $days_in_month = date('t', strtotime("$year-$month-01"));

                // Add empty cells for days before the first day of the month
                for ($i = 0; $i < $first_day; $i++) {
                    echo "<div class='day empty'></div>";
                }

                // Add cells for each day of the month
                for ($day = 1; $day <= $days_in_month; $day++) {
                    $is_today = ($day == date('j') && $month == date('m') && $year == date('Y'));
                    $day_class = $is_today ? 'day today' : 'day';
                    $formatted_date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    
                    echo "<div class='$day_class' data-date='$formatted_date'>";
                    echo "<span class='date'>$day</span>";
                    
                    if (isset($events[$day])) {
                        echo "<div class='events'>";
                        foreach ($events[$day] as $event) {
                            $time = date('H:i', strtotime($event['event_date']));
                            echo "<div class='event' data-event-id='{$event['id']}'>";
                            echo "<span class='event-time'>$time</span>";
                            echo "<span class='event-title'>" . htmlspecialchars($event['title']) . "</span>";
                            echo "</div>";
                        }
                        echo "</div>";
                    }
                    
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <div class="calendar-actions">
            <a href="add_event.php" class="btn add-event">Add New Event</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Event handling for existing events
            const events = document.querySelectorAll('.event');
            events.forEach(event => {
                event.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent the day click event from firing
                    const eventId = this.dataset.eventId;
                    window.location.href = `edit_event.php?id=${eventId}`;
                });
            });

            // Day click handling
            const days = document.querySelectorAll('.day:not(.empty)');
            days.forEach(day => {
                day.addEventListener('click', function() {
                    const date = this.dataset.date;
                    window.location.href = `add_event.php?date=${date}`;
                });
            });
        });
    </script>
</body>
</html>


