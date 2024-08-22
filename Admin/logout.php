<?php
include('session.php');

if (isset($_GET['logout'])) {
    if (isset($_SESSION['admin_logged_in'])) {
        // Unset all session variables
        session_unset();
        // Destroy the session completely
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
?>
