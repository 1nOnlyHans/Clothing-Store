<?php
require "./Classes/Admin.php";
?>
<div class="modal fade" id="UpdateUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Update User</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="add-user-form">
                    <div class="mb-3">
                        <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Enter your firstname">
                    </div>
                    <div class="mb-3">
                        <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Enter your lastname">
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email">
                    </div>
                    <div class="mb-3">
                        <select name="role" id="role" class="form-select">
                            <option selected>Select Role</option>
                            <option value="Admin">Admin</option>
                            <option value="User">User</option>
                        </select>
                    </div>
                    <div class="mb-3 d-flex justify-content-center">
                        <button type="submit" class="btn btn-secondary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>