<?php
class Cart
{
    private $db;
    private $userID;
    private $productID;
    private $variantID;
    private $quantity;
    private $price;

    public function __construct($db, $userID, $productID, $variantID, $quantity)
    {
        $this->db = $db;
        $this->userID = $userID;
        $this->productID = $productID;
        $this->variantID = $variantID;
        $this->quantity = $quantity;
    }

    public function addToCart()
    {
        $productPrice = 0;

        // Find variant
        $findVariant = $this->db->prepare("SELECT * FROM product_variants WHERE id = :variant_id");
        $findVariant->bindParam(":variant_id", $this->variantID);
        $findVariant->execute();

        if ($findVariant->rowCount() === 0) {
            return [
                "status" => "error",
                "message" => "Invalid Product"
            ];
        }

        $row = $findVariant->fetch(PDO::FETCH_OBJ);
        $status = $row->status;

        if ($status === "Unavailable") {
            return [
                "status" => "error",
                "message" => "This item is currently unavailable"
            ];
        } elseif ($status === "Out of Stock") {
            return [
                "status" => "error",
                "message" => "This item is currently out of stock"
            ];
        }

        $productPrice = floatval($row->price);
        $productQuantity = (int) $row->stock;
        $totalPrice = $productPrice * (int) $this->quantity;

        // Check if item already exists in cart
        $ifExists = $this->db->prepare("SELECT quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id AND variant_id = :variant_id");
        $ifExists->bindParam(":user_id", $this->userID);
        $ifExists->bindParam(":product_id", $this->productID);
        $ifExists->bindParam(":variant_id", $this->variantID);
        $ifExists->execute();

        if ($ifExists->rowCount() > 0) {
            // If exists, increase quantity
            $existing = $ifExists->fetch(PDO::FETCH_OBJ);
            $newQuantity = $existing->quantity + $this->quantity;
            $newTotalPrice = $productPrice * $newQuantity;

            if ($newQuantity > $productQuantity) {
                return [
                    "status" => "error",
                    "message" => "Quantity exceeded item stocks"
                ];
            }

            $increaseQuantity = $this->db->prepare("
                UPDATE cart 
                SET quantity = :quantity, price = :price 
                WHERE user_id = :user_id AND product_id = :product_id AND variant_id = :variant_id
            ");
            $increaseQuantity->bindParam(":quantity", $newQuantity);
            $increaseQuantity->bindParam(":price", $newTotalPrice);
            $increaseQuantity->bindParam(":user_id", $this->userID);
            $increaseQuantity->bindParam(":product_id", $this->productID);
            $increaseQuantity->bindParam(":variant_id", $this->variantID);

            if ($increaseQuantity->execute()) {
                return [
                    "status" => "success",
                    "message" => "Cart updated"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "Failed to update quantity"
                ];
            }
        } else {
            // Add new item
            if ($this->quantity > $productQuantity) {
                return [
                    "status" => "error",
                    "message" => "Quantity exceeded item stocks"
                ];
            }

            $addItem = $this->db->prepare("
                INSERT INTO cart (user_id, product_id, variant_id, quantity, price)
                VALUES (:user_id, :product_id, :variant_id, :quantity, :price)
            ");
            $addItem->bindParam(":user_id", $this->userID);
            $addItem->bindParam(":product_id", $this->productID);
            $addItem->bindParam(":variant_id", $this->variantID);
            $addItem->bindParam(":quantity", $this->quantity);
            $addItem->bindParam(":price", $totalPrice);

            if ($addItem->execute()) {
                return [
                    "status" => "success",
                    "message" => "Item added to cart"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "Failed to add item"
                ];
            }
        }
    }

    public function editQuantity($cartID)
    {

        $productPrice = 0;
        $newQuantity = $this->quantity;

        $findVariant = $this->db->prepare("SELECT price,stock FROM  product_variants WHERE id = :variant_id");
        $findVariant->bindParam(":variant_id", $this->variantID);
        $findVariant->execute();

        if ($findVariant->rowCount() === 0) {
            return [
                "status" => "error",
                "message" => "Invalid Product"
            ];
        }

        $row = $findVariant->fetch(PDO::FETCH_OBJ);
        $productPrice = floatval($row->price);
        $productQuantity = (int) $row->stock;
        $newTotalPrice = $productPrice * (int) $newQuantity;

        if ($newQuantity > $productQuantity) {
            return [
                "status" => "error",
                "message" => "Quantity exceeded item stocks"
            ];
        }

        $query = $this->db->prepare("UPDATE cart 
                SET quantity = :quantity, price = :price 
                WHERE id = :id");
        $query->bindParam(":quantity", $newQuantity);
        $query->bindParam(":price", $newTotalPrice);
        $query->bindParam(":id", $cartID);
        $query->execute();

        if ($query->rowCount() > 0) {
            return [
                "status" => "success",
                "message" => "Card updated"
            ];
        } else {
            return [
                "status" => "error",
                "message" => "Nothing were changed"
            ];
        }
    }

    public function getAllCartItems()
    {
        $cartItems = [];
        $query = $this->db->prepare("SELECT 
        products.name AS product_name,
        product_variants.id AS variant_id,
        product_variants.size,
        product_variants.color,
        product_variants.price AS variant_price,
        product_variants.image,
        product_variants.stock AS item_stock,
        cart.id,
        cart.quantity,
        cart.price AS total_price
        FROM 
        products
        INNER JOIN 
        product_variants ON product_variants.product_id = products.id
        INNER JOIN 
        cart ON cart.variant_id = product_variants.id
        WHERE 
        cart.user_id = :user_id
        AND product_variants.status NOT IN ('Unavailable', 'Out of Stock')");
        $query->bindParam(":user_id", $this->userID);
        $query->execute();
        if ($query->rowCount() > 0) {
            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                $cartItems[] = [
                    "variant_id" => $row->variant_id,
                    "product_name" => $row->product_name,
                    "size" => $row->size,
                    "color" => $row->color,
                    "item_price" => $row->variant_price,
                    "item_stocks" => $row->item_stock,
                    "image" => $row->image,
                    "id" => $row->id,
                    "quantity" => $row->quantity,
                    "total_price" => $row->total_price
                ];
            }
        }
        return $cartItems;
    }

    public function removeCartItem($cartID){
        $query = $this -> db -> prepare("DELETE FROM cart WHERE id = :id");
        $query -> bindParam(":id",$cartID);
        $query -> execute();
        if($query -> rowCount() > 0){
            return [
                "status" => "success",
                "message" => "Deleted from cart"
            ];
        }
        else{
            return [
                "status" => "error",
                "message" => "Failed to delete from cart"
            ];
        }
    }
}
