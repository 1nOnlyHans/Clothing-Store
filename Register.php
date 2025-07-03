<?php
include "./includes/navbar.php";
?>

<div class="container">
    <div class="card mt-5 mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h1 class="text-center py-3">Register Account</h1>
            <form method="post" id="Register-form">
                <div class="mb-3">
                    <input type="text" name="reg_firstname" id="reg_firstname" class="form-control" placeholder="Enter your firstname">
                </div>
                <div class="mb-3">
                    <input type="text" name="reg_lastname" id="reg_lastname" class="form-control" placeholder="Enter your lastname">
                </div>
                <div class="mb-3">
                    <input type="email" name="reg_email" id="reg_email" class="form-control" placeholder="Enter your email">
                </div>
                <div class="mb-3">
                    <input type="password" name="reg_password" id="reg_password" class="form-control" placeholder="Enter your Password">
                </div>
                <div class="mb-3 d-flex justify-content-center">
                    <button type="submit" class="btn btn-secondary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#Register-form').on('submit', function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                method: "post",
                url: "./controllers/Register.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (response.status === "success") {
                        $('#Register-form')[0].reset();
                        Swal.fire({
                            icon: "success",
                            title: response.message,
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
            })
        });
    })
</script>