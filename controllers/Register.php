<?php
require "../Classes/AuthUser.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = [
        "firstname" => $_POST["reg_firstname"],
        "lastname" => $_POST["reg_lastname"],
        "email" => $_POST["reg_email"],
        "password" => $_POST["reg_password"],
        "role" => $_POST["role"] ?? "User"
    ];

    $action = new AuthUser();

    $action->Setter($data);

    $action->Register();
}
