<?php
include "./includes/navbar.php";

// Check if user is logged in
if (!isset($_SESSION['current_user'])) {
    header("Location: login.php");
    exit();
}

?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">User Profile</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION["current_user"] -> firstname . " " . $_SESSION["current_user"] -> lastname); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION["current_user"] -> email); ?></p>
                    <!-- Add more user info as needed -->

                    <form method="post" action="./controllers/Logout.php">
                        <a href="Logout.php" class="btn btn-danger">Log Out</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>