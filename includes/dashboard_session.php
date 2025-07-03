<?php
if (!isset($_SESSION["current_user"])) {
    header("Location: index.php");
    exit;
}