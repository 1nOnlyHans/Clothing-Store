<?php
    require "../Classes/Dbh.php";
    require "../Classes/Products.php";

    $dbh = new Dbh();
    $conn = $dbh -> Connect();
    $product_name = $_POST["product_name"];
    $category = $_POST["category"];

    $action = new Products($conn);
    echo json_encode($action -> viewProducts($product_name,$category));
?>