<?php
require_once 'includes/session.php';

function renderNavbar($activePage = '') {
    ?>
    <header>
        <div class="navbar-content">
            <div class="logo">
                <h1>My Agenda</h1>
            </div>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php" class="<?php echo ($activePage == 'home') ? 'active' : ''; ?>">Home</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="add_event.php" class="<?php echo ($activePage == 'add') ? 'active' : ''; ?>">Add Event</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="<?php echo ($activePage == 'login') ? 'active' : ''; ?>">Login</a></li>
                        <li><a href="register.php" class="<?php echo ($activePage == 'register') ? 'active' : ''; ?>">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <?php
}
?>





