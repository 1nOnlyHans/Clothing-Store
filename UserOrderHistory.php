<?php
include "./includes/navbar.php";
include "./includes/dashboard_session.php";
require "./Classes/Dbh.php";
require "./Classes/Order.php";
?>

<div class="container mt-5">
    <h1 class="text-center">My Orders</h1>
    <div class="card">
        <div class="card-body" id="container">
            <div class="row">
                <?php
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $dbh = new Dbh();
                    $conn = $dbh->Connect();
                    $getOrders = new Order($conn);
                    $user_id = $_SESSION["current_user"]->id;
                    $orders = $getOrders->getOrders($user_id);

                    if ($orders["status"] === "empty") {
                        echo '<p class="text-secondary">No orders</p>';
                    } else {
                        if (count($orders["orders"]) <= 0) {
                            echo '<p class="text-secondary">No orders</p>';
                        } else {
                            foreach ($orders["orders"] as $order) {
                                echo '
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center g-3">
                                        <div class="col-3 py-3">
                                            <p>Order Number: ' . $order->order_number . '</p>
                                        </div>
                                        <div class="col-3 py-3">
                                            <p>Total Amount: ' . $order->total_amount . '</p>
                                        </div>
                                        <div class="col-3 py-3">
                                            <p>Payment Method: ' . $order->payment_method . '</p>
                                        </div>
                                        <div class="col-2 py-3">
                                            <p>Order Status: ' . $order->order_status . '</p>
                                        </div>
                                        <div class="col-1 py-3 text-end">
                                            <a href="UserViewOrderDetails.php?order_id=' . $order->id . '" class="btn btn-primary btn-sm">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ';
                            }
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

    });
</script>