<?php
    require_once 'config/database.php';
    function renderAdminSidebar($activePage = '', $is_moderator = false)
    {
        // Get user info from session
        global $conn;
        $user_id = $_SESSION['user_id'];
        $sql     = "SELECT name, email FROM users WHERE id = ?";
        $stmt    = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
    ?>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><?php echo $is_moderator ? 'Moderator Panel' : 'Admin Panel'; ?></h2>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($user['name']); ?></span>
                <span class="user-email"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php" class="<?php echo $activePage === '' || $activePage === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="admin_panel.php?page=users" class="<?php echo $activePage === 'users' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Users Management
                </a>
            </li>
            <li>
                <a href="admin_panel.php?page=logs" class="<?php echo $activePage === 'logs' ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i> System Logs
                </a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    <?php
        }
    ?>
