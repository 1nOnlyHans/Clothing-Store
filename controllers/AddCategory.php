<?php
    require "../Classes/Admin.php";

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $category_name = $_POST["category_name"];
        $category_description = $_POST["category_description"];

        $action = new Admin();

        echo json_encode($action -> addCategory($category_name,$category_description));
    }
?>