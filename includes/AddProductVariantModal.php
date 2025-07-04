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
                        <label for="size" class="form-label">Size</label>
                        <input type="text" name="size" id="size" class="form-control" value="<?php echo htmlspecialchars($data['size'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="text" name="color" id="color" class="form-control" value="<?php echo htmlspecialchars($data['color'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" name="price" id="price" step="0.01" class="form-control" value="<?php echo htmlspecialchars($data['price'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" name="stock" id="stock" class="form-control" value="<?php echo htmlspecialchars($data['stock'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>