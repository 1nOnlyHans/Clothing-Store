<?php

require "Image.php";

class Products
{
    protected $db;
    private $table = "products";
    protected $imageUpload;

    public function __construct($db)
    {
        $this->db = $db;
        $this->imageUpload = new Image($db);
    }

    public function addProduct($name, $description, $category, $image)
    {
        $name = htmlspecialchars(trim($name));
        $description = htmlspecialchars(trim($description));
        $category = htmlspecialchars(trim($category));

        if (empty($name) || empty($description) || empty($category)) {
            return [
                "status" => "error",
                "message" => "Fill all the required fields"
            ];
        }

        if (isset($image) && !empty($image)) {
            $imageDatabaseName = time() . '_' . basename($image["name"]);
            $imageUpload = $this->imageUpload->UploadImage($image, $imageDatabaseName, "../public/uploads/product_images/");
            if ($imageUpload["status"] === "error") {
                return $imageUpload;
            }
        }

        try {
            $query = $this->db->prepare(
                "INSERT INTO " . $this->table . " (category_id,name, description,image)
                 VALUES (:category_id,:name,:description,:image)"
            );

            $query->bindParam(":category_id", $category);
            $query->bindParam(":name", $name);
            $query->bindParam(":description", $description);
            $query->bindParam(":image", $imageDatabaseName);

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

    public function updateProduct($id, $name, $description, $category, $image)
    {
        $id = (int) $id;
        $name = htmlspecialchars(trim($name));
        $description = htmlspecialchars(trim($description));
        $category = htmlspecialchars(trim($category));

        if (empty($name) || empty($description) || empty($category)) {
            return [
                "status" => "error",
                "message" => "Fill all the required fields"
            ];
        }

        $imageDatabaseName = null;

        if (isset($image) && !empty($image["tmp_name"])) {
            $imageDatabaseName = time() . '_' . basename($image["name"]);
            $fetchPreviousImage = $this->GetOneProduct($id);
            foreach ($fetchPreviousImage as $data) {
                $PreviousImage = $data["image"];
            }
            $UpdateImage = $this->imageUpload->UpdateImage($PreviousImage, $image, $imageDatabaseName, "../public/uploads/product_images/");
            if ($UpdateImage["status"] === "error") {
                return $UpdateImage;
            }
        } else {
            $fetchPreviousImage = $this->GetOneProduct($id);
            foreach ($fetchPreviousImage as $data) {
                $imageDatabaseName = $data["image"];
            }
        }

        try {
            if (count($verifyProduct = $this->GetOneProduct($id)) > 0) {
                $updateQuery = $this->db->prepare(
                    "UPDATE " . $this->table . "
                 SET category_id = :category_id, name = :name, description = :description,image = :image WHERE id = :id"
                );
                $updateQuery->bindParam(":category_id", $category);
                $updateQuery->bindParam(":name", $name);
                $updateQuery->bindParam(":description", $description);
                $updateQuery->bindParam(":image", $imageDatabaseName);
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
                "SELECT 
                categories.*, 
                products.*, 
                SUM(product_variants.stock) AS total_stock,
                COUNT(product_variants.id) AS total_variants
                FROM 
                categories
                INNER JOIN 
                products ON categories.id = products.category_id
                LEFT JOIN 
                product_variants ON products.id = product_variants.product_id
                GROUP BY 
                categories.id, 
                products.id
            ");
            $query->execute();
            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                    $products[] = [
                        "id" => $row->id,
                        "name" => $row->name,
                        "description" => $row->description,
                        "image" => $row->image,
                        "category" => $row->category_id,
                        "category_name" => $row->category_name,
                        "total_stock" => $row -> total_stock,
                        "total_variants" => $row -> total_variants
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
                    "image" => $row->image,
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

    // public function getTotalStocks($productID){
    //     $productID = (int) $productID;
    //     $query = $this -> db -> prepare("SELECT SUM(stock) as total_stocks FROM product_variants WHERE product_id = 2");
    //     $query -> bindParam(":product_id",$productID);
    //     $query -> execute();
    //     return $query -> fetchColumn();
    // }
}
