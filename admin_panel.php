<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'components/admin_sidebar.php';

// Verify admin/moderator access
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'moderator'])) {
    header("Location: index.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'users';
$is_moderator = $_SESSION['user_role'] === 'moderator';

// Fetch data based on page and role
switch($page) {
    case 'users':
        if ($is_moderator) {
            // Moderators can only see regular users
            $sql = "SELECT id, name, email, role, status, created_at FROM users WHERE role = 'user' ORDER BY created_at DESC";
        } else {
            $sql = "SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC";
        }
        $result = $conn->query($sql);
        break;
    case 'logs':
        if ($is_moderator) {
            // Moderators see only user-related logs
            $sql = "SELECT * FROM logs WHERE action NOT LIKE '%Admin%' ORDER BY created_at DESC LIMIT 100";
        } else {
            $sql = "SELECT * FROM logs ORDER BY created_at DESC LIMIT 100";
        }
        $result = $conn->query($sql);
        break;
}
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
        <?php renderAdminSidebar($page, $is_moderator); ?>
        
        <div class="content">
            <div class="content-header">
                <h1><?php echo ucfirst($page); ?> Management</h1>
            </div>

            <div class="data-table">
                <?php if ($page === 'users'): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr class="<?php echo $row['status'] === 'inactive' ? 'inactive-user' : ''; ?>">
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $row['status']; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                                        <?php if($row['email'] !== 'admin@example.com'): ?>
                                            <a href="toggle_user_status.php?id=<?php echo $row['id']; ?>&status=<?php echo $row['status'] === 'active' ? 'inactive' : 'active'; ?>" 
                                               class="action-btn <?php echo $row['status'] === 'active' ? 'deactivate-btn' : 'activate-btn'; ?>"
                                               onclick="return confirm('Are you sure you want to <?php echo $row['status'] === 'active' ? 'deactivate' : 'activate'; ?> this user?')">
                                                <?php echo $row['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                            </a>
                                            <?php if(!$is_moderator): // Only show delete button for admins ?>
                                                <a href="delete_user.php?id=<?php echo $row['id']; ?>" 
                                                   class="action-btn delete-btn"
                                                   onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                    Delete
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php elseif ($page === 'logs'): ?>
                    <div class="data-table logs-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Action</th>
                                    <th>User</th>
                                    <th>Timestamp</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result): while($log = $result->fetch_assoc()): 
                                    $action_class = '';
                                    if ($log['action'] === 'Login Success' || $log['action'] === 'Login') {
                                        $action_class = 'action-login-success';
                                    } elseif ($log['action'] === 'Failed Login') {
                                        $action_class = 'action-failed-login';
                                    } elseif ($log['action'] === 'User Update' || $log['action'] === 'User Status Change') {
                                        $action_class = 'action-user-update';
                                    }
                                ?>
                                    <tr>
                                        <td>#<?php echo str_pad($log['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                        <td class="<?php echo $action_class; ?>"><?php echo htmlspecialchars($log['action']); ?></td>
                                        <td><?php echo htmlspecialchars($log['user']); ?></td>
                                        <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($log['details']); ?></td>
                                    </tr>
                                <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>









