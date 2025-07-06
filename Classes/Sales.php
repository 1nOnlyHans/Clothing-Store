<?php

class AdminSalesManagement
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getTopProducts()
    {
        $query = $this->db->prepare("SELECT 
        p.id AS product_id,
        p.name AS product_name,
        SUM(si.quantity) AS total_units_sold,
        SUM(si.total_amount) AS total_sales
        FROM 
            sales_items si
        INNER JOIN 
            products p ON p.id = si.product_id
        GROUP BY 
            p.id, p.name
        ORDER BY 
            total_units_sold DESC
        LIMIT 10");
        $query->execute();
        if ($query->rowCount() > 0) {
            $topProducts = $query->fetchAll(PDO::FETCH_OBJ);
            return $topProducts;
        } else {
            return [
                "status" => "error",
                "message" => "No top products"
            ];
        }
    }

    public function getTotalItemsSold()
    {
        $query = $this->db->prepare("SELECT SUM(quantity) as total_items_sold FROM sales_items");
        $query->execute();
        $itemSold = $query->fetch(PDO::FETCH_OBJ);
        return $itemSold;
    }

    public function getSalesData()
    {
        $query = $this->db->prepare("SELECT
        sales.created_at as sales_date,
        products.id AS product_id,
        products.name AS product_name,
        product_variants.id AS variant_id,
        product_variants.color,
        product_variants.size,

        SUM(sales_items.quantity) AS total_units_sold,
        SUM(sales_items.total_amount) AS total_revenue,
        SUM(sales_items.total_production_cost) AS total_production_cost,
        (SUM(sales_items.total_amount) - SUM(sales_items.total_production_cost)) AS total_profit,

        product_variants.price AS variant_price_sold

        FROM sales
        INNER JOIN sales_items ON sales.id = sales_items.sales_id
        INNER JOIN products ON products.id = sales_items.product_id
        INNER JOIN product_variants ON product_variants.id = sales_items.variant_id

        GROUP BY products.id, product_variants.id

        ORDER BY total_units_sold DESC");
        $query->execute();
        $salesData = $query->fetchAll(PDO::FETCH_OBJ);
        return $salesData;
    }
}
