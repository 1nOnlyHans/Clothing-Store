<?php
session_start();
require "../Classes/Dbh.php";
require '../Classes/Order.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dbh = new Dbh();
    $conn = $dbh->Connect();
    $getOrders = new Order($conn);
    $orderID = $_POST["order_id"];
    $userID = $_SESSION["current_user"]->id;
    $action = new Order($conn);
    echo json_encode($action -> cancelOrder($orderID,$userID));
}
