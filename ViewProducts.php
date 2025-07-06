<?php
include "./includes/navbar.php";
?>

<!-- Top Navbar -->


<!-- Hero Section -->
<header class="bg-primary text-white text-center py-5 hero">
    <div class="container">
        <h1 class="display-4">Welcome to My Online Clothing Store</h1>
        <p class="lead">Trendy. Affordable. Stylish.</p>
    </div>
</header>

<section class="py-5">
    <div class="container-fluid">
        <div class="container">
            <h2 class="text-center mb-4">Featured Products</h2>
            <div class="row mb-4">
                <div class="col-md-8 mb-2 mb-md-0">
                    <input type="text" name="product_name" id="product_name" class="form-control form-control-lg" placeholder="Search by name...">
                </div>
                <div class="col-md-4">
                    <form id="browseForm">
                        <select name="category" id="category" class="form-select form-select-lg">
                            <option value="">All Categories</option>
                            <?php
                            require './Classes/Dbh.php';
                            require './Classes/Categories.php';
                            $dbh = new Dbh();
                            $conn = $dbh->Connect();
                            $fetchProducts = new Category($conn);
                            $categories = $fetchProducts->GetAllCategories();

                            foreach ($categories as $category) {
                                echo '
                                <option value="' . $category["category_id"] . '">' . $category["category_name"] . '</option>
                            ';
                            }
                            ?>
                        </select>

                    </form>

                </div>
            </div>
            <div class="row g-4" id="products-container">

            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<!-- <footer class="bg-dark text-white text-center py-3">
    <div class="container">
      &copy; <?php echo date("Y"); ?> My Online Clothing Store. All rights reserved.
    </div>
  </footer> -->

<style>
    * {
        scroll-behavior: smooth;
    }

    .hero {
        background: linear-gradient(rgba(9, 5, 54, 0.3), rgba(5, 4, 46, 0.7)), url('./public/assets/landingpageBg.jpg') center center/cover no-repeat;
        height: 30vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        background-size: cover;
    }

    /* Fix card size */
    .card {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-align: center;
    }

    /* Fix image size inside card */
    .card-img-top {
        height: 200px;
        /* Adjust as needed */
        object-fit: cover;
        /* Crop to fill */
    }

    /* Center text contents */
    .card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .card-title,
    .card-text {
        margin: 0 0 10px;
    }

    .card .btn {
        margin-top: auto;
        /* Push button to bottom if needed */
    }
</style>

<script>
    $(document).ready(function() {
        browse();

        function browse() {
            var product_name = $('#product_name').val();
            var category = $('#category').val();
            $.ajax({
                method: "POST",
                url: "./controllers/viewProducts.php",
                data: {
                    product_name: product_name,
                    category: category
                },
                dataType: "json",
                success: function(response) {
                    const container = $('#products-container');
                    console.log(response)
                    if (response.length > 0) {
                        const products = response.map((item) =>
                            `   
                            <div class="col-md-3">
                            <div class="card">
                                <img src="./public/uploads/product_images/${item.image}" class="card-img-top" alt="Product 1">
                                <div class="card-body">
                                <h5 class="card-title">${item.name}</h5>
                                <p class="card-text">Category:${item.category_name}</p>
                                <p class="card-text">Variants:${item.total_variants}</p>
                                <a href="UserViewProductDetails.php?productID=${item.id}" class="btn btn-primary">View</a>
                                </div>
                            </div>
                            </div>
                        `
                        );
                        container.empty();
                        container.append(products);
                    } else {
                        container.empty();
                        container.append(`<p class="text-center mt-5">No results</>`)
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        $('#browseForm').on('change', function() {
            browse();
        });
        $('#product_name').on('keydown', function() {
            browse();
        })
    });
</script>