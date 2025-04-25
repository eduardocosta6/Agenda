<?php
    require_once 'config/database.php';
    require_once 'includes/session.php';
    require_once 'includes/logger.php';
    require_once 'components/admin_sidebar.php';

    // Verify admin/moderator access
    if (! isset($_SESSION['user_role']) || ! in_array($_SESSION['user_role'], ['admin', 'moderator'])) {
        header("Location: index.php");
        exit();
    }

    $is_moderator = $_SESSION['user_role'] === 'moderator';
    $error        = '';
    $success      = '';

    // Check if ID is provided
    if (! isset($_GET['id'])) {
        header("Location: admin_panel.php?page=users");
        exit();
    }

    $id = $_GET['id'];

    // Get user data
    $sql  = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: admin_panel.php?page=users");
        exit();
    }

    $user = $result->fetch_assoc();

    // Check if moderator is trying to edit admin
    if ($is_moderator && $user['role'] === 'admin') {
        header("Location: admin_panel.php?page=users");
        exit();
    }

    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name   = $_POST['name'];
        $email  = $_POST['email'];
        $role   = $_POST['role'];
        $status = $_POST['status'];

        // Validate email format
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format";
        } else {
            // Check if email already exists for another user
            $check_sql  = "SELECT id FROM users WHERE email = ? AND id != ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("si", $email, $id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $error = "Email already exists for another user";
            } else {
                // Update user
                $update_sql  = "UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ssssi", $name, $email, $role, $status, $id);

                if ($update_stmt->execute()) {
                    $success = "User updated successfully";

                    // Log the action
                    $details = "Updated user: $name (ID: $id) - Role: $role, Status: $status";
                    log_action('User Update', $details);

                    // Redirect after short delay
                    header("refresh:2;url=admin_panel.php?page=users");
                } else {
                    $error = "Error updating user: " . $conn->error;
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - My Agenda</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-layout">
        <?php renderAdminSidebar('users', $is_moderator); ?>

        <div class="content">
            <div class="content-header">
                <h1>Edit User</h1>
            </div>

            <div class="form-container">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>                                                                  <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>                                                            <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="admin-form">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="role"><i class="fas fa-user-tag"></i> Role:</label>
                        <select id="role" name="role"                                                      <?php echo($user['email'] === 'admin@example.com' || ($is_moderator && $user['role'] !== 'user')) ? 'disabled' : ''; ?>>
                            <option value="user"                                                 <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                            <?php if (! $is_moderator): ?>
                                <option value="moderator"<?php echo $user['role'] === 'moderator' ? 'selected' : ''; ?>>Moderator</option>
                                <option value="admin"                                                      <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <?php endif; ?>
                        </select>
                        <?php if ($user['email'] === 'admin@example.com' || ($is_moderator && $user['role'] !== 'user')): ?>
                            <input type="hidden" name="role" value="<?php echo htmlspecialchars($user['role']); ?>">
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="status"><i class="fas fa-toggle-on"></i> Status:</label>
                        <select id="status" name="status"                                                          <?php echo $user['email'] === 'admin@example.com' ? 'disabled' : ''; ?>>
                            <option value="active"                                                   <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive"                                                     <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                        <?php if ($user['email'] === 'admin@example.com'): ?>
                            <input type="hidden" name="status" value="active">
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <a href="admin_panel.php?page=users" class="btn light" title="Cancel">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <button type="submit" class="btn primary" title="Update User">
                            <i class="fas fa-save"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .form-container {
            background-color: var(--white-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            padding: 2rem;
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid var(--border-color);
        }

        .admin-form .form-group {
            margin-bottom: 1.5rem;
        }

        .admin-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-color);
        }

        .admin-form input,
        .admin-form select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-size: 1rem;
        }

        .admin-form input:focus,
        .admin-form select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</body>
</html>
