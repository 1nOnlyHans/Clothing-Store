<?php
include "./includes/admin_sidebar.php";
?>

<div class="container">
    <div class="page-inner" id="#container">
        <h1 class="text-center my-5">Product Categories</h1>
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#AddCategoryModal">
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
include "./includes/EditCategoryModal.php";
?>

<script>
    $(document).ready(function() {

        fetchCategories();

        function fetchCategories() {
            $.ajax({
                method: "GET",
                url: "./controllers/GetAllCategories.php",
                dataType: "json",
                success: function(response) {
                    $("#category-tbl").DataTable({
                        data: response,
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
                                    <button class="btn btn-outline-info edit" data-id="${data.category_id}"
                                        data-category_name="${data.category_name}"
                                        data-category_description="${data.category_description}">Edit</button>
                                    <button class="btn btn-outline-danger delete" data-id="${data.category_id}">Delete</button>
                                </div>
                            `;
                                },
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
                                    columns: [0, 1]
                                }
                            },
                            {
                                extend: "csv",
                                text: "📄 CSV",
                                className: "btn btn-success me-3 mb-3",
                                exportOptions: {
                                    columns: [0, 1]
                                }
                            },
                            {
                                extend: "pdf",
                                text: "📑 PDF",
                                className: "btn btn-danger me-3 mb-3",
                                exportOptions: {
                                    columns: [0, 1]
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
                        $('#add-category-form')[0].reset();
                        fetchCategories();
                        // Close the modal properly
                        let modal = bootstrap.Modal.getInstance(document.getElementById('AddCategoryModal'));
                        modal.hide();
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
                    console.log(error.responseText);
                }
            });
        });

        $(document).on('click', '.edit', function() {
            const id = $(this).attr('data-id');
            const name = $(this).attr('data-category_name');
            const description = $(this).attr('data-category_description');
            $('#EditCategoryModal input[name="categoryID"]').val(id);
            $('#EditCategoryModal input[name="category_name"]').val(name);
            $('#EditCategoryModal textarea[name="category_description"]').val(description);
            $('#EditCategoryModal').modal('show');
        });

        $('#update-category-form').on('submit', function(event) {
            event.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                method: "POST",
                url: "./controllers/UpdateCategory.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (response.status === "success") {
                        $('#update-category-form')[0].reset();
                        fetchCategories();
                        let modal = bootstrap.Modal.getInstance(document.getElementById('EditCategoryModal'));
                        modal.hide();
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
                    console.log(error.responseText);
                }
            });
        });

        $(document).on('click', '.delete', function() {
            const id = $(this).attr('data-id');
            Swal.fire({
                title: "Are you sure?",
                text: "All the items will also be removed!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "POST",
                        url: "./controllers/DeleteCategory.php",
                        data: {
                            categoryID: id
                        },
                        dataType: "json",
                        success: function(response) {
                            console.log(response);
                            if (response.status === "success") {
                                fetchCategories();
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