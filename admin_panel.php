<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'components/admin_sidebar.php';

// Verify admin access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'users';

// Fetch data based on page
switch($page) {
    case 'users':
        $sql = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
        $result = $conn->query($sql);
        break;
    case 'logs':
        $sql = "SELECT * FROM logs ORDER BY created_at DESC LIMIT 100";
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
        <?php renderAdminSidebar($page); ?>
        
        <div class="content">
            <div class="content-header">
                <h1><?php echo ucfirst($page); ?> Management</h1>
                <div class="header-actions">
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
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
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                                        <?php if($row['email'] !== 'admin@example.com'): ?>
                                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" 
                                               class="action-btn delete-btn"
                                               onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php elseif ($page === 'logs'): ?>
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
                            <?php if ($result): while($log = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($log['id']); ?></td>
                                    <td><?php echo htmlspecialchars($log['action']); ?></td>
                                    <td><?php echo htmlspecialchars($log['user']); ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($log['details']); ?></td>
                                </tr>
                            <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>



