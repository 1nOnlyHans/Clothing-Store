<?php
require "Dbh.php";

class AuthUser extends Dbh
{
    private $db;
    private $table = "users";

    private $firstname;
    private $lastname;
    private $email;
    private $password;
    private $role;
    private $profile_img;

    public function __construct()
    {
        $this->db = $this->Connect();
    }

    public function Setter($datas)
    {
        foreach ($datas as $key => $value) {
            $this->$key = $value;
        }
    }

    public function Getter($datas)
    {
        $getData = [];
        foreach ($datas as $key) {
            $getData[$key] = $this->$key;
        }
        return $getData;
    }

    public function validatePassword()
    {
        $pass_regex = '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/';
        if (preg_match($pass_regex, $this->password)) {
            return true;
        }
        return false;
    }

    public function Register()
    {
        // Sanitize
        $this->firstname = htmlspecialchars(trim($this->firstname));
        $this->lastname = htmlspecialchars(trim($this->lastname));
        $this->email = filter_var(trim($this->email), FILTER_SANITIZE_EMAIL);
        $this->password = trim($this->password);
        $this->role = htmlspecialchars(trim($this->role));

        //Validation
        if (empty($this->firstname) || empty($this->lastname) || empty($this->email) || empty($this->password) || empty($this->role)) {
            echo json_encode(["status" => "error", "message" => "Fill all the required fields"]);
            return;
        }
        if (strlen($this->firstname) <= 1 || strlen($this->lastname) <= 1) {
            echo json_encode(["status" => "error", "message" => "Enter a valid name"]);
            return;
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "error", "message" => "Invalid email address"]);
            return;
        }

        // Checking if email is already registered
        $verify_email = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE email = :email");
        $verify_email->bindParam(":email", $this->email);
        $verify_email->execute();
        if ($verify_email->rowCount() > 0) {
            echo json_encode(["status" => "error", "message" => "Email is already registered"]);
            return;
        }

        if (!$this->validatePassword()) {
            echo json_encode(["status" => "error", "message" => "Password is weak"]);
            return;
        } else {
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        }

        //Insertion of Data
        try {
            $query = $this->db->prepare("INSERT INTO " . $this->table . " (firstname,lastname,email,password,role) VALUES (:firstname,:lastname,:email,:password,:role)");
            $query->bindParam(":firstname", $this->firstname);
            $query->bindParam(":lastname", $this->lastname);
            $query->bindParam(":email", $this->email);
            $query->bindParam(":password", $hashedPassword);
            $query->bindParam(":role", $this->role);
            $query->execute();

            if ($query->rowCount() > 0) {
                echo json_encode(["status" => "success", "message" => "Registered Successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to register"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => "404", "message" => "Database error: " . $e->getMessage()]);
        }
    }

    public function Login($email, $password)
    {
        $email = htmlspecialchars(trim($email));
        $password = trim($password);

        if (empty($email) || empty($password)) {
            echo json_encode(["status" => "error", "message" => "Fill all the required fields"]);
            return;
        }

        try {
            $query = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE email = :email");
            $query->bindParam(":email", $email);
            $query->execute();
            if ($query->rowCount() > 0) {
                $row = $query->fetch(PDO::FETCH_OBJ);
                if ($row->status === "Inactive") {
                    echo json_encode(["status" => "error", "message" => "Failed to login account"]);
                } else {
                    if (password_verify($password, $row->password)) {
                        $_SESSION["current_user"] = $row;
                        echo json_encode(["status" => "success", "message" => "Login Successfully", "role" => $_SESSION["current_user"]->role]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
                    }
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => "404", "message" => "Database error: " . $e->getMessage()]);
        }
    }

    public function updateUser($id, $firstname, $lastname, $email, $role, $status)
    {
        try {
            $findUser = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE id = :id");
            $findUser->bindParam(":id", $id);
            $findUser->execute();
            if($findUser -> rowCount() <= 0){
                return [
                    "status" => "error",
                    "message" => "Invalid Account"
                ];
            }

            $query = $this->db->prepare(
                "UPDATE {$this->table} 
            SET firstname = :firstname, lastname = :lastname, email = :email, role = :role, status = :status
            WHERE id = :id"
            );
            $query->bindParam(":firstname", $firstname);
            $query->bindParam(":lastname", $lastname);
            $query->bindParam(":email", $email);
            $query->bindParam(":role", $role);
            $query->bindParam(":status", $status);
            $query->bindParam(":id", $id);
            $query->execute();

            if ($query->rowCount() > 0) {
                return [
                    "status" => "success",
                    "message" => "User updated successfully"
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
                "message" => "Database Error: " . $e->getMessage()
            ];
        }
    }
}
