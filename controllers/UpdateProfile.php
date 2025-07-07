<?php
require "../Classes/AuthUser.php";
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userID = $_SESSION["current_user"] -> id;
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $role = $_POST["role"];
    $status = $_POST["status"];
    $action = new AuthUser();

    echo json_encode($action->updateUser($userID,$firstname,$lastname,$email,$role,$status));
}