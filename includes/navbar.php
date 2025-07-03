<?php
session_start();
include "header.php";
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand text-dark" href="#">AYASIB CLOTHING</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-dark" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="Login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="Register.php">Register</a>
                </li>
            </ul>
            <div class="d-flex">
                <a class="nav-link" href="#">My Cart</a>
            </div>
        </div>
    </div>
</nav>