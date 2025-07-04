<?php

class ProductVariant extends Products
{
    private $table = "product_variants";

    public function __construct($db)
    {
        parent::__construct($db);
    }

    public function addVariant($productID, $size, $color, $price, $stock)
    {
        $productID = (int) $productID;
        $price = floatval($price);
        $size = htmlspecialchars(trim($size));
        $color = htmlspecialchars(trim($color));
        $stock = (int) $stock;

        if (empty($productID) || empty($price) || empty($size) || empty($color) || empty($stock)) {
            return [
                "status" => "error",
                "message" => "Fill all the required fields"
            ];
        }

        $validateVariant = $this->db->prepare(
            "SELECT * FROM " . $this->table . " 
            WHERE product_id = :product_id AND size = :size AND color = :color"
        );
        $validateVariant->bindParam(":product_id", $productID);
        $validateVariant->bindParam(":size", $size);
        $validateVariant->bindParam(":color", $color);
        $validateVariant->execute();

        if ($validateVariant->rowCount() > 0) {
            return [
                "status" => "warning",
                "message" => "This variant is already added"
            ];
        }

        try {
            $query = $this->db->prepare(
                "INSERT INTO " . $this->table . " 
             (product_id, size, color, price, stock) 
             VALUES (:product_id, :size, :color, :price, :stock)"
            );
            $query->bindParam(":product_id", $productID);
            $query->bindParam(":size", $size);
            $query->bindParam(":color", $color);
            $query->bindParam(":price", $price);
            $query->bindParam(":stock", $stock);

            if ($query->execute()) {
                return [
                    "status" => "success",
                    "message" => "Variant is added"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "Failed to add variant"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ];
        }
    }

    public function updateVariant($variantID, $price, $stock)
    {
        $variantID = (int) $variantID;

        $findVariant = $this->GetOneVariant($variantID);
        if (count($findVariant) === 0) {
            return [
                "status" => "error",
                "message" => "Invalid Variant"
            ];
        }

        try {
            $updateQuery = $this->db->prepare("UPDATE " . $this->table . " SET price = :price, stock = :stock WHERE id = :id");
            $updateQuery->bindParam(":price", $price);
            $updateQuery->bindParam(":stock", $stock);
            $updateQuery->bindParam(":id", $variantID);
            $updateQuery->execute();

            if ($updateQuery->rowCount() > 0) {
                return [
                    "status" => "success",
                    "message" => "Variant updated successfully"
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

    public function removeVariant($variantID)
    {
        $variantID = (int) $variantID;

        try {
            $deleteQuery = $this->db->prepare("DELETE FROM " . $this->table . " WHERE id = :id");
            $deleteQuery->bindParam(":id", $variantID);
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

    public function GetAllVariants($productID)
    {
        $variants = [];

        try {
            $query = $this->db->prepare("SELECT * FROM " . $this->table ." WHERE product_id = :product_id");
            $query -> bindParam(":product_id",$productID);
            $query->execute();
            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                    $variants[] = [
                        "id" => $row->id,
                        "product_id" => $row->product_id,
                        "size" => $row->size,
                        "color" => $row->color,
                        "price" => $row->price,
                        "stock" => $row->stock,
                    ];
                }
            }
            return $variants;
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ];
        }
    }

    public function GetOneVariant($variantID)
    {
        $variantID = (int) $variantID;
        $product = [];
        try {
            $query = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE id = :id");
            $query->bindParam(":id", $variantID);
            $query->execute();
            if ($query->rowCount() > 0) {
                $row = $query->fetch(PDO::FETCH_OBJ);

                $product[] = [
                    "id" => $row->id,
                    "product_id" => $row->product_id,
                    "price" => $row->price,
                    "size" => $row->size,
                    "stock" => $row->stock,
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
