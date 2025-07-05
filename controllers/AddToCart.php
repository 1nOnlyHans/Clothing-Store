<?php
    session_start();
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if(isset($_SESSION["current_user"])){
            require "../Classes/Dbh.php";
            require "../Classes/Cart.php";
            $dbh = new Dbh();
            $conn = $dbh -> Connect();
            $userID = $_SESSION["current_user"] -> id;
            $productID = $_POST["productID"];
            $variantID = $_POST["variantID"];
            $quantity = $_POST["quantity"];
            $action = new Cart($conn,$userID,$productID,$variantID,$quantity);
            echo json_encode($action -> addToCart($userID));
        }
        else{
            echo json_encode([
                "status" => "error",
                "message" => "You need to login first!"
            ]);
        }
    }
?>