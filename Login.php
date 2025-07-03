<?php
include "./includes/navbar.php";
?>

<div class="container">
    <div class="card mt-5 mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h1 class="text-center py-3">Login Account</h1>
            <form method="post" id="Login-form">
                <div class="mb-3">
                    <input type="email" name="log_email" id="log_email" class="form-control" placeholder="Enter your email">
                </div>
                <div class="mb-3">
                    <input type="password" name="log_password" id="log_password" class="form-control" placeholder="Enter your Password">
                </div>
                <div class="mb-3 d-flex justify-content-center">
                    <button type="submit" class="btn btn-secondary">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#Login-form').on('submit', function(event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                method: "post",
                url: "./controllers/Login.php",
                data: formData,
                dataType: "json",
                
                success: function(response) {
                    console.log(response);
                    if (response.status === "success") {
                        $('#Login-form')[0].reset();
                        Swal.fire({
                            icon: "success",
                            title: response.message,
                            timer: 2000,
                            timerProgressBar: true,
                        });

                        setTimeout(() => {
                            if(response.role === "Admin"){
                                window.location.href = `AdminDashboard.php`;
                            }
                            else if(response.role === "User"){
                                window.location.href = `UserDashboard.php`;
                            }
                        },3000)

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
            })
        });
    })
</script>