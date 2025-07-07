<?php

class AdminOrderManagement
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllOrders()
    {
        try {
            $query = $this->db->prepare("
            SELECT orders.id as order_id, orders.order_number, orders.total_amount, orders.payment_method, orders.payment_status, orders.order_status, orders.shipping_address, orders.created_at as order_date, users.firstname,users.lastname,users.email,users.profile_img FROM orders INNER JOIN users ON orders.user_id = users.id ORDER BY orders.created_at DESC
            ");
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

    public function getOrderDetails($orderID)
    {
        $fetchorderItems = $this->db->prepare("SELECT 
            orders.id AS order_id,
            orders.user_id AS user_id,
            users.firstname AS user_firstname,
            users.lastname AS user_lastname,
            users.email AS user_email,
            order_status,
            orders.order_number,
            orders.shipping_address,
            orders.total_amount,
            orders.payment_method,
            orders.payment_status,
            orders.gcash_number,
            orders.created_at AS order_date,
            
            order_items.id AS order_item_id,
            order_items.quantity AS item_quantity,
            order_items.unit_price AS unit_price,
            order_items.total_price AS total_unit_price,
            
            product_variants.id AS variant_id,
            products.name AS product_name,
            product_variants.size AS variant_size,
            product_variants.color AS variant_color,
            product_variants.stock AS variant_stock,
            product_variants.image,
            product_variants.status AS product_status

            FROM orders
            INNER JOIN users ON orders.user_id = users.id
            INNER JOIN order_items ON orders.id = order_items.order_id
            INNER JOIN product_variants ON order_items.variant_id = product_variants.id
            INNER JOIN products ON order_items.product_id = products.id
            WHERE orders.id = :order_id");

        $fetchorderItems->bindParam(":order_id", $orderID);
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

    public function toShip($orderID)
    {
        $this->db->beginTransaction();

        $checkOrderStatus = $this -> db -> prepare("SELECT order_status FROM orders WHERE id = :id");
        $checkOrderStatus -> bindParam(":id",$orderID);
        $checkOrderStatus -> execute();
        if($checkOrderStatus -> rowCount() > 0){
            $status = $checkOrderStatus -> fetch(PDO::FETCH_OBJ);
            if($status -> order_status === "Delivered"){
                return [
                    "status" => "error",
                    "message" => "This order is already delivered"
                ];
            }
            else if($status -> order_status === "To Ship"){
                return [
                    "status" => "error",
                    "message" => "This order is already being shipped"
                ];
            }
            else if ($status -> order_status === "Cancelled"){
                return [
                    "status" => "error",
                    "message" => "This order is already cancelled by the purchaser"
                ];
            }
        }
        else{
            $this -> db ->rollback();
            return [
                "status" => "error",
                "message" => "Invalid Order"
            ];
        }

        $fetchOrderItems = $this->db->prepare("SELECT order_items.*,
                orders.order_status,
                orders.total_amount AS total_amount,
                orders.id as order_id,
                orders.user_id as user_id,
                product_variants.production_cost,
                product_variants.stock as product_stocks 
                FROM order_items 
                INNER JOIN orders ON orders.id = order_items.order_id 
                INNER JOIN product_variants ON product_variants.id = order_items.variant_id 
                WHERE order_id = :order_id");

        $fetchOrderItems->bindParam(":order_id", $orderID);
        $fetchOrderItems->execute();

        if ($fetchOrderItems->rowCount() <= 0) {
            $this->db->rollBack();
            return [
                "status" => "error",
                "message" => "Order items are empty"
            ];
        }

        $orderItems = $fetchOrderItems->fetchAll(PDO::FETCH_OBJ);

        $reduceStocks = $this->db->prepare("UPDATE product_variants SET stock = :stock WHERE id = :id");

        foreach ($orderItems as $item) {
            $newStock = 0;
            $newStock = ($item->product_stocks - $item->quantity);

            if ($item->quantity > $item->product_stocks) {
                $this->db->rollback();
                return [
                    "status" => "error",
                    "message" => "Not enough stocks"
                ];
            } else {
                $reduceStocks->bindParam(":stock", $newStock);
                $reduceStocks->bindParam(":id", $item->variant_id);
                $reduceStocks->execute();

                if ($reduceStocks->rowCount() <= 0) {
                    $this->db->rollback();
                    return [
                        "status" => "error",
                        "message" => "Failed to reduct stocks"
                    ];
                }
            }
        }

        $totalItems = 0;
        $totalProductionCost = 0;
        $totalAmount = $orderItems[0]->total_amount;

        foreach ($orderItems as $item) {
            $perItemProductionCost = $item->production_cost * $item->quantity;
            $totalProductionCost += $perItemProductionCost;
            $totalItems += $item->quantity;
        }

        $profit = $totalAmount - $totalProductionCost;

        // Insert into sales table
        $insertSalesData = $this->db->prepare("
        INSERT INTO sales (order_id, total_items, total_amount, total_production_cost,profit) 
        VALUES (:order_id, :total_items, :total_amount, :total_production_cost,:profit)
        ");
        $insertSalesData->bindParam(":order_id", $orderID);
        $insertSalesData->bindParam(":total_items", $totalItems);
        $insertSalesData->bindParam(":total_amount", $totalAmount);
        $insertSalesData->bindParam(":total_production_cost", $totalProductionCost);
        $insertSalesData->bindParam(":profit", $profit);

        if (!$insertSalesData->execute()) {
            $this->db->rollBack();
            return [
                "status" => "error",
                "message" => "Failed to insert sales data"
            ];
        }

        $salesID = $this->db->lastInsertId();

        $insertSalesItem = $this->db->prepare("
        INSERT INTO sales_items 
        (sales_id, product_id, variant_id, quantity, total_amount, total_production_cost)
        VALUES (:sales_id, :product_id, :variant_id, :quantity, :total_amount, :total_production_cost)
    ");

        foreach ($orderItems as $item) {
            $perItemProductionCost = $item->production_cost * $item->quantity;

            $insertSalesItem->bindParam(":sales_id", $salesID);
            $insertSalesItem->bindParam(":product_id", $item->product_id);
            $insertSalesItem->bindParam(":variant_id", $item->variant_id);
            $insertSalesItem->bindParam(":quantity", $item->quantity);
            $insertSalesItem->bindParam(":total_amount", $item->total_price);
            $insertSalesItem->bindParam(":total_production_cost", $perItemProductionCost);

            if (!$insertSalesItem->execute()) {
                $this->db->rollBack();
                return [
                    "status" => "error",
                    "message" => "Failed to add items to sales_items"
                ];
            }
        }

        // $removeOrderItems = $this->db->prepare("DELETE FROM order_items WHERE order_id = :order_id");
        // $removeOrderItems->bindParam(":order_id", $orderID);
        // $removeOrderItems->execute();

        // if ($removeOrderItems->rowCount() <= 0) {
        //     $this->db->rollBack();
        //     return [
        //         "status" => "error",
        //         "message" => "Failed to remove order items"
        //     ];
        // }

        $updateOrderStatus = $this->db->prepare("UPDATE orders SET order_status = :order_status WHERE id = :id");
        $newStatus = "To Ship";
        $updateOrderStatus->bindParam(":order_status", $newStatus);
        $updateOrderStatus->bindParam(":id", $orderID);
        $updateOrderStatus->execute();

        if ($updateOrderStatus->rowCount() > 0) {
            $user_id = $orderItems[0]->user_id;
            $order_id = $orderItems[0]->order_id;
            $message = "Your order is on the way";

            $sendDeliverMessage = $this->db->prepare("INSERT INTO deliver_message (user_id,order_id,message) VALUES (:user_id,:order_id,:message)");
            $sendDeliverMessage->bindParam(":user_id", $user_id);
            $sendDeliverMessage->bindParam(":order_id", $order_id);
            $sendDeliverMessage->bindParam(":message", $message);
            if ($sendDeliverMessage->execute()) {
                $this->db->commit();
                return [
                    "status" => "success",
                    "message" => "Order is ready to ship"
                ];
            } else {
                $this->db->rollBack();
                return [
                    "status" => "error",
                    "message" => "Failed to send message to user"
                ];
            }
        } else {
            $this->db->rollBack();
            return [
                "status" => "error",
                "message" => "Failed to process order"
            ];
        }
    }

    public function getTotalOrderDatas()
    {
        $latestOrders = $this->getAllOrders();
        $processingOrdersQuery = $this->db->prepare("SELECT COUNT(*) as processing_orders,order_status FROM orders WHERE order_status = 'Processing'");
        $toShipOrdersQuery = $this->db->prepare("SELECT COUNT(*) as to_ship_orders,order_status FROM orders WHERE order_status = 'To Ship'");
        $deliveredOrdersQuery = $this->db->prepare("SELECT COUNT(*) as delivered_orders,order_status FROM orders WHERE order_status = 'Delivered'");
        $queries = [$processingOrdersQuery, $toShipOrdersQuery, $deliveredOrdersQuery];
        foreach ($queries as $query) {
            $query->execute();
        }

        $processingOrders = $queries[0]->fetchAll(PDO::FETCH_OBJ);
        $toShipOrders = $queries[1]->fetchAll(PDO::FETCH_OBJ);
        $deliveredOrders = $queries[2]->fetchAll(PDO::FETCH_OBJ);
        
        return [
            "processing_orders" => $processingOrders,
            "to_ship_orders" => $toShipOrders,
            "deliveredOrders" => $deliveredOrders,
            "latest_orders" => $latestOrders
        ];
    }
}
