<?php
    require "../Classes/Admin.php";

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $categoryID = $_POST["categoryID"];
        $action = new Admin();
        echo json_encode($action -> removeCategory($categoryID));
    }
?>