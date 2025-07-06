<?php
if (!isset($_SESSION["current_user"])) {
    header("Location: ViewProducts.php");
    exit;
}