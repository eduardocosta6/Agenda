<?php
function renderAdminSidebar($activePage = '') {
    ?>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Admin Panel</h2>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="?page=users" class="<?php echo $activePage === 'users' ? 'active' : ''; ?>">
                    Users Management
                </a>
            </li>
            <li>
                <a href="?page=logs" class="<?php echo $activePage === 'logs' ? 'active' : ''; ?>">
                    System Logs
                </a>
            </li>
        </ul>
    </div>
    <?php
}
?>
