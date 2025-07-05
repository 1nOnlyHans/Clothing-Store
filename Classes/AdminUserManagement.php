<?php

class UserManagement
{
    private $table = "users";
    private $db;
    private $role;
    private $imageUpload;

    public function __construct($db)
    {
        $this->db = $db;
        $this->imageUpload = new Image();
    }

    public function getUserById($id)
    {
        $user = [];
        $query = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        if ($query->rowCount() > 0) {
            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                $users[] = [
                    "firstname" => $row->firstname,
                    "lastname" => $row->lastname,
                    "email" => $row->email,
                    "role" => $row->role,
                    "profile_img" => $row->profile_img,
                    "created_at" => $row->created_at,
                    "status" => $row->status
                ];
            }
            return $user;
        }
    }

    public function getUserByRole($role)
    {
        $users = [];
        $query = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE role = :role");
        $query->bindParam(":role", $role);
        $query->execute();
        if ($query->rowCount() > 0) {
            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                $users[] = [
                    "firstname" => $row->firstname,
                    "lastname" => $row->lastname,
                    "email" => $row->email,
                    "role" => $row->role,
                    "profile_img" => $row->profile_img,
                    "created_at" => $row->created_at,
                    "status" => $row->status
                ];
            }
            return $users;
        }
    }

    public function addUser($firstname, $lastname,$email,$role)
    {
        $firstname = htmlspecialchars(trim($firstname));
        $lastname = htmlspecialchars(trim($lastname));
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        $password = "default";
        $role = htmlspecialchars(trim($role));

        if (empty($firstname) || empty($lastname) || empty($email) || empty($role)) {
            return ["status" => "error", "message" => "Fill all the required fields"];
        }

        if (strlen($firstname) <= 1 || strlen($lastname) <= 1) {
            return ["status" => "error", "message" => "Enter a valid name"];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["status" => "error", "message" => "Invalid email address"];
        }

        $verify_email = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $verify_email->bindParam(":email", $email);
        $verify_email->execute();
        if ($verify_email->rowCount() > 0) {
            return ["status" => "error", "message" => "Email is already registered"];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $query = $this->db->prepare("INSERT INTO " . $this -> table . " (firstname,lastname,email,password,role) VALUES (:firstname,:lastname,:email,:password,:role)");
            $query->bindParam(":firstname", $firstname);
            $query->bindParam(":lastname", $lastname);
            $query->bindParam(":email", $email);
            $query->bindParam(":password", $hashedPassword);
            $query->bindParam(":role", $role);
            $query->execute();

            if ($query->rowCount() > 0) {
                return ["status" => "success", "message" => "User registered successfully"];
            } else {
                return ["status" => "error", "message" => "Failed to register"];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Database error: " . $e->getMessage()];
        }
    }

    public function updateUser($id, $firstname, $lastname, $email, $role, $image)
    {
        try {
            $findUser = $this->getUserById($id);
            if (!$findUser) {
                return [
                    "status" => "error",
                    "message" => "Invalid User"
                ];
            }

            $imageDatabaseName = $findUser["profile_img"];

            if (isset($image) && !empty($image["tmp_name"])) {
                $imageDatabaseName = time() . '_' . basename($image["name"]);
                $UpdateImage = $this->imageUpload->UpdateImage($imageDatabaseName, $image, $imageDatabaseName, "../public/uploads/user_images/");
                if ($UpdateImage["status"] === "error") {
                    return $UpdateImage;
                }
            }

            if ($findUser["role"] === "Admin" && $_SESSION["current_user"]->role === "Admin") {
                return [
                    "status" => "error",
                    "message" => "Permission denied: Cannot update another admin."
                ];
            }

            $query = $this->db->prepare("UPDATE " . $this->table . " SET firstname = :firstname, lastname = :lastname, email = :email, role =: role, profile_img = :profile_img WHERE id = :id");
            $query->bindParam(":firstname", $firstname);
            $query->bindParam(":lastname", $lastname);
            $query->bindParam(":email", $email);
            $query->bindParam(":role", $role);
            $query->bindParam(":profile_img", $imageDatabaseName);
            $query->bindParam(":id", $id);
            $query->execute();

            if ($query->rowCount() > 0) {
                return [
                    "status" => "success",
                    "message" => "User Updated"
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

    public function deleteUser($id)
    {
        try {
            $deleteQuery = $this->db->prepare("UPDATE " . $this->table . " SET status = :status");
            $deleteQuery->bindParam(":id", $id);
            $deleteQuery->bindParam(":status", "Inactive");
            $deleteQuery->execute();

            if ($deleteQuery->rowCount() > 0) {
                return [
                    "status" => "success",
                    "message" => "User deleted successfully"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "Failed to delete User"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ];
        }
    }
}
