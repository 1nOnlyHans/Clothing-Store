<?php
include "./includes/admin_sidebar.php";
?>

<div class="container">
    <div class="page-inner">
        <h1 class="text-center my-5">Order details</h1>
        <div class="card">
            <div class="card-body" id="container">

            </div>
        </div>
    </div>
</div>
<?php
include "./includes/AddUserModal.php";
?>

<script>
    $(document).ready(function() {
        getOrderDetails();

        function getOrderDetails() {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            const orderID = urlParams.get('orderID');
            $.ajax({
                method: "POST",
                url: "./controllers/getOrderDetails.php",
                data: {
                    orderID: orderID
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    const container = $('#container');
                    if (response.length > 0) {
                        const orderDetails = response.map((item) =>
                            `
                                <div class="row align-items-center g-3 mb-3">
                                    <div class="col-md-2">
                                    <div style="width: 100%; height: 100px; overflow: hidden;">
                                        <img src="./public/uploads/variant_images/${item.image}"
                                        alt="Product Image"
                                        style="width: 100%; height: 100%; object-fit: cover;"
                                        class="rounded">
                                    </div>
                                    </div>
                                    <div class="col-md-10">
                                    <div class="d-flex flex-wrap align-items-center">
                                        <p class="mb-1 me-4"><strong>Product:</strong> ${item.product_name}</p>
                                        <p class="mb-1 me-4"><strong>Size:</strong> ${item.variant_size}</p>
                                        <p class="mb-1 me-4"><strong>Color:</strong> ${item.variant_color}</p>
                                        <p class="mb-1 me-4"><strong>Ordered Qty:</strong> ${item.item_quantity}</p>
                                        <p class="mb-1 me-4"><strong>Unit Price:</strong> ₱${parseFloat(item.unit_price).toFixed(2)}</p>
                                        <p class="mb-1 me-4"><strong>Total:</strong> ₱${parseFloat(item.total_unit_price).toFixed(2)}</p>
                                        <p class="mb-1 me-4"><strong>Current Stocks:</strong> ${item.variant_stock}</p>
                                        <p class="mb-1 me-4">
                                        <strong>Status:</strong>
                                        <span class="badge ${
                                            item.item_quantity > item.variant_stock
                                            ? "text-bg-warning"
                                            : item.product_status === "Available"
                                                ? "text-bg-success"
                                                : item.product_status === "Unavailable"
                                                ? "text-bg-danger"
                                                : "text-bg-secondary"
                                        }">
                                            ${
                                            item.item_quantity > item.variant_stock
                                                ? "Insufficient Stock"
                                                : item.product_status
                                            }
                                        </span>
                                        </p>
                                    </div>
                                    </div>
                                </div>
                                `
                        ).join("");

                        const subTotal = parseFloat(response[0].total_amount).toFixed(2);

                        let isInsufficient;

                        response.forEach((item) => {
                            item.item_quantity > item.variant_stock ? isInsufficient = true : isInsufficient = false;
                        });

                        let isShipped;
                        response.forEach((item) => {
                            item.order_status === "To Ship" ? isShipped = true : isShipped = false;
                        });

                        let isDelivered;
                        response.forEach((item) => {
                            item.order_status === "Delivered" ? isDelivered = true : isDelivered = false;
                        });
                        const subTotalLabel = `
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <h5 class="mb-0"><strong>Subtotal:</strong> ₱${subTotal}</h5>
                                    <button type="button" class="btn btn-primary" id="toShipBtn" 
                                    ${isInsufficient ? "disabled" : isShipped ? "disabled" : isDelivered ? "disabled" : ""}>
                                    ${isShipped ? "Shipped" : isDelivered ? "Delivered" : "To Ship"}
                                    </button>
                                </div>
                                `;

                        const shippingAddress = `
                        <div class="card mb-4">
                            <div class="card-body">
                            <h5 class="mb-3"><strong>Order Information</strong></h5>
                            <p class="mb-1"><strong>Order Number:</strong> ${response[0].order_number}</p>
                            <hr>
                            <h5 class="mb-3"><strong>Purchaser</strong></h5>
                            <p class="mb-1"><strong>Name:</strong> ${response[0].user_firstname} ${response[0].user_lastname}</p>
                            <p class="mb-1"><strong>Email:</strong> ${response[0].user_email}</p>
                            <hr>
                            <h5 class="mb-3"><strong>Shipping Address</strong></h5>
                            <p class="mb-0">${response[0].shipping_address}</p>
                            <hr>
                            <h5 class="mb-3"><strong>Order Status</strong></h5>
                            <span class="badge ${
                                response[0].order_status === "Delivered" ? "bg-success" :
                                response[0].order_status === "Processing" ? "bg-warning text-dark" :
                                response[0].order_status === "To Ship" ? "bg-primary" :
                                response[0].order_status === "Cancelled" ? "bg-secondary" :
                                "bg-secondary"
                            }">
                                ${response[0].order_status}
                            </span>
                            </div>
                        </div>
                        `;

                        const orderBlock = `
                                ${shippingAddress}
                                ${orderDetails}
                                ${subTotalLabel}
                                `;

                        container.empty();
                        container.append(orderBlock);
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            })
        }

        $(document).on('click', '#toShipBtn', function() {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            const orderID = urlParams.get('orderID');

            $.ajax({
                method: "POST",
                url: "./controllers/ProcessOrder.php",
                data: {
                    orderID: orderID
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    getOrderDetails();
                    if (response.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: response.message
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: response.message
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>