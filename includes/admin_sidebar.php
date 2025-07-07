<?php
session_start();
include "./includes/dashboard_session.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title><?php echo $_GET["page"];?></title>
  <meta
    content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
    name="viewport" />
  <?php
  require "lib.php";
  ?>
</head>

<body class="bg-light">
  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar sidebar-style-2">
      <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header bg-secondary">
          <p class="text-white h5">Admin Panel</p>
          <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar">
              <i class="gg-menu-right"></i>
            </button>
            <button class="btn btn-toggle sidenav-toggler">
              <i class="gg-menu-left"></i>
            </button>
          </div>
          <button class="topbar-toggler more">
            <i class="gg-more-vertical-alt"></i>
          </button>
        </div>
        <!-- End Logo Header -->
      </div>
      <div class="sidebar-wrapper scrollbar scrollbar-inner bg-light">
        <div class="sidebar-content">
          <ul class="nav nav-secondary">
            <li class="nav-item">
              <a href="AdminDashboard.php?page=AdminDashboard">
                <i class="fa-solid fa-house"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="AdminUserManagement.php?page=UserManagement">
                <i class="fa-solid fa-folder-open"></i>
                <p>User Management</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="AdminProductCategories.php?page=ProductCategories">
                <i class="fa-solid fa-bell"></i>
                <p>Product Categories</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="AdminProductsManagement.php?page=ProductManagement">
                <i class="fa-solid fa-bell"></i>
                <p>Products</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="AdminOrderManagement.php?page=OrderManagement">
                <i class="fa-solid fa-chalkboard"></i>
                <p>Orders</p>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- End Sidebar -->

    <div class="main-panel">
      <div class="main-header">
        <div class="main-header-logo">
          <!-- Logo Header -->
          <div class="logo-header bg-secondary">
            <a href="index.html" class="logo text-white">
              Admin Panel
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <!-- Navbar Header -->
        <nav
          class="navbar navbar-header navbar-header-transparent navbar-expand-lg bg-secondary">
          <div class="container-fluid">
            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
              <li class="nav-item topbar-user dropdown hidden-caret">
                <a
                  class="dropdown-toggle profile-pic"
                  data-bs-toggle="dropdown"
                  href="#"
                  aria-expanded="false">
                  <div class="avatar-sm">
                    <!-- <img
                        src="public/uploads/profile-img/"
                        alt="..."
                        class="avatar-img rounded-circle"
                      /> -->
                  </div>
                  <span class="profile-username">
                    <span class="op-7 text-white">Hi,</span>
                    <span class="fw-bold text-white">
                      <?php
                      echo $_SESSION["current_user"]->firstname;
                      ?>
                    </span>
                  </span>
                </a>
                <ul class="dropdown-menu dropdown-user animated fadeIn me-5">
                  <div class="dropdown-user-scroll scrollbar-outer">
                    <li>
                      <div class="user-box">
                        <div class="u-text text-white">
                          <h4>
                            <?php
                            echo $_SESSION['current_user']->firstname . " " . $_SESSION["current_user"]->lastname;
                            ?>
                          </h4>
                          <p class="text-muted ">
                            <?php
                            echo $_SESSION["current_user"]->email;
                            ?>
                          </p>
                          <a
                            href="AdminViewProfile.php?page=Profile"
                            class="btn btn-xs btn-sm bg-secondary">View Profile</a>
                        </div>
                      </div>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="Logout.php">Logout</a>
                    </li>
                  </div>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
        <!-- End Navbar -->
      </div>