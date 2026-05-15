<?php
class Leave {
    private $conn;
    private $table_name = "leaves";

    public $id;
    public $employee_id;
    public $leave_type;
    public $leave_date;
    public $leave_end_date;
    public $reason;
    public $status;
    public $attachment_path;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (employee_id, leave_type, leave_date, leave_end_date, reason, status, attachment_path) 
                  VALUES (:employee_id, :leave_type, :leave_date, :leave_end_date, :reason, :status, :attachment_path)";
        
        $stmt = $this->conn->prepare($query);

        if (empty($this->status)) $this->status = 'pending';

        $stmt->bindParam(':employee_id', $this->employee_id, PDO::PARAM_INT);
        $stmt->bindParam(':leave_type', $this->leave_type, PDO::PARAM_STR);
        $stmt->bindParam(':leave_date', $this->leave_date, PDO::PARAM_STR);
        $stmt->bindParam(':leave_end_date', $this->leave_end_date, PDO::PARAM_STR);
        $stmt->bindParam(':reason', $this->reason, PDO::PARAM_STR);
        $stmt->bindParam(':status', $this->status, PDO::PARAM_STR);
        $stmt->bindParam(':attachment_path', $this->attachment_path, PDO::PARAM_STR);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Leave Create Error: " . $e->getMessage());
            return false;
        }
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        if (empty($this->id)) return false;

        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function update() {
        if (empty($this->id)) return false;

        $query = "UPDATE " . $this->table_name . " 
                  SET employee_id = :employee_id, leave_type = :leave_type, 
                      leave_date = :leave_date, leave_end_date = :leave_end_date, 
                      reason = :reason, status = :status 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $this->employee_id, PDO::PARAM_INT);
        $stmt->bindParam(':leave_type', $this->leave_type, PDO::PARAM_STR);
        $stmt->bindParam(':leave_date', $this->leave_date, PDO::PARAM_STR);
        $stmt->bindParam(':leave_end_date', $this->leave_end_date, PDO::PARAM_STR);
        $stmt->bindParam(':reason', $this->reason, PDO::PARAM_STR);
        $stmt->bindParam(':status', $this->status, PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete() {
        if (empty($this->id)) return false;
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
