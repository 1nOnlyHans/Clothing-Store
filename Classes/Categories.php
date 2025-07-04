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
            $verifyCategory = $this->db->prepare("SELECT * FROM {$this->table} WHERE category_name = :category_name");
            $verifyCategory->bindParam(":category_name", $category_name);
            $verifyCategory->execute();
            if ($verifyCategory->rowCount() > 0) {
                return [
                    "status" => "error",
                    "message" => "Category is already added"
                ];
            }

            $query = $this->db->prepare("INSERT INTO {$this->table} (category_name, category_description) VALUES (:category_name, :category_description)");
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

    public function EditCategory($categoryID, $category_name, $category_description)
    {
        $categoryID = (int) $categoryID;
        $category_name = htmlspecialchars(trim($category_name));
        $category_description = htmlspecialchars(trim($category_description));

        if (empty($category_name) || empty($category_description)) {
            return [
                "status" => "error",
                "message" => "Fill all the required fields"
            ];
        }

        try {
            
            if (count($this->GetOneCategory($categoryID)) === 0) {
                return [
                    "status" => "error",
                    "message" => "Category does not exist"
                ];
            }

            $query = $this->db->prepare(
                "UPDATE {$this->table} 
                 SET category_name = :category_name, category_description = :category_description 
                 WHERE id = :id"
            );
            $query->bindParam(":category_name", $category_name);
            $query->bindParam(":category_description", $category_description);
            $query->bindParam(":id", $categoryID);
            $query->execute();

            if ($query->rowCount() > 0) {
                return [
                    "status" => "success",
                    "message" => "Category updated successfully"
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => "No changes were made"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ];
        }
    }

    public function removeCategory($id)
    {
        $id = (int) $id;
        try {
            $deleteQuery = $this->db->prepare("DELETE FROM " . $this->table . " WHERE id = :id");
            $deleteQuery->bindParam(":id", $id);
            $deleteQuery->execute();

            if ($deleteQuery->rowCount() > 0) {
                return [
                    "status" => "success",
                    "message" => "Category deleted successfully"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "Failed to delete category"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ];
        }
    }

    public function GetOneCategory($categoryID)
    {
        $categoryID = (int) $categoryID;
        $category = [];

        try {
            $query = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $query->bindParam(":id", $categoryID);
            $query->execute();

            if ($query->rowCount() > 0) {
                $row = $query->fetch(PDO::FETCH_OBJ);
                $category[] = [
                    "id" => $row->id,
                    "category_name" => $row->category_name,
                    "category_description" => $row->category_description
                ];
            }

            return $category;
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
            $query = $this->db->prepare("SELECT * FROM {$this->table}");
            $query->execute();
            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                    $categories[] = [
                        "category_id" => $row->id,
                        "category_name" => $row->category_name,
                        "category_description" => $row->category_description
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
