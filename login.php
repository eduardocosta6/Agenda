<?php
    require_once 'config/database.php';
    require_once 'includes/session.php';
    require_once 'includes/logger.php';

    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email    = $_POST['email'];
        $password = $_POST['password'];

        $sql  = "SELECT id, password, role, name, status FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if ($user['status'] === 'inactive') {
                $error = "This account has been deactivated";
                log_action('Failed Login', "Failed login attempt for email: $email (Account inactive)");
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_role'] = $user['role'];

                // Log the successful login
                $details = "User: " . $user['name'] . " (ID: " . $user['id'] . ") - Role: " . $user['role'];
                log_action('Login Success', $details);

                // Redirect based on role
                if (in_array($user['role'], ['admin', 'moderator'])) {
                    header("Location: dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "Invalid password";
                log_action('Failed Login', "Failed login attempt for email: $email (Invalid password)");
            }
        } else {
            $error = "User not found";
            log_action('Failed Login', "Failed login attempt for non-existent email: $email");
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - My Agenda</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
</head>
<body>
    <?php require_once 'components/navbar.php';
    renderNavbar('login'); ?>

    <div class="container">
        <main>
            <form method="POST" class="event-form">
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn submit">Login</button>
            </form>
        </main>
    </div>
</body>
</html>






