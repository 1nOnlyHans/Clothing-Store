<?php
include "./includes/admin_sidebar.php";
?>
<div class="container">
    <div class="page-inner" id="container">

    </div>
</div>
<?php
include "./includes/AddProductVariantModal.php";
include "./includes/EditVariantModal.php";
?>
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
                            <div class="card mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                <div class="col-md-2">
                                    <div style="width: 100%; height: 100px; overflow: hidden;">
                                    <img src="./public/uploads/variant_images/${variant.image}"
                                        alt="Variant Image"
                                        style="width: 100%; height: 100%; object-fit: cover;"
                                        class="rounded">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <p class="mb-1"><strong>Size:</strong> ${variant.size}</p>
                                </div>
                                <div class="col-md-2">
                                    <p class="mb-1"><strong>Color:</strong> ${variant.color}</p>
                                </div>
                                <div class="col-md-2">
                                    <p class="mb-1"><strong>Price:</strong> ₱${parseFloat(variant.price).toFixed(2)}</p>
                                </div>
                                <div class="col-md-2">
                                    <p class="mb-1"><strong>Stock:</strong> ${variant.stock}</p>
                                </div>
                                <div class="col-md-2 text-end">
                                    <div class="d-flex justify-content-center align-content-center gap-2">
                                    <button class="btn btn-outline-primary btn-sm edit" data-id="${variant.id}"
                                    data-size="${variant.size}" data-color="${variant.color}" data-price="${parseFloat(variant.price).toFixed(2)}" data-stock="${variant.stock}">Edit</button>
                                    <button class="btn btn-outline-danger btn-sm delete" data-id="${variant.id}">Delete</button>
                                    </div>
                                </div>
                                </div>
                            </div>
                            </div>
                        `).join("");
                        const productCard = () => `
                    <div class="container py-4">
                        <div class="page-inner">
                        <div class="row" style="min-height: 600px;">
                            
                            <!-- LEFT: Product Image + Form -->
                            <div class="col-lg-4 d-flex flex-column">
                            
                            <!-- Image Card WITH product name -->
                            <div class="card shadow-sm mb-3 flex-grow-1">
                            <div class="card-body text-center">
                                <h5 class="card-title mb-3">${product.name}</h5>
                                <div style="width: 100%; height: 250px; overflow: hidden;">
                                <img src="./public/uploads/product_images/${product.image}"
                                    alt="Product Image"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                    class="rounded">
                                </div>
                            </div>
                            </div>
                            
                            <!-- Update Form Card -->
                            <div class="card shadow-sm flex-grow-1">
                                <div class="card-body">
                                <form id="update-product-form" enctype="multipart/form-data">
                                    <input type="hidden" name="productID" id="productID" value="${product.id}">

                                    <div class="mb-3">
                                    <label for="name" class="form-label">Product Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="${product.name}" required>
                                    </div>

                                    <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="3" required>${product.description}</textarea>
                                    </div>

                                    <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select name="category" id="category" class="form-select">
                                        ${categoriesOptions}
                                    </select>
                                    </div>

                                    <div class="mb-3">
                                    <label for="image" class="form-label">Replace Image</label>
                                    <input type="file" name="image" id="image" class="form-control">
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Update Product</button>
                                </form>
                                </div>
                            </div>
                            </div>

                            <!-- RIGHT: Variants -->
                            <div class="col-lg-8 d-flex flex-column">
                            <div class="card shadow-sm flex-grow-1 d-flex flex-column">
                                <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h3 class="mb-0">Product Variants (${response.variants.length})</h3>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#AddProductVariantModal">
                                    + Add Variant
                                    </button>
                                </div>
                                <div class="variants-list flex-grow-1 overflow-auto">
                                    ${productVariants}
                                </div>
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

        $(document).on('submit', '#update-product-form', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                method: "post",
                url: "./controllers/UpdateProduct.php",
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
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
            });
        });

        $('#add-product-variant-form').on('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                method: "POST",
                url: "./controllers/AddVariant.php",
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    if (response.status === "success") {
                        $('#add-product-variant-form')[0].reset();
                        fetchProductDetails();
                        // Close the modal properly
                        let modal = bootstrap.Modal.getInstance(document.getElementById('AddProductVariantModal'));
                        modal.hide();
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
                error: function(error) {
                    console.log(error.responseText);
                }
            });
        });

        $(document).on('click', '.edit', function() {
            const id = $(this).attr('data-id');
            const size = $(this).attr('data-size');
            const color = $(this).attr('data-color');
            const price = $(this).attr('data-price');
            const stock = $(this).attr('data-stock');
            $('#EditVariantModal input[name="variantID"]').val(id);
            $('#EditVariantModal input[name="size"]').val(size);
            $('#EditVariantModal input[name="color"]').val(color);
            $('#EditVariantModal input[name="price"]').val(price);
            $('#EditVariantModal input[name="stock"]').val(stock);
            $('#EditVariantModal').modal('show');
        });

        $('#update-product-variant-form').on('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                method: "POST",
                url: "./controllers/UpdateVariant.php",
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    if (response.status === "success") {
                        fetchProductDetails();
                        let modal = bootstrap.Modal.getInstance(document.getElementById('EditVariantModal'));
                        modal.hide();
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
                error: function(error) {
                    console.log(error.responseText);
                }
            });
        });

        $(document).on('click', '.delete', function() {
            const id = $(this).attr('data-id');
            Swal.fire({
                title: "Are you sure?",
                text: "This variant will be removed!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "POST",
                        url: "./controllers/DeleteVariant.php",
                        data: {
                            variantID: id
                        },
                        dataType: "json",
                        success: function(response) {
                            console.log(response);
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
                    });
                }
            });
        });
    });
</script>