<?php
    require "../Classes/Admin.php";
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $userID = $_POST["userID"];
        $action = new Admin();
        echo json_encode($action -> deleteUser($userID));
    }
?>