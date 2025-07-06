<?php
include "./includes/admin_sidebar.php";
?>

<div class="container">
    <div class="page-inner">
        <h1 class="text-center my-5">Products Inventory</h1>
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#AddProductModal">
            Add Product
        </button>
        <div class="card">
            <div class="card-body">
                <div style="overflow-x: auto;">
                    <table class="table table-bordered" id="product-tbl">
                        <thead>
                            <tr>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Image</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Total Variants</th>
                                <th class="text-center">Total Stocks</th>
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
                url: "./controllers/GetAllProducts.php",
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    $("#product-tbl").DataTable({
                        data: response,
                        columns: [{
                                data: "name",
                                class: "text-center"
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function(data, type, row) {
                                    return `<img src="./public/uploads/product_images/${data.image}" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;">`;
                                }
                            },
                            {
                                data: "category_name",
                                class: "text-center"
                            },
                            {
                                data: "total_variants",
                                class: "text-center"
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function(data, type, row) {
                                    return `${data.total_stock === null ? '<span class="badge text-bg-danger">Out of Stock</span>' : data.total_stock}`;
                                }
                            },
                            {
                                data: null,
                                render: function(data, type, row) {
                                    return `
                                        <div class="d-flex justify-content-center align-content-center gap-3">
                                            <a href="AdminProductDetails.php?productID=${data.id}" class="btn btn-outline-primary">
                                                view
                                            </a>
                                            <button type="button" class="btn btn-outline-danger delete" data-id="${data.id}">Delete</button>
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
                        dom: "Blfrtip", // ✅ makes sure length, filter, info, pagination show
                        buttons: [{
                                extend: "print",
                                text: "🖨️ Print",
                                className: "btn btn-warning me-3 mb-3",
                                exportOptions: {
                                    columns: [0, 2, 3, 4]
                                }
                            },
                            {
                                extend: "csv",
                                text: "📄 CSV",
                                className: "btn btn-success me-3 mb-3",
                                exportOptions: {
                                    columns: [0, 2, 3, 4]
                                }
                            },
                            {
                                extend: "pdf",
                                text: "📑 PDF",
                                className: "btn btn-danger me-3 mb-3",
                                exportOptions: {
                                    columns: [0, 2, 3, 4]
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
                        $('#add-product-form')[0].reset();
                        fetchProducts();
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