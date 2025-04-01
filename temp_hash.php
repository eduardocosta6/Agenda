<?php
require_once 'config/database.php';

$password = 'admin';
$hashed = password_hash($password, PASSWORD_DEFAULT);
echo "UPDATE users SET password = '$hashed' WHERE email = 'admin@example.com';";