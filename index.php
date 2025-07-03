<?php
include "./includes/navbar.php";
include "./includes/session.php";
?>


<section class="hero">
    <div class="container">
        <h1>Discover Your Style</h1>
        <a href="#products-section" class="btn btn-primary btn-lg mt-3">Shop Now</a>
    </div>
</section>

<div class="container" id="products-section">
    <h1 class="text-center mt-5">Products</h1>
    <div
        class="row justify-content-center align-items-center g-2" id="product-container">
    </div>

</div>
<style>
    * {
        scroll-behavior: smooth;
    }

    .hero {
        background: linear-gradient(rgba(9, 5, 54, 0.3), rgba(5, 4, 46, 0.7)), url('./public/assets/landingpageBg.jpg') center center/cover no-repeat;
        height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
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

                if (response.data.length > 0) {
                    const products = response.data.map((item) =>
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