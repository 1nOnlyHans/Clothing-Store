<?php
require_once "../Classes/Admin.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $category = $_POST["category"] ?? "";
    $name = $_POST["name"] ?? "";
    $description = $_POST["description"] ?? "";
    $base_price = $_POST["base_price"] ?? 0;
    $image = $_FILES["image"] ?? "";
    $action = new Admin();
    echo json_encode($action -> addProduct($name,$description,$base_price,$category,$image));
}