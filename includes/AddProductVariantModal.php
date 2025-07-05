<div class="modal fade" id="AddProductVariantModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Add Product</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="add-product-variant-form" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="hidden" name="productID" id="productID" value="<?php echo $_GET["productID"] ?>">
                        <label for="size" class="form-label">Size</label>
                        <select name="size" id="size" class="form-select" required>
                            <option selected>Select Size</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="X">X</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                            <option value="4XL">4XL</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="text" name="color" id="color" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" name="price" id="price" step="0.01" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" name="stock" id="stock" class="form-control" required>
                    </div>
                    <select name="status" id="status" class="form-select">
                        <option value="Available">Available</option>
                        <option value="Unavailable">Unavailable</option>
                        <option value="Out of Stock">Out of Stock</option>
                    </select>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" name="image" id="image" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>