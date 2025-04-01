<?php
function renderAdminSidebar($activePage = '', $is_moderator = false) {
    ?>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><?php echo $is_moderator ? 'Moderator Panel' : 'Moderator Panel'; ?></h2>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php" class="<?php echo $activePage === 'dashboard' ? 'active' : ''; ?>">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="admin_panel.php?page=users" class="<?php echo $activePage === 'users' ? 'active' : ''; ?>">
                    Users Management
                </a>
            </li>
            <li>
                <a href="admin_panel.php?page=logs" class="<?php echo $activePage === 'logs' ? 'active' : ''; ?>">
                    System Logs
                </a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    <?php
}
?>






