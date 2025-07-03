<?php
include "./includes/admin_sidebar.php";
?>

<div class="container">
    <div class="page-inner">
        <h1 class="text-center my-5">Products Categories</h1>
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#AddProductModal">
            Add Category
        </button>
        <div class="card">
            <div class="card-body">
                <div style="overflow-x: auto;">
                    <table class="table table-bordered" id="category-tbl">
                        <thead>
                            <tr>
                                <th class="text-center">Category Name</th>
                                <th class="text-center">Category Description</th>
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
include "./includes/AddCategoryModal.php";
?>

<script>
    $(document).ready(function() {
        let categoryTable = $("#category-tbl").DataTable({
            ajax: "./controllers/GetAllCategories.php",
            columns: [{
                    data: "category_name",
                    class: "text-center"
                },
                {
                    data: "category_description",
                    class: "text-center"
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
          <div class="d-flex justify-content-center align-content-center gap-3">
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
                        columns: [0,1]
                    }
                },
                {
                    extend: "csv",
                    text: "📄 CSV",
                    className: "btn btn-success me-3 mb-3",
                    exportOptions: {
                        columns: [0,1]
                    }
                },
                {
                    extend: "pdf",
                    text: "📑 PDF",
                    className: "btn btn-danger me-3 mb-3",
                    exportOptions: {
                        columns: [0,1]
                    }
                }
            ]
        });

        $('#add-category-form').on('submit', function(event) {
            event.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                method: "POST",
                url: "./controllers/AddCategory.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        categoryTable.ajax.reload();
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