<?php
include "./includes/admin_sidebar.php";
?>
<div class="container">
    <div class="page-inner">
        <div class="card mb-4">
            <div class="card-body" id="profile-view">
                <h1 class="text-center mb-4">My Profile</h1>
                <!-- User info will be injected here -->
            </div>
        </div>

        <div class="card">
            <div class="card-body" id="profile-update">
                <h2 class="text-center mb-4">Update Profile</h2>
                <form id="update-profile-form">
                    <input type="hidden" name="userID" id="userID">
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Enter your first name">
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Enter your last name">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-select">
                            <option value="Admin">Admin</option>
                            <option value="User">User</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function(){
        fetchUserDetails();

        function fetchUserDetails(){
            $.ajax({
                method: "get",
                url: "./controllers/Profile.php",
                dataType:"json",
                success: function(response){
                    const viewContainer = $('#profile-view');
                    const form = $('#update-profile-form');

                    if(response.length > 0){
                        const user = response[0];

                        // === Fill VIEW CARD ===
                        const viewCard = `
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <img src="./public/uploads/user_images/${user.profile_img}" alt="Profile Image"
                                        class="img-thumbnail rounded-circle mb-3"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <div class="col-md-8">
                                    <p><strong>Full Name:</strong> ${user.firstname} ${user.lastname}</p>
                                    <p><strong>Email:</strong> ${user.email}</p>
                                    <p><strong>Role:</strong> ${user.role}</p>
                                    <p><strong>Status:</strong> <span class="badge ${user.status === 'Active' ? 'bg-success' : 'bg-secondary'}">${user.status}</span></p>
                                    <p><strong>Joined:</strong> ${user.created_at}</p>
                                </div>
                            </div>
                        `;
                        viewContainer.append(viewCard);

                        // === Fill UPDATE FORM ===
                        $('#userID').val(user.id);
                        $('#firstname').val(user.firstname);
                        $('#lastname').val(user.lastname);
                        $('#email').val(user.email);
                        $('#role').val(user.role);
                        $('#status').val(user.status);
                    } else {
                        viewContainer.append(`<div class="alert alert-info">No user data found.</div>`);
                    }
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                }
            });
        }

        // === Example submit event ===
        $('#update-profile-form').submit(function(e){
            e.preventDefault();

            const formData = new FormData(this);
            $.ajax({
                method: "POST",
                url: "./controllers/UpdateProfile.php", 
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(response){
                    console.log(response);
                    alert(response.message);
                    
                    $('#profile-view').empty();
                    fetchUserDetails();
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                }
            });
        });

    });
</script>
