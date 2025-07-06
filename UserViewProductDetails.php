<?php
include "./includes/navbar.php";

?>
<div class="container">
    <div class="row" id="container"></div>
</div>
<script>
    $(document).ready(function() {
        const fetchProductDetails = () => {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            const productID = urlParams.get('productID');
            $.ajax({
                method: "post",
                url: "./controllers/GetOneProduct.php",
                data: {
                    productID: productID
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    const container = $('#container');
                    if (response.data.length === 0) {
                        container.append(`<div class="page-inner">
                        <h1 class='text-center'>Invalid Product</h1></div>`);
                    } else {
                        const product = response.data[0];
                        const categories = response.category_selection;
                        const categoriesOptions = categories.map((category) => `
                            <option value="${category.category_id}" ${product.category === category.category_id ? "selected" : ""}>${category.category_name}</option>
                        `).join("");
                        const productVariants = response.variants.length === 0 ?
                            `<p class="text-center">No Variants</p>` :
                            response.variants.map((variant) => `
                                <div class="card mb-3" style="min-height: 120px;">
                                <div class="card-body">
                                    <div class="row align-items-center g-3">
                                    <div class="col-md-2">
                                        <div style="width: 100%; height: 100px; overflow: hidden;">
                                        <img src="./public/uploads/variant_images/${variant.image}"
                                            alt="Variant Image"
                                            style="width: 100%; height: 100%; object-fit: cover;"
                                            class="rounded">
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="row">
                                        <div class="col-md-2"><p class="mb-1"><strong>Size:</strong> ${variant.size}</p></div>
                                        <div class="col-md-2"><p class="mb-1"><strong>Color:</strong> ${variant.color}</p></div>
                                        <div class="col-md-2"><p class="mb-1"><strong>Price:</strong> ₱${parseFloat(variant.price).toFixed(2)}</p></div>
                                        <div class="col-md-2"><p class="mb-1"><strong>Stock:</strong> ${variant.stock}</p></div>
                                        <div class="col-md-2">
                                            <p class="mb-1">
                                            <strong>Status:</strong> 
                                            <span class="badge ${variant.status === "Available" ? "text-bg-success" : variant.status === "Unavailable" ? "text-bg-danger" : "text-bg-secondary"}">${variant.status}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <form method="post" class="d-flex flex-column gap-2 addToCart-form">
                                            <input type="hidden" name="productID" value="${variant.product_id}">
                                            <input type="hidden" name="variantID" value="${variant.id}">
                                            ${variant.status === "Unavailable" || variant.status === "Out of Stock" ? `` : `
                                                <input type="number" name="quantity" value="1" min="1" max="${variant.stock}" class="form-control mb-1">
                                            `}
                                            <button type="submit" class="btn btn-primary btn-sm" name="addToCartBtn"
                                                    ${variant.status === "Unavailable" || variant.status === "Out of Stock" ? "disabled" : ""}>
                                                Add to Cart
                                            </button>
                                            </form>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                </div>
                            `).join("");
                        const productCard = () => `
                        <div class="page-inner py-4">
                            <div class="row gx-4 gy-4" style="min-height: 600px;">

                            <!-- LEFT: Product Info -->
                            <div class="col-lg-4">
                                <div class="card shadow-sm h-100">
                                <div class="card-body text-center">
                                    <h4 class="card-title mb-3 fw-bold">${product.name}</h4>
                                    <div style="width: 100%; height: 250px; overflow: hidden;">
                                    <img src="./public/uploads/product_images/${product.image}"
                                        alt="Product Image"
                                        style="width: 100%; height: 100%; object-fit: cover;"
                                        class="rounded">
                                    </div>
                                    <hr>
                                    <p class="text-start"><strong>Description:</strong> ${product.description}</p>
                                </div>
                                </div>
                            </div>

                            <!-- RIGHT: Variants -->
                            <div class="col-lg-8">
                                <div class="card shadow-sm h-100 d-flex flex-column">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="mb-0 fw-bold">Product Variants (${response.variants.length})</h4>
                                    </div>
                                    <div class="flex-grow-1 overflow-auto" style="max-height: 500px;">
                                    ${productVariants}
                                    </div>
                                </div>
                                </div>
                            </div>

                            </div>
                        </div>
                        `;

                        container.empty();
                        container.append(productCard);
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
        fetchProductDetails();

        $(document).on('submit', '.addToCart-form', function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                method: "post",
                url: "./controllers/AddToCart.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        fetchProductDetails();
                        Swal.fire({
                            icon: "success",
                            title: response.message
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: response.message
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            })
        });
    });
</script>