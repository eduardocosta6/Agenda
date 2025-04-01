<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/logger.php';

// Verify admin/moderator access
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'moderator'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: admin_panel.php");
    exit();
}

$user_id = $_GET['id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    // Check if password should be updated
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name = ?, email = ?, role = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $role, $password, $user_id);
    } else {
        $sql = "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $role, $user_id);
    }
    
    if ($stmt->execute()) {
        // Update session if the edited user is currently logged in
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
            $_SESSION['user_role'] = $role;
            
            // If user was promoted to admin, redirect them to admin panel
            if ($role === 'admin') {
                $success = "User updated successfully. You now have admin privileges.";
                header("Refresh: 2; URL=admin_panel.php");
            }
        }
        
        $success = "User updated successfully";
        log_action('User Update', "Updated user ID: $user_id - New role: $role");
    } else {
        $error = "Error updating user";
    }
}

// Fetch user data
$sql = "SELECT id, name, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: admin_panel.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php require_once 'components/admin_sidebar.php'; renderAdminSidebar('users'); ?>
        
        <div class="content">
            <div class="content-header">
                <h1>Edit User</h1>
                <div class="header-actions">
                    <a href="admin_panel.php?page=users" class="btn back">Back to Users</a>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <div class="edit-form-container">
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST" class="edit-form">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="role">Role:</label>
                        <select id="role" name="role" <?php echo $user['email'] === 'admin@example.com' ? 'disabled' : ''; ?>>
                            <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <option value="moderator" <?php echo $user['role'] === 'moderator' ? 'selected' : ''; ?>>Moderator</option>
                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <?php elseif ($_SESSION['user_role'] === 'moderator' && $user['role'] === 'moderator'): ?>
                                <option value="moderator" selected>Moderator</option>
                            <?php endif; ?>
                        </select>
                        <?php if ($user['email'] === 'admin@example.com'): ?>
                            <small class="form-text text-muted">Main admin role cannot be changed</small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="password">New Password: (leave empty to keep current)</label>
                        <input type="password" id="password" name="password">
                    </div>

                    <button type="submit" class="btn submit">Update User</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>


