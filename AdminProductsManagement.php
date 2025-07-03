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
                                <th class="text-center">Base Price</th>
                                <th class="text-center">Discount</th>
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
        let productsTable = $("#product-tbl").DataTable({
            ajax: "./controllers/GetAllProducts.php",
            columns: [{
                    data: "name",
                    class: "text-center"
                },
                {
                    data: null,
                    class: "text-center",
                    render: function(data, type, row) {
                        return `<img src="./public/uploads/product_images/${data.image}" alt="Product Image" width="150" height="auto">`;
                    }
                },
                {
                    data: "category_name",
                    class: "text-center"
                },
                {
                    data: "price",
                    class: "text-center"
                },
                {
                    data: "discount",
                    class: "text-center"
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
          <div class="d-flex justify-content-center align-content-center gap-3">
            <button class="btn btn-outline-primary view-btn" data-id=${data.name}>View</button>
            <button class="btn btn-outline-info">Edit</button>
            <button class="btn btn-outline-danger">Delete</button>
          </div>`;
                    },
                },
            ],
            responsive: true,
            dom: "Bfrtip",
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
                        productsTable.ajax.reload();
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
                error: function(error) {
                    console.log(error);
                }
            });
        });
    });
</script>