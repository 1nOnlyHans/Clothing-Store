<?php
require_once "../Classes/Admin.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $product_id = $_POST["productID"];
    $size = $_POST["size"] ?? "";
    $color = $_POST["color"] ?? "";
    $price = $_POST["price"] ?? "";
    $production_cost = $_POST["production_cost"] ?? "";
    $stock = $_POST["stock"] ?? "";
    $image = $_FILES["image"] ?? "";
    $status = $_POST["status"]??"";
    $action = new Admin();

    echo json_encode($action -> AddProductVariant($product_id,$size,$color,$price,$stock,$status,$image,$production_cost));
}