<?php
if (isset($_SESSION["current_user"])) {
    if ($_SESSION["current_user"]->role === "Admin") {
        header("Location: AdminDashboard.php");
        exit;
    } else if ($_SESSION["current_user"]->role === "User") {
        header("Location: User_dashboard.php");
        exit;
    }
}
