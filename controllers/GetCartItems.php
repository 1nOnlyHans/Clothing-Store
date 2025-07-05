<?php
session_start();
    if($_SERVER["REQUEST_METHOD"] === "GET"){
        require "../Classes/Dbh.php";
        require "../Classes/Cart.php";
        $dbh = new Dbh();
        $conn = $dbh -> Connect();
        $userID = $_SESSION["current_user"] -> id;
        $action = new Cart($conn,$userID,null,null,null);
        echo json_encode($action -> getAllCartItems($userID));
    }
?>