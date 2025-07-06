<?php
class ProductVariant extends Products
{
    private $table = "product_variants";

    public function __construct($db)
    {
        parent::__construct($db);
    }

    public function addVariant($productID, $size, $color, $price, $stock, $status, $image, $productionCost)
    {
        $productID = (int) $productID;
        $price = floatval($price);
        $productionCost = floatval($productionCost);
        $size = htmlspecialchars(trim($size));
        $color = htmlspecialchars(trim($color));
        $stock = (int) $stock;
        $status = htmlspecialchars(trim($status));

        if (empty($productID) || empty($price) || empty($size) || empty($color) || empty($stock) || empty($status) || empty($productionCost)) {
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

        if (isset($image) && !empty($image["tmp_name"])) {
            $imageDatabaseName = time() . '_' . basename($image["name"]);
            $imageUpload = $this->imageUpload->UploadImage(
                $image,
                $imageDatabaseName,
                "../public/uploads/variant_images/"
            );
            if ($imageUpload["status"] === "error") {
                return $imageUpload;
            }
        } else {
            return [
                "status" => "error",
                "message" => "Provide an image"
            ];
            $imageDatabaseName = null;
        }

        try {
            $query = $this->db->prepare(
                "INSERT INTO " . $this->table . " 
                 (product_id, size, color, price, production_cost, stock, image, status) 
                 VALUES (:product_id, :size, :color, :price, :production_cost, :stock, :image, :status)"
            );
            $query->bindParam(":product_id", $productID);
            $query->bindParam(":size", $size);
            $query->bindParam(":color", $color);
            $query->bindParam(":price", $price);
            $query->bindParam(":production_cost", $productionCost);
            $query->bindParam(":stock", $stock);
            $query->bindParam(":image", $imageDatabaseName);
            $query->bindParam(":status", $status);

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

    public function updateVariant($variantID, $size, $color, $price, $stock, $status, $image, $productionCost)
    {
        $variantID = (int) $variantID;
        $price = floatval($price);
        $productionCost = floatval($productionCost);
        $size = htmlspecialchars(trim($size));
        $color = htmlspecialchars(trim($color));
        $stock = (int) $stock;
        $status = htmlspecialchars(trim($status));

        if (empty($price) || empty($size) || empty($color) || empty($stock) || empty($status) || empty($productionCost)) {
            return [
                "status" => "error",
                "message" => "Fill all the required fields"
            ];
        }

        $findVariant = $this->getOneVariant($variantID);
        if (count($findVariant) === 0) {
            return [
                "status" => "error",
                "message" => "Invalid Variant"
            ];
        }

        
        $imageDatabaseName = null;

        if (isset($image) && !empty($image["tmp_name"])) {
            $imageDatabaseName = time() . '_' . basename($image["name"]);

            foreach ($findVariant as $data) {
                $PreviousImage = $data["image"];
            }

            $updateImage = $this->imageUpload->UpdateImage(
                $PreviousImage,
                $image,
                $imageDatabaseName,
                "../public/uploads/variant_images/"
            );
            if ($updateImage["status"] === "error") {
                return $updateImage;
            }
        } else {
            foreach ($findVariant as $data) {
                $imageDatabaseName = $data["image"];
            }
        }

        if((int) $stock <= 0){
            $status = "Out of Stock";
        }

        try {
            $updateQuery = $this->db->prepare("
                UPDATE {$this->table}
                SET size = :size,
                    color = :color,
                    price = :price,
                    production_cost = :production_cost,
                    stock = :stock,
                    image = :image,
                    status = :status
                WHERE id = :id
            ");

            $updateQuery->bindParam(":size", $size);
            $updateQuery->bindParam(":color", $color);
            $updateQuery->bindParam(":price", $price);
            $updateQuery->bindParam(":production_cost", $productionCost);
            $updateQuery->bindParam(":stock", $stock);
            $updateQuery->bindParam(":image", $imageDatabaseName);
            $updateQuery->bindParam(":status", $status);
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
            $query = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE product_id = :product_id");
            $query->bindParam(":product_id", $productID);
            $query->execute();
            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                    $variants[] = [
                        "id" => $row->id,
                        "product_id" => $row->product_id,
                        "size" => $row->size,
                        "color" => $row->color,
                        "price" => $row->price,
                        "production_cost" => $row->production_cost,
                        "stock" => $row->stock,
                        "image" => $row->image,
                        "status" => $row->status
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
                    "size" => $row->size,
                    "color" => $row->color,
                    "price" => $row->price,
                    "production_cost" => $row->production_cost,
                    "stock" => $row->stock,
                    "image" => $row->image,
                    "status" => $row->status
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
?>
