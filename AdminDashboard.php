<?php
include "./includes/admin_sidebar.php";
?>

<div class="container">
    <div class="page-inner">
        <div class="row" id="cardContainer">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body py-5 bg-success">
                        <p class="fs-2 fw-bold text-white">Delivered Orders</p>
                        <p class="fs-3 fw-bold text-white" id="deliveredOrdersCard"></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body py-5 bg-warning">
                        <p class="fs-2 fw-bold text-white">Processing Orders</p>
                        <p class="fs-3 fw-bold text-white" id="processingOrdersCard"></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body py-5 bg-primary">
                        <p class="fs-2 fw-bold text-white">To Ship Orders</p>
                        <p class="fs-3 fw-bold text-white" id="toShipOrdersCard"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6 col-sm-12">
                <div class="card" style="height: 500px;"> <!-- Fixed height -->
                    <div class="card-body">
                        <h1 class="text-center">Latest Orders</h1>
                        <div
                            class="row justify-content-center align-items-start g-2"
                            id="later-orders-container"
                            style="max-height: 400px; overflow-y: auto;"> <!-- Scrollable -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="card">
                    <h1 class="text-center">Top Product Sold</h1>
                    <div class="card-body">
                        <canvas id="topProducts" width="800px" height="400px"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-center fw-bold fs-3">Sales Table</p>
                        <table class="table-responsive" id="sales-table">
                            <thead>
                                <tr>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">Color</th>
                                    <th class="text-center">Size</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Total Unit Sold</th>
                                    <th class="text-center">Total Revenue</th>
                                    <th class="text-center">Total Production Cost</th>
                                    <th class="text-center">Total Profit</th>
                                    <th class="text-center">Sold Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #later-orders-container .card {
        min-height: 120px;
        overflow: hidden;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        const salesChart = document.getElementById('salesChart');
        const topProductsChart = document.getElementById('topProducts');
        getDatas();

        function getDatas() {
            $.ajax({
                method: "get",
                url: "./controllers/AdminDashboardData.php",
                dataType: "json",
                success: function(response) {
                    console.log(response);

                    // === TOP PRODUCTS ===
                    const topProductsChart = document.getElementById('topProducts');
                    if (response.top_products.length > 0) {
                        const topProductsName = response.top_products.map((item) => item.product_name);
                        const totalUnitsSold = response.top_products.map((item) => parseInt(item.total_units_sold));
                        new Chart(topProductsChart, {
                            type: 'bar',
                            data: {
                                labels: topProductsName,
                                datasets: [{
                                    label: 'Units Sold',
                                    data: totalUnitsSold,
                                    backgroundColor: [
                                        'rgba(54, 162, 235, 0.7)',
                                        'rgba(255, 99, 132, 0.7)',
                                        'rgba(255, 206, 86, 0.7)',
                                        'rgba(75, 192, 192, 0.7)'
                                    ]
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                scales: {
                                    x: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    } else {
                        $(topProductsChart).replaceWith(`
          <div class="alert alert-info text-center">
            No top product data available.
          </div>
        `);
                    }

                    // === LATEST ORDERS ===
                    const latestOrderContainer = $('#later-orders-container');
                    const latestOrdersData = response.orders_data.latest_orders.orders;
                    latestOrderContainer.empty();
                    if (latestOrdersData.length > 0) {
                        const latestOrders = latestOrdersData.map((order) => `
                        <div class="card mb-3">
                            <div class="card-body">
                            <div class="row align-items-center g-3">
                                <div class="col-md-2">
                                <div style="width: 100%; height: 100px; overflow: hidden;">
                                    <img src="./public/uploads/user_images/${order.profile_img}"
                                    alt="User Image"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                    class="rounded">
                                </div>
                                </div>
                                <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-2">
                                    <p class="mb-1"><strong>Order #:</strong> ${order.order_number}</p>
                                    </div>
                                    <div class="col-md-2">
                                    <p class="mb-1"><strong>Amount:</strong> ₱${parseFloat(order.total_amount).toFixed(2)}</p>
                                    </div>
                                    <div class="col-md-2">
                                    <p class="mb-1"><strong>Payment:</strong> ${order.payment_method}</p>
                                    </div>
                                    <div class="col-md-2">
                                    <p class="mb-1"><strong>Status:</strong>
                                        <span class="badge ${order.order_status === "Delivered" ? "text-bg-success" :
                                                            order.order_status === "Processing" ? "text-bg-warning" : "text-bg-secondary"}">
                                        ${order.order_status}
                                        </span>
                                    </p>
                                    </div>
                                    <div class="col-md-2">
                                    <p class="mb-1"><strong>Customer:</strong> ${order.firstname} ${order.lastname}</p>
                                    </div>
                                    <div class="col-md-2 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="AdminViewOrderDetails.php?orderID=${order.order_id}"
                                        class="btn btn-outline-primary btn-sm">View</a>
                                    </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        `).join("");
                        latestOrderContainer.append(latestOrders);
                    } else {
                        latestOrderContainer.append(`
                        <div class="alert alert-info text-center">
                            No recent orders found.
                        </div>
                        `);
                    }

                    // === DELIVERED / PROCESSING / TO SHIP ===
                    const ordersData = response.orders_data;
                    const deliveredOrders = ordersData.delivered_orders.length > 0 ? ordersData.delivered_orders[0].delivered_orders : 0;
                    const processingOrders = ordersData.remaining_orders.length > 0 ? ordersData.remaining_orders[0].processing_orders : 0;
                    const toShipOrders = ordersData.to_ship_orders.length > 0 ? ordersData.to_ship_orders[0].to_ship_orders : 0;
                    $("#deliveredOrdersCard").text(deliveredOrders);
                    $("#processingOrdersCard").text(processingOrders);
                    $("#toShipOrdersCard").text(toShipOrders);

                    // === SALES TABLE ===
                    $('#sales-table').DataTable({
                        data: response.sales_data,
                        columns: [{
                                data: "product_name",
                                class: "text-center"
                            },
                            {
                                data: "color",
                                class: "text-center"
                            },
                            {
                                data: "size",
                                class: "text-center"
                            },
                            {
                                data: "variant_price_sold",
                                class: "text-center"
                            },
                            {
                                data: "total_units_sold",
                                class: "text-center"
                            },
                            {
                                data: "total_revenue",
                                class: "text-center"
                            },
                            {
                                data: "total_production_cost",
                                class: "text-center"
                            },
                            {
                                data: "total_profit",
                                class: "text-center"
                            },
                            {
                                data: "sales_date",
                                class: "text-center"
                            }
                        ],
                        destroy: true,
                        responsive: true,
                        lengthMenu: [
                            [5, 10, 25, 50, -1],
                            [5, 10, 25, 50, "All"]
                        ],
                        pageLength: 5,
                        paging: true,
                        dom: "Blfrtip",
                        buttons: [{
                                extend: "print",
                                text: "🖨️ Print",
                                className: "btn btn-warning me-3 mb-3",
                                exportOptions: {
                                    columns: ":visible"
                                }
                            },
                            {
                                extend: "csv",
                                text: "📄 CSV",
                                className: "btn btn-success me-3 mb-3",
                                exportOptions: {
                                    columns: ":visible"
                                }
                            },
                            {
                                extend: "pdf",
                                text: "📑 PDF",
                                className: "btn btn-danger me-3 mb-3",
                                exportOptions: {
                                    columns: ":visible"
                                }
                            }
                        ],
                        language: {
                            emptyTable: "No sales data available."
                        }
                    });

                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
        // new Chart(salesChart, {
        //     type: 'bar',
        //     data: {
        //         labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        //         datasets: [{
        //             label: 'Sales',
        //             data: [100, 200, 150],
        //             borderColor: 'blue',
        //             fill: false
        //         }]
        //     }
        // });


    });
</script>