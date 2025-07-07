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

    public function getOrderDetails($orderID, $userID)
    {
        $fetchorderItems = $this->db->prepare("SELECT orders.id AS order_id,orders.user_id as user_id,order_status,orders.order_number, orders.shipping_address, orders.total_amount, orders.payment_status,orders.payment_method, orders.created_at AS order_date, order_items.id AS order_item_id, order_items.quantity AS item_quantity, order_items.unit_price AS unit_price, order_items.total_price as total_unit_price, product_variants.id AS variant_id,products.name as product_name, product_variants.size AS variant_size, product_variants.color AS variant_color, product_variants.stock AS variant_stock, product_variants.image, product_variants.status as product_status FROM orders INNER JOIN order_items ON orders.id = order_items.order_id INNER JOIN product_variants ON order_items.variant_id = product_variants.id INNER JOIN products ON order_items.product_id = products.id WHERE
        orders.id = :order_id and orders.user_id = :user_id");

        $fetchorderItems->bindParam(":order_id", $orderID);
        $fetchorderItems->bindParam(":user_id", $userID);
        $fetchorderItems->execute();
        if ($fetchorderItems->rowCount() > 0) {
            return $fetchorderItems->fetchAll(PDO::FETCH_OBJ);
        } else {
            return [
                "status" => "error",
                "message" => "Invalid OrderID"
            ];
        }
    }

    public function cancelOrder($orderID, $userID)
    {
        $checkOrder = $this->db->prepare("SELECT payment_method,order_status FROM orders WHERE id = :id AND user_id = :user_id");
        $checkOrder->bindParam(":id", $orderID);
        $checkOrder->bindParam(":user_id", $userID);
        $checkOrder->execute();
        if ($checkOrder->rowCount() > 0) {
            $row = $checkOrder->fetch(PDO::FETCH_OBJ);
            if ($row->order_status === "To Ship" || $row->order_status === "Delivered" || $row->payment_method === "Gcash") {
                return [
                    "status" => "error",
                    "message" => "Can't cancel order"
                ];
            } else {
                $newStatus = "Cancelled";
                $updateStatus = $this->db->prepare("UPDATE orders SET order_status = :order_status WHERE id = :id AND user_id = :user_id");
                $updateStatus->bindParam(":order_status", $newStatus);
                $updateStatus->bindParam(":id", $orderID);
                $updateStatus->bindParam(":user_id", $userID);
                $updateStatus->execute();
                if ($updateStatus->rowCount() > 0) {
                    return [
                        "status" => "success",
                        "message" => "Cancelled order"
                    ];
                } else {
                    return [
                        "status" => "error",
                        "message" => "Failed to cancel order"
                    ];
                }
            }
        } else {
            return [
                "status" => "error",
                "message" => "Invalid Order"
            ];
        }
    }

    public function getDeliverMessages($userID)
    {
        $query = $this->db->prepare("SELECT orders.order_number, deliver_message.* FROM deliver_message INNER JOIN orders ON orders.id = deliver_message.order_id WHERE deliver_message.user_id = :user_id ORDER BY created_at DESC");
        $query->bindParam(":user_id", $userID);
        $query->execute();
        $messages = $query->fetchAll(PDO::FETCH_OBJ);
        return $messages;
    }

    public function receiveOrder($orderID, $userID)
    {
        $verifyOrder = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $verifyOrder->bindParam(":id", $orderID);
        $verifyOrder->execute();
        $orderData = $verifyOrder->fetch(PDO::FETCH_OBJ);
        if ($verifyOrder->rowCount() === 0) {
            return [
                "status" => "error",
                "message" => "Invalid Order"
            ];
        }
        if ($orderData->order_status === "To Ship") {
            $newStatus = "Delivered";
            $updateStatus = $this->db->prepare("UPDATE orders SET order_status = :order_status WHERE id = :id AND user_id = :user_id");
            $updateStatus->bindParam(":order_status", $newStatus);
            $updateStatus->bindParam(":id", $orderID);
            $updateStatus->bindParam(":user_id", $userID);
            $updateStatus->execute();
            if ($updateStatus->rowCount() > 0) {
                return [
                    "status" => "success",
                    "message" => "Order Received"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "Nothing were changed"
                ];
            }
        }
        return [
            "status" => "error",
            "message" => "Failed to update order status"
        ];
    }
}
