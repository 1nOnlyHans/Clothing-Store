<?php
require_once "../Classes/Admin.php";

if($_SERVER["REQUEST_METHOD"] === "GET"){
    $action = new Admin();
    echo json_encode(["data" => $action -> getAllProducts()]);
}