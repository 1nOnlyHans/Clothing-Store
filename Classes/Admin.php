<?php
require_once "Dbh.php";
require_once "Products.php";
require_once "Categories.php";
require_once "ProductVariant.php";

class Admin extends Dbh
{
    private $db;
    private $Product;
    private $categories;
    private $productVariants;

    public function __construct()
    {
        $this->db = $this->Connect();
        $this->Product = new Products($this->db);
        $this->categories = new Category($this->db);
        $this->productVariants = new ProductVariant($this->db);
    }

    // Product methods
    public function addProduct($name, $description, $category, $image)
    {
        return $this->Product->addProduct($name, $description, $category, $image);
    }

    public function updateProduct($id, $name, $description, $category, $image)
    {
        return $this->Product->updateProduct($id, $name, $description, $category, $image);
    }

    public function removeProduct($id)
    {
        return $this->Product->removeProduct($id);
    }

    public function getAllProducts()
    {
        return $this->Product->GetAllProducts();
    }

    public function getOneProduct($id)
    {
        return [
            "data" => $this->Product->GetOneProduct($id),
            "category_selection" => $this->getAllCategories(),
            "variants" => $this->productVariants->GetAllVariants($id)
        ];
    }

    // Category methods
    public function addCategory($category_name, $category_description)
    {
        return $this->categories->AddCategory($category_name, $category_description);
    }

    public function editCategory($categoryID, $category_name, $category_description)
    {
        return $this->categories->EditCategory($categoryID, $category_name, $category_description);
    }

    public function removeCategory($categoryID)
    {
        return $this->categories->removeCategory($categoryID);
    }

    public function getAllCategories()
    {
        return $this->categories->GetAllCategories();
    }

    public function getOneCategory($categoryID)
    {
        return $this->categories->GetOneCategory($categoryID);
    }

    // Product variant methods
    public function addProductVariant($productID,$size,$color,$price,$stock,$image)
    {
        return $this->productVariants->addVariant($productID,$size,$color,$price,$stock,$image);
    }

    public function updateVariant($variantID,$size,$color,$price,$stock,$image){
        return $this -> productVariants -> updateVariant($variantID,$size,$color,$price,$stock,$image);
    }

    public function removeVariant($variantID){
        return $this -> productVariants -> removeVariant($variantID);
    }

    public function getProductVariants($productID)
    {
        return $this->productVariants->GetAllVariants($productID);
    }

}
