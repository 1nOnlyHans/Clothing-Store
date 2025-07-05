<?php
require_once "Dbh.php";
require_once "Products.php";
require_once "Categories.php";
require_once "ProductVariant.php";
require_once "AdminUserManagement.php";

class Admin extends Dbh
{
    private $db;
    private $Product;
    private $categories;
    private $productVariants;
    private $UserManagement;
    public function __construct()
    {
        $this->db = $this->Connect();
        $this->Product = new Products($this->db);
        $this->categories = new Category($this->db);
        $this->productVariants = new ProductVariant($this->db);
        $this->UserManagement = new UserManagement($this->db);
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
    public function addProductVariant($productID, $size, $color, $price, $stock,$status,$image)
    {
        return $this->productVariants->addVariant($productID, $size, $color, $price, $stock,$status,$image);
    }

    public function updateVariant($variantID, $size, $color, $price, $stock, $status,$image)
    {
        return $this->productVariants->updateVariant($variantID, $size, $color, $price, $stock, $status,$image);
    }

    public function removeVariant($variantID)
    {
        return $this->productVariants->removeVariant($variantID);
    }

    public function getProductVariants($productID)
    {
        return $this->productVariants->GetAllVariants($productID);
    }

    //UserManagement Methods
    public function getUserById($id)
    {
        return $this->UserManagement->getUserById($id);
    }

    public function getUserByRole($role)
    {
        return $this->UserManagement->getUserByRole($role);
    }

    public function addUser($firstname,$lastname,$email,$role){
        return $this -> UserManagement -> addUser($firstname,$lastname,$email,$role);
    }
    public function updateUser($id, $firstname, $lastname, $email, $role, $image)
    {
        return $this->UserManagement->updateUser($id, $firstname, $lastname, $email, $role, $image);
    }

    public function deleteUser($id) {
        return $this->UserManagement->deleteUser($id);
    }
}
