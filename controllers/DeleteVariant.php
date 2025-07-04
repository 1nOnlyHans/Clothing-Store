<?php
    require "../Classes/Admin.php";
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $variantID = $_POST["variantID"];
        $action = new Admin();
        echo json_encode($action -> removeVariant($variantID));
    }
?>