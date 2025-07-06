<?php
require "../Classes/Admin.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $action = new Admin();
    $orderID = $_POST["orderID"];
    echo json_encode($action -> getOrderDetails($orderID));
}