<?php
session_start();
include "header.php";
?>

<title>
<?php 
    echo $_GET["page"];
?>
</title>
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
    <div class="container">
        <a class="navbar-brand" href="index.php">My Clothing Store</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="topNavbar">
            <ul class="navbar-nav ms-auto">
                <?php
                if (isset($_SESSION["current_user"])) {
                    echo '  <li class="nav-item">
                                <a class="nav-link active" href="ViewProducts.php?page=Products">Products</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="UserViewCart.php?page=Cart">MyCart</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="UserOrderHistory.php?page=Order History">Order history</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="UserViewNotification.php?page=Notifications">Notification</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="UserProfile.php?page=Profile">MyProfile</a>
                            </li>
                            '
                            ;
                }
                else{
                    echo '<li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Login.php?page=Login">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Register.php?page=Register">Register</a>
                </li>';
                }
                ?>
                
            </ul>
        </div>
    </div>
</nav>