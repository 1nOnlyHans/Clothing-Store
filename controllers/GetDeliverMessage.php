<?php
session_start();
require "../Classes/Dbh.php";
require '../Classes/Order.php';
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $dbh = new Dbh();
    $conn = $dbh->Connect();
    $getOrders = new Order($conn);
    $userID = $_SESSION["current_user"]->id;
    $action = new Order($conn);
    echo json_encode($action -> getDeliverMessages($userID));
}
