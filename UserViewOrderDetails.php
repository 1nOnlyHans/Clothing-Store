<?php
include "./includes/navbar.php";
include "./includes/dashboard_session.php";
require "./Classes/Dbh.php";
require "./Classes/Order.php";
?>

<div class="container mt-5">
    <h1 class="text-center">My Orders</h1>
    <p class="alert d-none" id="alert"></p>
    <p class="alert alert-info bg-info text-white"><span class="text-center">Note!</span> You can't cancel your order if you used online payment</p>
    <div class="card">
        <div class="card-body" id="container">
            <div class="row">
                <?php
                if ($_SERVER["REQUEST_METHOD"] === "GET") {
                    $dbh = new Dbh();
                    $conn = $dbh->Connect();
                    $getOrders = new Order($conn);
                    $orderID = $_GET["order_id"];
                    $userID = $_SESSION["current_user"]->id;

                    $orders = $getOrders->getOrderDetails($orderID, $userID);

                    if (isset($orders["status"]) && $orders["status"] === "error") {
                        echo '<p class="text-secondary">' . $orders["message"] . '</p>';
                    } else {

                        $first = $orders[0];
                        echo '
                            <h5>Order Number: ' . $first->order_number . '</h5>
                            <p>Order Date: ' . date("F j, Y", strtotime($first->order_date)) . '</p>
                            <p>Order Status: ' . $first->order_status . '</p>
                            <p>Shipping Address: ' . $first->shipping_address . '</p>
                            <p>Payment Method: ' . $first->payment_method . '</p>
                            <p>Payment Status: ' . $first->payment_status . '</p>
                            <h5 class="mt-3">Order Items:</h5>
                            <div class="scrollable">
                        ';

                        foreach ($orders as $item) {
                            echo '
                                <div class="card mb-3">
                                    <div class="row g-0">
                                        <div class="col-md-2">
                                            <img src="./public/uploads/variant_images/' . $item->image . '" class="order-item-image" alt="' . $item->product_name . '">
                                        </div>
                                        <div class="col-md-10">
                                            <div class="card-body">
                                                <h5 class="card-title">' . $item->product_name . '</h5>
                                                <p class="card-text">
                                                    Size: ' . $item->variant_size . '<br>
                                                    Color: ' . $item->variant_color . '<br>
                                                    Quantity: ' . $item->item_quantity . '<br>
                                                    Unit Price: ₱' . $item->unit_price . '<br>
                                                    Subtotal: ₱' . $item->total_unit_price . '
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ';
                        }

                        echo '</div>';
                        echo '<h5 class="text-end">Order Total: ₱' . $first->total_amount . '</h5>';
                        if ($first->order_status === "To Ship" || $first->order_status === "Delivered") {
                            echo '
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-success" id="received-btn" data-id="'. $first->order_id .'" data-id="' . $first->order_id . '" '.($first -> order_status === "Delivered" ? "disabled" : "").'>
                                    '.($first -> order_status === "Delivered" ? "Received" : "I received this order").'
                                </button>
                            </div>';
                        }
                        else{
                            echo '
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-danger" ' . (in_array($first->order_status, ["Cancelled", "To Ship", "Delivered"]) ? "disabled" : ($first->payment_status === "Paid" ? "disabled" : "")) . ' id="cancel-btn" data-id="' . $first->order_id . '">
                                    '.($first -> order_status === "Cancelled" ? "Cancelled" : "Cancel Order").'
                                </button>
                            </div>';
                        }
                        
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
    .scrollable {
        max-height: 300px;
        overflow-y: auto;
        padding-right: 10px;
    }

    .order-item-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 5px;
    }
</style>

<script>
    $(document).ready(function() {
        $(document).on('click', '#cancel-btn', function() {
            var orderID = $(this).attr("data-id");
            $.ajax({
                method: "post",
                url: "./controllers/CancelOrder.php",
                data: {
                    order_id: orderID
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (response.status === "success") {
                        $('#alert').addClass("alert-success bg-success text-white");
                        $('#alert').removeClass("d-none");
                        $('#alert').text(response.message);
                    } else {
                        $('#alert').addClass("alert-danger bg-danger text-white");
                        $('#alert').removeClass("d-none");
                        $('#alert').text(response.message);
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });

        $(document).on('click', '#received-btn', function() {
            var orderID = $(this).attr("data-id");
            $.ajax({
                method: "post",
                url: "./controllers/ReceivedOrder.php",
                data: {
                    order_id: orderID
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (response.status === "success") {
                        $('#alert').addClass("alert-success bg-success text-white");
                        $('#alert').removeClass("d-none");
                        $('#alert').text(response.message);
                    } else {
                        $('#alert').addClass("alert-danger bg-danger text-white");
                        $('#alert').removeClass("d-none");
                        $('#alert').text(response.message);
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>