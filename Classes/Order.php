<?php

class Order
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . rand(10000, 99999);
    }

    public function placeOrder($userID, $totalAmount, $payment_method, $shippingAddress)
    {
        try {
            $this->db->beginTransaction();

            // Fetch cart items
            $fetchCartItems = $this->db->prepare("
            SELECT 
                cart.id AS cart_id,
                cart.quantity,
                cart.price AS total_price,
                product_variants.id AS variant_id,
                product_variants.product_id,
                product_variants.price AS unit_price,
                product_variants.stock as product_stock,
                product_variants.status
            FROM cart
            INNER JOIN product_variants ON cart.variant_id = product_variants.id
            WHERE cart.user_id = :user_id
        ");
            $fetchCartItems->bindParam(":user_id", $userID);
            $fetchCartItems->execute();

            if ($fetchCartItems->rowCount() === 0) {
                return ["status" => "error", "message" => "Cart is empty"];
            }

            $cartItems = $fetchCartItems->fetchAll(PDO::FETCH_OBJ);

            // Check stocks first
            foreach ($cartItems as $item) {
                if ($item->quantity > $item->product_stock) {
                    $this->db->rollBack();
                    return [
                        "status" => "error",
                        "message" => "Product ID {$item->product_id} exceeds available stock ({$item->product_stock})."
                    ];
                }
            }

            // Determine payment status
            $paymentStatus = ($payment_method === "Gcash") ? "Paid" : "Pending";

            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Insert into orders
            $insertOrder = $this->db->prepare("
            INSERT INTO orders 
                (user_id, order_number, total_amount, payment_method, payment_status, shipping_address)
            VALUES
                (:user_id, :order_number, :total_amount, :payment_method, :payment_status, :shipping_address)
        ");
            $insertOrder->bindParam(":user_id", $userID);
            $insertOrder->bindParam(":order_number", $orderNumber);
            $insertOrder->bindParam(":total_amount", $totalAmount);
            $insertOrder->bindParam(":payment_method", $payment_method);
            $insertOrder->bindParam(":payment_status", $paymentStatus);
            $insertOrder->bindParam(":shipping_address", $shippingAddress);

            if (!$insertOrder->execute()) {
                $this->db->rollBack();
                return ["status" => "error", "message" => "Failed to create order"];
            }

            $orderID = $this->db->lastInsertId();

            $insertOrderItem = $this->db->prepare("
            INSERT INTO order_items 
                (order_id, product_id, variant_id, quantity, unit_price, total_price)
            VALUES 
                (:order_id, :product_id, :variant_id, :quantity, :unit_price, :total_price)
        ");

            foreach ($cartItems as $item) {
                $insertOrderItem->bindParam(":order_id", $orderID);
                $insertOrderItem->bindParam(":product_id", $item->product_id);
                $insertOrderItem->bindParam(":variant_id", $item->variant_id);
                $insertOrderItem->bindParam(":quantity", $item->quantity);
                $insertOrderItem->bindParam(":unit_price", $item->unit_price);
                $insertOrderItem->bindParam(":total_price", $item->total_price);

                $insertOrderItem->execute();

            }

            $clearCart = $this->db->prepare("DELETE FROM cart WHERE user_id = :user_id");
            $clearCart->bindParam(":user_id", $userID);
            $clearCart->execute();

            $this->db->commit();

            return [
                "status" => "success",
                "message" => "Order placed successfully!",
                "order_number" => $orderNumber
            ];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return ["status" => "error", "message" => "Error: " . $e->getMessage()];
        }
    }

    public function getOrders($userID)
    {
        try {
            $query = $this->db->prepare("
            SELECT * FROM orders 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC
        ");
            $query->bindParam(":user_id", $userID);
            $query->execute();

            if ($query->rowCount() > 0) {
                $orders = $query->fetchAll(PDO::FETCH_OBJ);
                return [
                    "status" => "success",
                    "orders" => $orders
                ];
            } else {
                return [
                    "status" => "empty",
                    "message" => "No orders found."
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Error: " . $e->getMessage()
            ];
        }
    }


}
