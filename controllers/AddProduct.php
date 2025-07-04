<?php
require_once "../Classes/Admin.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $category = $_POST["category"] ?? "";
    $name = $_POST["name"] ?? "";
    $description = $_POST["description"] ?? "";
    $image = $_FILES["image"] ?? "";
    $action = new Admin();
    echo json_encode($action -> addProduct($name,$description,$category,$image));
}