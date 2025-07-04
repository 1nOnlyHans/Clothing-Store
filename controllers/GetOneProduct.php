<?php
require "../Classes/Admin.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $productID = $_POST["productID"];
    $action = new Admin();
    echo json_encode($action -> getOneProduct($productID));
}
