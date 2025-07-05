<?php
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        require "../Classes/Dbh.php";
        require "../Classes/Cart.php";
        $dbh = new Dbh();
        $conn = $dbh -> Connect();
        $cartID = $_POST["cartID"];
        $action = new Cart($conn,null,null,null,null);
        echo json_encode($action -> removeCartItem($cartID));
    }
?>