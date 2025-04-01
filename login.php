<?php
require_once 'config/database.php';
require_once 'includes/session.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Add debugging
    $sql = "SELECT id, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        // Debug password verification
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role']; // Store the role
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password";
            // Debug info - remove in production
            error_log("Password verification failed for email: " . $email);
            error_log("Provided password: " . $password);
            error_log("Stored hash: " . $user['password']);
        }
    } else {
        $error = "Email not found";
        // Debug info - remove in production
        error_log("No user found with email: " . $email);
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
    <?php require_once 'components/navbar.php'; renderNavbar('login'); ?>
    
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


