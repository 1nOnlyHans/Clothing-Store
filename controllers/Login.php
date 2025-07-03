<?php
session_start();
require "../Classes/AuthUser.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["log_email"] ?? "";
    $password = $_POST["log_password"] ?? "";

    $login = new AuthUser();

    $login->Login($email, $password);
}
