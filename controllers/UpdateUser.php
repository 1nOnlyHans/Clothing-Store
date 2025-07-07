<?php
require "../Classes/Admin.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userID = $_POST["userID"];
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $role = $_POST["role"];
    $status = $_POST["status"];
    $action = new Admin();

    echo json_encode($action->updateUser($userID,$firstname,$lastname,$email,$role,$status));
}