<?php

class Dbh
{
    private $dbhost = "localhost";
    private $dbuser = "root";
    private $dbpass = "";
    private $dbname = "clothing_store_system";

    public function Connect()
    {
        try {
            $conn = new PDO('mysql:host=' . $this->dbhost . ';dbname=' . $this->dbname . ';charset=utf8', $this->dbuser, $this->dbpass);

            $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $conn->setAttribute(PDO::MYSQL_ATTR_DIRECT_QUERY, true);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (PDOException $e) {
            echo json_encode(["status" => "404", "message" => "Database error: " . $e->getMessage()]);
        }
    }
}
