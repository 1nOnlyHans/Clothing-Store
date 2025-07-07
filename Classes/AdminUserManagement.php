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
        $users = [];
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
        }
        return $users;
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
                    "id" => $row->id,
                    "firstname" => $row->firstname,
                    "lastname" => $row->lastname,
                    "email" => $row->email,
                    "role" => $row->role,
                    "profile_img" => $row->profile_img,
                    "created_at" => $row->created_at,
                    "status" => $row->status
                ];
            }
        }
        return $users;
    }

    public function addUser($firstname, $lastname, $email, $role, $status)
    {
        $firstname = htmlspecialchars(trim($firstname));
        $lastname = htmlspecialchars(trim($lastname));
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        $password = "default";
        $role = htmlspecialchars(trim($role));
        $status = htmlspecialchars(trim($status));

        if (empty($firstname) || empty($lastname) || empty($email) || empty($role) || empty($status)) {
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
            $query = $this->db->prepare(
                "INSERT INTO {$this->table} (firstname, lastname, email, password, role, status)
            VALUES (:firstname, :lastname, :email, :password, :role, :status)"
            );

            $query->bindParam(":firstname", $firstname);
            $query->bindParam(":lastname", $lastname);
            $query->bindParam(":email", $email);
            $query->bindParam(":password", $hashedPassword);
            $query->bindParam(":role", $role);
            $query->bindParam(":status", $status);

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


    public function updateUser($id, $firstname, $lastname, $email, $role, $status)
    {
        try {
            $findUser = $this->getUserById($id);
            $role = "";

            foreach($findUser as $data){
                $role = $data["role"];
            }

            if (count($findUser) === 0) {
                return [
                    "status" => "error",
                    "message" => "Invalid User"
                ];
            }

            if ($role === "Admin" && $_SESSION["current_user"]->role === "Admin") {
                return [
                    "status" => "error",
                    "message" => "Permission denied: Cannot update another admin."
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

    public function deleteUser($id)
    {
        try {
            $deleteQuery = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $deleteQuery->bindParam(":id", $id);
            $deleteQuery->execute();

            if ($deleteQuery->rowCount() > 0) {
                return [
                    "status" => "success",
                    "message" => "User deleted successfully"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "Failed to delete user or user not found"
                ];
            }
        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ];
        }
    }

    public function changePassword($userId, $currentPassword, $newPassword, $confirmPassword)
    {
        // 1. Validate new password & confirm match
        if (empty($newPassword) || empty($confirmPassword)) {
            return [
                "status" => "error",
                "message" => "Please fill in all password fields."
            ];
        }

        if ($newPassword !== $confirmPassword) {
            return [
                "status" => "error",
                "message" => "New password and confirmation do not match."
            ];
        }

        if (strlen($newPassword) < 6) {
            return [
                "status" => "error",
                "message" => "Password must be at least 6 characters long."
            ];
        }

        // 2. Fetch the current hashed password
        $query = $this->db->prepare("SELECT password FROM {$this->table} WHERE id = :id");
        $query->bindParam(":id", $userId);
        $query->execute();

        if ($query->rowCount() == 0) {
            return [
                "status" => "error",
                "message" => "User not found."
            ];
        }

        $row = $query->fetch(PDO::FETCH_ASSOC);
        $hashedPassword = $row["password"];

        // 3. Verify current password
        if (!password_verify($currentPassword, $hashedPassword)) {
            return [
                "status" => "error",
                "message" => "Current password is incorrect."
            ];
        }

        // 4. Hash and update to new password
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $updateQuery = $this->db->prepare("UPDATE {$this->table} SET password = :password WHERE id = :id");
        $updateQuery->bindParam(":password", $newHashedPassword);
        $updateQuery->bindParam(":id", $userId);
        $updateQuery->execute();

        if ($updateQuery->rowCount() > 0) {
            return [
                "status" => "success",
                "message" => "Password updated successfully."
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "No changes made."
            ];
        }
    }
}
