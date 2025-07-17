<?php
session_start();
session_destroy();

// Clear the remember me cookie if it exists
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

header('Location: index.php');
exit();