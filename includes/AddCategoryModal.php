<?php
    require "./Classes/Admin.php";
?>
<div class="modal fade" id="AddProductModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Add Category</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="add-category-form">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">
                            Category Name
                        </label>
                        <input type="text" name="category_name" id="category_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_description" class="form-label">
                            Category Description
                        </label>
                        <textarea name="category_description" id="category_description" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>