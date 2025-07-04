<?php
require_once "../Classes/Admin.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $category_id = $_POST["categoryID"] ?? "";
    $category_name = $_POST["category_name"] ?? "";
    $category_description = $_POST["category_description"] ?? "";

    $action = new Admin();

    echo json_encode($action -> editCategory($category_id,$category_name,$category_description));
}