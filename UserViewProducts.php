<?php
include "./includes/navbar.php";
include "./includes/dashboard_session.php";
?>

<!-- Top Navbar -->


<!-- Hero Section -->
<!-- <header class="bg-primary text-white text-center py-5 hero">
    <div class="container">
        <h1 class="display-4">Welcome to My Online Clothing Store</h1>
        <p class="lead">Trendy. Affordable. Stylish.</p>
    </div>
</header> -->

<!-- Featured Products Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Featured Products</h2>
        <div class="row g-4">
            <?php
            require './Classes/Dbh.php';
            require './Classes/Products.php';
            $dbh = new Dbh();
            $conn = $dbh->Connect();
            $fetchProducts = new Products($conn);
            $products = $fetchProducts->GetAllProducts();

            foreach ($products as $product) {
                echo '
                    <div class="col-md-4">
                    <div class="card">
                        <img src="./public/uploads/product_images/' . $product["image"] . '" class="card-img-top" alt="Product 1">
                        <div class="card-body">
                        <h5 class="card-title">' . $product["name"] . '</h5>
                        <p class="card-text">Category: ' . $product["category_name"] . '</p>
                        <p class="card-text">Variants: ' . $product["total_variants"] . '</p>
                        <a href="UserViewProductDetails.php?productID='.$product["id"].'" class="btn btn-primary">View</a>
                        </div>
                    </div>
                    </div>
                ';
            }
            ?>

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
        $.ajax({
            method: "GET",
            url: "./controllers/GetAllProducts.php",
            dataType: "json",
            success: function(response) {
                const container = $('#product-container');

                if (response.length > 0) {
                    const products = response.map((item) =>
                        `   
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-header">
                                        <img src="./public/uploads/product_images/${item.image}" class="img img-thumbnail" style="width: 350; height: auto;">
                                    </div>
                                    <div class="card-body">
                                        <p class="text-center">${item.name}</p>
                                    </div>
                                </div>
                            </div>
                        `
                    );
                    container.append(products);
                } else {
                    container.append(`<h1 class="text-center mt-5">No Available Products</h1>`)
                }
            },
            error: function(xhr) {
                console.log(xhr);
            }
        })
    });
</script>