<?php
require '../Classes/Admin.php';
if($_SERVER["REQUEST_METHOD"] === "GET"){
    $action = new Admin();
    echo json_encode($action -> getDashboardDatas());
}