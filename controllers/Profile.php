<?php
    session_start();
    require "../Classes/Admin.php";
    if($_SERVER["REQUEST_METHOD"] === "GET"){
        $action = new Admin();
        $userID = $_SESSION["current_user"]->id;
        echo json_encode($action->getUserById($userID));
    }
?>