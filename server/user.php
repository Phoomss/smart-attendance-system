<?php
class User
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $employee_code;
    public $title;
    public $firstname;
    public $surname;
    public $username;
    public $phone;
    public $email;
    public $password;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (employee_code, title, firstname, surname, username, phone, password) 
                  VALUES (:employee_code, :title, :firstname, :surname, :username, :phone, :password)";

        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(':employee_code', $this->employee_code);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':password', $hashedPassword);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("User Create Error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllUser()
    {
        $query = "SELECT id, employee_code, title, firstname, surname, username, email, phone, role, created_at FROM " . $this->table_name . " WHERE role != 'admin'";
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute();
            return [
                "success" => true,
                "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("User getAllUser Error: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }

    public function getUserInfo($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? ["success" => true, "data" => $result] : ["success" => false, "message" => "User not found"];
        } catch (PDOException $e) {
            error_log("User getUserInfo Error: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }

    public function update($id)
    {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET employee_code = :employee_code, title = :title, firstname = :firstname, 
                          surname = :surname, username = :username, phone = :phone, email = :email";

            if (!empty($this->password)) {
                $query .= ", password = :password";
            }

            $query .= " WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            // Handle potential nulls for unique fields
            $empCode = !empty($this->employee_code) ? $this->employee_code : null;
            $phone = !empty($this->phone) ? $this->phone : null;

            $stmt->bindParam(':employee_code', $empCode);
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':firstname', $this->firstname);
            $stmt->bindParam(':surname', $this->surname);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if (!empty($this->password)) {
                $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt->bindParam(':password', $hashedPassword);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("User Update Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
