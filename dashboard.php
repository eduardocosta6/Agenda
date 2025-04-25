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

    // Get user statistics
    $total_users_sql    = "SELECT COUNT(*) as count FROM users";
    $active_users_sql   = "SELECT COUNT(*) as count FROM users WHERE status = 'active'";
    $inactive_users_sql = "SELECT COUNT(*) as count FROM users WHERE status = 'inactive'";

    $total_users    = $conn->query($total_users_sql)->fetch_assoc()['count'];
    $active_users   = $conn->query($active_users_sql)->fetch_assoc()['count'];
    $inactive_users = $conn->query($inactive_users_sql)->fetch_assoc()['count'];

    // Get event statistics
    $total_events_sql    = "SELECT COUNT(*) as count FROM events";
    $upcoming_events_sql = "SELECT COUNT(*) as count FROM events WHERE event_date >= CURDATE()";
    $past_events_sql     = "SELECT COUNT(*) as count FROM events WHERE event_date < CURDATE()";

    $total_events    = $conn->query($total_events_sql)->fetch_assoc()['count'];
    $upcoming_events = $conn->query($upcoming_events_sql)->fetch_assoc()['count'];
    $past_events     = $conn->query($past_events_sql)->fetch_assoc()['count'];

    // Get recent logs
    $recent_logs_sql = "SELECT * FROM logs ORDER BY created_at DESC LIMIT 5";
    $recent_logs     = $conn->query($recent_logs_sql);

    // Get recent users
    $recent_users_sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
    $recent_users     = $conn->query($recent_users_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - My Agenda</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-layout">
        <?php renderAdminSidebar('dashboard', $is_moderator); ?>

        <div class="content">
            <div class="content-header">
                <h1>Dashboard</h1>
            </div>

            <div class="dashboard-stats">
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Users</h3>
                            <p class="stat-value"><?php echo $total_users; ?></p>
                            <div class="stat-details">
                                <span class="active"><?php echo $active_users; ?> Active</span>
                                <span class="inactive"><?php echo $inactive_users; ?> Inactive</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon events">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Events</h3>
                            <p class="stat-value"><?php echo $total_events; ?></p>
                            <div class="stat-details">
                                <span class="upcoming"><?php echo $upcoming_events; ?> Upcoming</span>
                                <span class="past"><?php echo $past_events; ?> Past</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-sections">
                <div class="section">
                    <h2><i class="fas fa-user-plus"></i> Recent Users</h2>
                    <div class="data-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $recent_users->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                                        <td>
                                            <span class="status-badge                                                                                                                                                                                                                                                                                     <?php echo $user['status']; ?>">
                                                <?php echo ucfirst($user['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="section-footer">
                        <a href="admin_panel.php?page=users" class="view-all-btn" title="View All Users">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="section">
                    <h2><i class="fas fa-history"></i> Recent Activity</h2>
                    <div class="data-table logs-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>User</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($log = $recent_logs->fetch_assoc()):
                                                                      // Determine log severity class based on action type
                                        $action_class = 'log-normal'; // Default class

                                        // High severity actions (red text)
                                        if (strpos($log['action'], 'Failed Login') !== false ||
                                        strpos($log['action'], 'User Deletion') !== false ||
                                        strpos($log['action'], 'Error') !== false) {
                                            $action_class = 'log-danger';
                                        }
                                        // Warning actions (yellow/orange text)
                                    elseif (strpos($log['action'], 'User Status Change') !== false ||
                                        strpos($log['action'], 'Deactivate') !== false) {
                                        $action_class = 'log-warning';
                                    }
                                    // Success actions (green text)
                                    elseif (strpos($log['action'], 'Login Success') !== false ||
                                        strpos($log['action'], 'Success') !== false) {
                                        $action_class = 'log-success';
                                    }
                                    // Info actions (blue text)
                                    elseif (strpos($log['action'], 'User Update') !== false ||
                                        strpos($log['action'], 'View') !== false) {
                                        $action_class = 'log-info';
                                    }
                                ?>
                                    <tr class="<?php echo $action_class; ?>">
                                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                                        <td><?php echo htmlspecialchars($log['user']); ?></td>
                                        <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="section-footer">
                        <a href="admin_panel.php?page=logs" class="view-all-btn" title="View All Logs">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dashboard-stats {
            margin-bottom: 2rem;
        }

        .stats-row {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .stat-card {
            flex: 1;
            min-width: 250px;
            background-color: var(--white-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            border: 1px solid var(--border-color);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--white-color);
        }

        .stat-icon.users {
            background-color: var(--primary-color);
        }

        .stat-icon.events {
            background-color: var(--success-color);
        }

        .stat-content {
            flex: 1;
        }

        .stat-content h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1rem;
            color: var(--text-muted);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            margin: 0 0 0.5rem 0;
            color: var(--dark-color);
        }

        .stat-details {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
        }

        .stat-details .active, .stat-details .upcoming {
            color: var(--success-color);
        }

        .stat-details .inactive, .stat-details .past {
            color: var(--danger-color);
        }

        .dashboard-sections {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .section {
            background-color: var(--white-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            padding: 1.5rem;
            border: 1px solid var(--border-color);
        }

        .section h2 {
            margin: 0 0 1rem 0;
            font-size: 1.25rem;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-footer {
            margin-top: 1rem;
            text-align: right;
        }

        .view-all-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .view-all-btn:hover {
            color: var(--primary-dark);
        }

        @media (max-width: 768px) {
            .stats-row {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>
