<?php
include "./includes/admin_sidebar.php";
?>

<div class="container">
    <div class="page-inner">
        <h1 class="text-center my-5">User Management</h1>
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#AddUserModal">
            Add User
        </button>
        <div class="card">
            <div class="card-body">
                <div style="overflow-x: auto;">
                    <table class="table table-bordered" id="product-tbl">
                        <thead>
                            <tr>
                                <th class="text-center">Name</th>
                                <th class="text-center">Image</th>
                                <th class="text-center">email</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Date Created</th>
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
include "./includes/AddUserModal.php";
include "./includes/UpdateUserForm.php";
?>
<script>
    $(document).ready(function() {
        fetchUsers();

        function fetchUsers() {
            $.ajax({
                method: "GET",
                url: "./controllers/GetUsers.php",
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    $("#product-tbl").DataTable({
                        data: response,
                        columns: [{
                                data: null,
                                class: "text-center",
                                render: function(data, type, row) {
                                    return `${data.firstname} ${data.lastname}`;
                                }
                            },
                            {
                                data: null,
                                class: "text-center",
                                render: function(data, type, row) {
                                    return `<img src="./public/uploads/user_images/${data.profile_img}" alt="Profile Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">`;
                                }
                            },
                            {
                                data: "email",
                                class: "text-center"
                            },
                            {
                                data: "role",
                                class: "text-center"
                            },
                            {
                                data: "status",
                                class: "text-center"
                            },
                            {
                                data: "created_at",
                                class: "text-center"
                            },
                            {
                                data: null,
                                render: function(data, type, row) {
                                    return `
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-primary edit" data-bs-toggle="modal" data-bs-target="#UpdateUserModal" data-id="${data.id}"
                        data-firstname="${data.firstname}"
                        data-lastname="${data.lastname}"
                        data-email="${data.email}"
                        data-role="${data.role}"
                        data-status="${data.status}">
                            Edit
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm delete" data-id="${data.id}">Delete</button>
                    </div>`;
                                }
                            }
                        ],
                        language: {
                            emptyTable: "No users found"
                        },
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

        $('#add-user-form').on('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                method: "POST",
                url: "./controllers/AddUser.php",
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    if (response.status === "success") {
                        $('#add-user-form')[0].reset();
                        fetchUsers();
                        let Modal = bootstrap.Modal.getInstance(document.getElementById('AddUserModal'));
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
                text: "User account will be deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "POST",
                        url: "./controllers/RemoveUser.php",
                        data: {
                            userID: id
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.status === "success") {
                                fetchUsers();
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

        $(document).on('click', '.edit', function() {
            const id = $(this).data('id');
            const firstname = $(this).data('firstname');
            const lastname = $(this).data('lastname');
            const email = $(this).data('email');
            const role = $(this).data('role');
            const status = $(this).data('status');

            $('#UpdateUserModal input[name="userID"]').val(id);
            $('#UpdateUserModal input[name="firstname"]').val(firstname);
            $('#UpdateUserModal input[name="lastname"]').val(lastname);
            $('#UpdateUserModal input[name="email"]').val(email);
            $('#UpdateUserModal select[name="role"]').val(role);
            $('#UpdateUserModal select[name="status"]').val(status);
            $('#UpdateUserModal').modal('show');
        });

        $(document).on('click', '.edit', function() {
            const id = $(this).data('id');
            const firstname = $(this).data('firstname');
            const lastname = $(this).data('lastname');
            const email = $(this).data('email');
            const role = $(this).data('role');
            const status = $(this).data('status');

            $('#UpdateUserModal input[name="userID"]').val(id);
            $('#UpdateUserModal input[name="firstname"]').val(firstname);
            $('#UpdateUserModal input[name="lastname"]').val(lastname);
            $('#UpdateUserModal input[name="email"]').val(email);
            $('#UpdateUserModal select[name="role"]').val(role);
            $('#UpdateUserModal select[name="status"]').val(status);
            $('#UpdateUserModal').modal('show');
        });

        $('#update-user-form').on('submit', function(event) {
            event.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                method: "POST",
                url: "./controllers/UpdateUser.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (response.status === "success") {
                        fetchUsers();
                        let modal = bootstrap.Modal.getInstance(document.getElementById('UpdateUserModal'));
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
    });
</script>