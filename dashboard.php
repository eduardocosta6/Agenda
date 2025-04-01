<?php
    require_once 'config/database.php';
    require_once 'includes/session.php';
    require_once 'components/admin_sidebar.php';

    // Verify admin/moderator access
    if (! isset($_SESSION['user_role']) || ! in_array($_SESSION['user_role'], ['admin', 'moderator'])) {
        header("Location: index.php");
        exit();
    }

    $is_moderator = $_SESSION['user_role'] === 'moderator';

    // Get total users count
    $users_sql = $is_moderator
    ? "SELECT COUNT(*) as count FROM users WHERE role = 'user'"
    : "SELECT COUNT(*) as count FROM users";
    $users_result = $conn->query($users_sql);
    $users_count  = $users_result->fetch_assoc()['count'];

    // Get recent logs - only login attempts (successful and failed)
    $logs_sql = $is_moderator
    ? "SELECT * FROM logs
       WHERE action NOT LIKE '%Admin%'
       AND (action LIKE '%Login%' OR action LIKE '%Failed Login%')
       ORDER BY created_at DESC
       LIMIT 5"
    : "SELECT * FROM logs
       WHERE (action LIKE '%Login%' OR action LIKE '%Failed Login%')
       ORDER BY created_at DESC
       LIMIT 5";
    $logs_result = $conn->query($logs_sql);

    // Get online users count
    $online_users_sql = "SELECT u.name, u.role, us.last_activity
                     FROM user_sessions us
                     JOIN users u ON us.user_id = u.id
                     WHERE us.last_activity > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                     ORDER BY us.last_activity DESC";
    $online_users_result = $conn->query($online_users_sql);
    $online_users_count  = $online_users_result ? $online_users_result->num_rows : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - My Agenda</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php renderAdminSidebar('dashboard', $is_moderator); ?>

        <div class="content">
            <div class="content-header">
                <h1>Dashboard</h1>
            </div>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p class="stat-number"><?php echo $users_count; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Users Online</h3>
                    <p class="stat-number"><?php echo $online_users_count; ?></p>
                </div>
            </div>

            <div class="online-users">
                <h2>Currently Online (<?php echo $online_users_count; ?>)</h2>
                <div class="online-users-grid">
                    <?php while ($online_user = $online_users_result->fetch_assoc()): ?>
                        <div class="online-user-card">
                            <div class="user-info">
                                <span class="user-name"><?php echo htmlspecialchars($online_user['name']); ?></span>
                                <span class="user-role                                                       <?php echo $online_user['role']; ?>">
                                    <?php echo ucfirst($online_user['role']); ?>
                                </span>
                            </div>
                            <div class="last-seen online">
                                <span class="online-indicator"></span> Online
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="recent-activity">
                <h2>Recent Activity</h2>
                <div class="activity-list">
                    <?php while ($log = $logs_result->fetch_assoc()): ?>
                        <div class="activity-item">
                            <span class="activity-time"><?php echo date('M d, H:i', strtotime($log['created_at'])); ?></span>
                            <span class="activity-action"><?php echo htmlspecialchars($log['action']); ?></span>
                            <span class="activity-details"><?php echo htmlspecialchars($log['details']); ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>






