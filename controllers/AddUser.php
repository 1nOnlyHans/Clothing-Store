<?php
require "../Classes/Admin.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $role = $_POST["role"];

    $action = new Admin();

    echo json_encode($action->addUser($firstname,$lastname,$email,$role));
}