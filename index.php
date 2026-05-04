<?php
session_start();

// If logged in → go to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: user_dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>