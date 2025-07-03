<?php
require_once "Dbh.php";
require_once "Products.php";
require_once "Categories.php";

class Admin extends Dbh
{
    private $db;
    private $Product;
    private $categories;
    public function __construct()
    {
        $this->db = $this->Connect();
        $this->Product = new Products($this->db);
        $this->categories = new Category($this->db);
    }

    public function addProduct($name, $description, $price, $category, $image)
    {
        return $this->Product->addProduct($name, $description, $price, $category, $image);
    }

    public function updateProduct($id, $name, $description, $price, $category)
    {
        return $this->Product->updateProduct($id, $name, $description, $price, $category);
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
        return $this->Product->GetOneProduct($id);
    }

    public function addCategory($category_name,$category_description){
        return $this -> categories -> AddCategory($category_name,$category_description);
    }
    
    public function getAllCategories(){
        return $this -> categories -> GetAllCategories();
    }

}
