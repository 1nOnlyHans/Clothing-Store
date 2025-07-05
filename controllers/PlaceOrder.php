<?php
    session_start();
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        require "../Classes/Dbh.php";
        require "../Classes/Order.php";
        $dbh = new Dbh();
        $conn = $dbh -> Connect();
        $userID = $_SESSION["current_user"] -> id;
        $totalAmount = $_POST["total_amount"];
        $shipping_address = $_POST["shipping_address"];
        $payment_method = $_POST["payment_method"];
        $action = new Order($conn);
        echo json_encode($action -> placeOrder($userID,$totalAmount,$payment_method,$shipping_address));
    }
?>