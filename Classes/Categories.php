<?php

class Category
{
    private $db;
    private $table = "categories";
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function AddCategory($category_name, $category_description)
    {
        $category_name = htmlspecialchars(trim($category_name));
        $category_description = htmlspecialchars(trim($category_description));

        if (empty($category_name) || empty($category_description)) {
            return [
                "status" => "error",
                "message" => "Fill all the required fields"
            ];
        }

        try {
            $verifyCategory = $this->db->prepare("SELECT * FROM categories WHERE category_name = :category_name");
            $verifyCategory->bindParam(":category_name", $category_name);
            $verifyCategory->execute();
            if ($verifyCategory->rowCount() > 0) {
                return [
                    "status" => "error",
                    "message" => "Category is already added"
                ];
            }

            $query = $this->db->prepare("INSERT INTO categories (category_name,category_description) VALUES (:category_name,:category_description)");
            $query->bindParam(":category_name", $category_name);
            $query->bindParam(":category_description", $category_description);

            if ($query->execute()) {
                return [
                    "status" => "success",
                    "message" => "Category Added"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "Failed to Add Category"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ];
        }
    }
    
    public function GetAllCategories()
    {
        $categories = [];

        try {
            $query = $this->db->prepare("SELECT * FROM " . $this->table);
            $query->execute();
            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                    $categories[] = [
                        "category_id" => $row->id,
                        "category_name" => $row->category_name,
                        "category_description" => $row->category_description,
                    ];
                }
            }
            return $categories;
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ];
        }
    }
}
