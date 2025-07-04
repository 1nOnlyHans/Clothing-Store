<?php
require "./Classes/Admin.php";
?>
<div class="modal fade" id="AddProductModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Add Product</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="add-product-form" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Product name
                        </label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Description
                        </label>
                        <textarea name="description" id="description" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">
                            Category
                        </label>
                        <select name="category" id="category" class="form-select">
                            <?php
                            $action = new Admin();
                            $categories = $action->getAllCategories();
                            foreach ($categories as $category) {
                                echo '<option value="' . $category["category_id"] . '">' . $category["category_name"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">
                            Image
                        </label>
                        <input type="file" name="image" id="image" class="form-control" required></input>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>