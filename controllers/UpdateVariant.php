<?php
require_once "../Classes/Admin.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $variantID = $_POST["variantID"];
    $size = $_POST["size"] ?? "";
    $color = $_POST["color"] ?? "";
    $price = $_POST["price"] ?? "";
    $stock = $_POST["stock"] ?? "";
    $image = $_FILES["image"] ?? "";
    $status = $_POST["status"] ?? "";
    $action = new Admin();

    echo json_encode($action -> updateVariant($variantID,$size,$color,$price,$stock,$status,$image));
}