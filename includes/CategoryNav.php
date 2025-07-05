
<ul class="navbar-nav mb-2 mb-lg-0">
    <?php
    require_once "./Classes/Dbh.php";
    require_once "./Classes/Categories.php";

    $dbh = new Dbh();
    $conn = $dbh->Connect();

    $categoryObj = new Category($conn);
    $categories = $categoryObj->GetAllCategories();

    if ($categories) {
        foreach ($categories as $category) {
        echo '<li class="nav-item">
                <a class="nav-link" href="productPerCategory.php?category=' . htmlspecialchars($category["category_id"]) . '">'
                    . htmlspecialchars($category["category_name"]) .
                '</a>
                </li>';
        }
    } else {
        echo '<li class="nav-item">
                <span class="nav-link text-white">No categories found</span>
            </li>';
    }
    ?>
</ul>


