<?php
include "./includes/admin_sidebar.php";
?>

<div class="container">
    <div class="page-inner">
        <h1 class="text-center my-5">Orders</h1>
        <div class="card">
            <div class="card-body">
                <div style="overflow-x: auto;">
                    <table class="table table-bordered" id="order-tbl">
                        <thead>
                            <tr>
                                <th class="text-center">ORD</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Image</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Payment Method</th>
                                <th class="text-center">Payment Status</th>
                                <th class="text-center">Shipping Address</th>
                                <th class="text-center">Total Amount</th>
                                <th class="text-center">Order Status</th>
                                <th class="text-center">Order Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "./includes/AddProductModal.php";
?>
<script>
    $(document).ready(function() {
        fetchProducts();

        function fetchProducts() {
            $.ajax({
                method: "GET",
                url: "./controllers/adminGetAllOrders.php",
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    $("#order-tbl").DataTable({
                        data: response.orders,
                        columns: [{
                                data: "order_number",
                                class: "text-center"
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function(data, type, row) {
                                    return data.firstname + " " + data.lastname;
                                }
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function(data, type, row) {
                                    return `<img src="./public/uploads/user_images/${data.profile_img}" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;">`;
                                }
                            },
                            {
                                data: "email",
                                class: "text-center"
                            },
                            {
                                data: "payment_method",
                                class: "text-center"
                            },
                            {
                                data: "payment_status",
                                class: "text-center"
                            },
                            {
                                data: "shipping_address",
                                class: "text-center"
                            },
                            {
                                data: "total_amount",
                                class: "text-center"
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function(data,type,row){
                                    return `<p class="mb-1"><span class="badge ${data.order_status === "Processing" ? "text-bg-warning" : data.order_status === "To Ship" ? "text-bg-primary" : "text-bg-success"}">${data.order_status}</span></p>`;
                                }
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function(data, type, row) {
                                    const formatDate = new Date(data.order_date).toLocaleDateString(
                                        "en-US", {
                                            year: "numeric",
                                            month: "long",
                                            day: "numeric",
                                            hour: "numeric",
                                            minute: "2-digit",
                                            hour12: true
                                        }
                                    )
                                    return formatDate;
                                }
                            },
                            {
                                data: null,
                                render: function(data, type, row) {
                                    return `
                                        <div class="d-flex justify-content-center align-content-center gap-3">
                                            <a href="AdminViewOrderDetails.php?orderID=${data.order_id}" class="btn btn-outline-primary">
                                                View
                                            </a>
                                        </div>`;
                                },
                            },
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
                                    columns: [0, 1, 3, 4, 5, 6, 7, 8, 9]
                                }
                            },
                            {
                                extend: "csv",
                                text: "📄 CSV",
                                className: "btn btn-success me-3 mb-3",
                                exportOptions: {
                                    columns: [0, 1, 3, 4, 5, 6, 7, 8, 9]
                                }
                            },
                            {
                                extend: "pdf",
                                text: "📑 PDF",
                                className: "btn btn-danger me-3 mb-3",
                                exportOptions: {
                                    columns: [0, 1, 3, 4, 5, 6, 7, 8, 9]
                                }
                            }
                        ]
                    });
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            })
        }

        $('#add-product-form').on('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                method: "POST",
                url: "./controllers/AddProduct.php",
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    if (response.status === "success") {
                        fetchProducts();
                        $('#add-product-form')[0].reset();
                        let Modal = bootstrap.Modal.getInstance(document.getElementById('AddProductModal'));
                        Modal.hide();
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

        $(document).on('click', '.delete', function() {
            const id = $(this).attr('data-id');
            Swal.fire({
                title: "Are you sure?",
                text: "All the item variants will also be removed!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "POST",
                        url: "./controllers/DeleteProduct.php",
                        data: {
                            productID: id
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.status === "success") {
                                fetchProducts();
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
                }
            });
        });
    });
</script>