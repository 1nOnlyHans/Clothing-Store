<?php
    session_start();
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        require "../Classes/Dbh.php";
        require "../Classes/Cart.php";
        $dbh = new Dbh();
        $conn = $dbh -> Connect();
        $cartID = $_POST["cartID"];
        $variantID = $_POST["variantID"];
        $quantity = $_POST["quantity"];
        $action = new Cart($conn,null,null,$variantID,$quantity);
        echo json_encode($action -> editQuantity($cartID));
    }
?>