<?php
require_once "../Classes/Admin.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $id = $_POST["productID"] ?? "";
    $category = $_POST["category"] ?? "";
    $name = $_POST["name"] ?? "";
    $description = $_POST["description"] ?? "";
    $image = $_FILES["image"] ?? "";
    $action = new Admin();

    echo json_encode($action -> updateProduct($id,$name,$description,$category,$image));
}