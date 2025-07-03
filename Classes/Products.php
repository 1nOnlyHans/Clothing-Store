<?php

require "Image.php";

class Products
{
    protected $db;
    private $table = "products";
    private $imageUpload;

    public function __construct($db)
    {
        $this->db = $db;
        $this->imageUpload = new Image($db);
    }

    public function addProduct($name, $description, $price, $category,$image)
    {
        $name = htmlspecialchars(trim($name));
        $description = htmlspecialchars(trim($description));
        $price = floatval($price);
        $category = htmlspecialchars(trim($category));

        if (empty($name) || empty($description) || empty($price) || empty($category)) {
            return [
                "status" => "error",
                "message" => "Fill all the required fields"
            ];
        }

        if (!is_numeric($price) || $price <= 0) {
            return [
                "status" => "error",
                "message" => "Invalid Price"
            ];
        }

        if(isset($image) && !empty($image)){
            $imageDatabaseName = time() . '_' . basename($image["name"]);
            $imageUpload = $this -> imageUpload -> UploadImage($image,$imageDatabaseName,"../public/uploads/product_images/");
            if($imageUpload["status"] === "error"){
                return $imageUpload;
            }
        }

        try {
            $query = $this->db->prepare(
                "INSERT INTO " . $this->table . " (category_id,name, description, base_price,image)
                 VALUES (:category_id,:name,:description,:base_price,:image)"
            );

            $query->bindParam(":category_id", $category);
            $query->bindParam(":name", $name);
            $query->bindParam(":description", $description);
            $query->bindParam(":base_price", $price);
            $query->bindParam(":image",$imageDatabaseName);
            
            if ($query->execute()) {
                return [
                    "status" => "success",
                    "message" => "Product added successfully"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "Failed to add product"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ];
        }
    }

    public function updateProduct($id, $name, $description, $price, $category)
    {
        $id = (int) $id;
        $name = htmlspecialchars(trim($name));
        $description = htmlspecialchars(trim($description));
        $price = floatval($price);
        $category = htmlspecialchars(trim($category));

        if (empty($name) || empty($description) || empty($price) || empty($category)) {
            return [
                "status" => "error",
                "message" => "Fill all the required fields"
            ];
        }

        if (!is_numeric($price) || $price <= 0) {
            return [
                "status" => "error",
                "message" => "Invalid Price"
            ];
        }

        try {
            if (count($verifyProduct = $this->GetOneProduct($id)) > 0) {
                $updateQuery = $this->db->prepare(
                    "UPDATE " . $this->table . "
                     SET category_id = :category_id, name = :name, description = :description, base_price = :base_price WHERE id = :id"
                );
                $updateQuery->bindParam(":category_id", $category);
                $updateQuery->bindParam(":name", $name);
                $updateQuery->bindParam(":description", $description);
                $updateQuery->bindParam(":base_price", $price);
                $updateQuery->bindParam(":id", $id);
                $updateQuery->execute();

                if ($updateQuery->rowCount() > 0) {
                    return [
                        "status" => "success",
                        "message" => "Product updated successfully"
                    ];
                } else {
                    return [
                        "status" => "warning",
                        "message" => "No changes were made"
                    ];
                }
            } else {
                return [
                    "status" => "error",
                    "message" => "Invalid Product"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ];
        }
    }

    public function removeProduct($id)
    {
        $id = (int) $id;

        try {
            $deleteQuery = $this->db->prepare("DELETE FROM " . $this->table . " WHERE id = :id");
            $deleteQuery->bindParam(":id", $id);
            $deleteQuery->execute();

            if ($deleteQuery->rowCount() > 0) {
                return [
                    "status" => "success",
                    "message" => "Product deleted successfully"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "Failed to delete product"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ];
        }
    }

    public function GetAllProducts()
    {
        $products = [];
        try {
            $query = $this->db->prepare(
                "SELECT categories.*, products.* FROM categories
                 INNER JOIN products ON products.category_id = categories.id"
            );
            $query->execute();
            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                    $products[] = [
                        "id" => $row->id,
                        "name" => $row->name,
                        "description" => $row->description,
                        "price" => $row->base_price,
                        "discount" => $row->discount,
                        "image" => $row->image,
                        "category" => $row->category_id,
                        "category_name" => $row->category_name
                    ];
                }
            }

            return $products;
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ];
        }
    }

    public function GetOneProduct($id)
    {
        $id = (int) $id;
        $product = [];
        try {
            $query = $this->db->prepare("SELECT categories.*, products.* FROM categories
                 INNER JOIN products ON products.category_id = categories.id WHERE products.id = :id");
            $query->bindParam(":id", $id);
            $query->execute();
            if ($query->rowCount() > 0) {
                $row = $query->fetch(PDO::FETCH_OBJ);
                $product[] = [
                    "id" => $row->id,
                    "name" => $row->name,
                    "description" => $row->description,
                    "price" => $row->base_price,
                    "category" => $row->category_id,
                    "category_name" => $row->category_name
                ];
            }

            return $product;
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ];
        }
    }
}
