<?php
class Attendance
{
    private $conn;
    private $table_name = "attendances";

    // Configuration
    private $work_start_time = "08:30:00"; // Define standard start time

    public $id;
    public $employee_id;
    public $attendance_date;
    public $attendance_time;
    public $departure_time;
    public $status;
    public $latitude;
    public $longitude;

    /**
     * Dependency Injection for Database Connection
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Create a new attendance record
     * Automatically calculates 'on_time' or 'late' status
     */
    public function create()
    {
        // Validation
        if (empty($this->employee_id) || empty($this->attendance_date) || empty($this->attendance_time)) {
            return [
                "success" => false,
                "message" => "ข้อมูลไม่ครบถ้วนสำหรับการบันทึก"
            ];
        }

        // Automatic Status Calculation if not provided
        if (empty($this->status)) {
            $check_time = date('H:i:s', strtotime($this->attendance_time));
            $this->status = ($check_time <= $this->work_start_time) ? 'on_time' : 'late';
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  (employee_id, attendance_date, attendance_time, status, latitude, longitude) 
                  VALUES (:employee_id, :attendance_date, :attendance_time, :status, :latitude, :longitude)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':employee_id', $this->employee_id, PDO::PARAM_INT);
        $stmt->bindParam(':attendance_date', $this->attendance_date, PDO::PARAM_STR);
        $stmt->bindParam(':attendance_time', $this->attendance_time, PDO::PARAM_STR);
        $stmt->bindParam(':status', $this->status, PDO::PARAM_STR);
        $stmt->bindParam(':latitude', $this->latitude);
        $stmt->bindParam(':longitude', $this->longitude);

        try {
            if ($stmt->execute()) {
                return [
                    "success" => true,
                    "message" => "บันทึกเวลาเข้างานสำเร็จ (สถานะ: " . ($this->status == 'on_time' ? 'ปกติ' : 'สาย') . ")"
                ];
            }
            return ["success" => false, "message" => "ไม่สามารถบันทึกข้อมูลได้"];
        } catch (PDOException $e) {
            error_log("Attendance Create Error: " . $e->getMessage());
            return ["success" => false, "message" => "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage()];
        }
    }

    public function readInfo($id)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . "
                      WHERE employee_id = :id
                      ORDER BY attendance_date DESC
                      LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            error_log("Attendance readInfo Error: " . $e->getMessage());
            return false;
        }
    }    
    
    public function checkAttendance($employee_id) {
        try {
            $attendance_date = date('Y-m-d');
            $query = "SELECT id FROM " . $this->table_name . " WHERE employee_id = :employee_id AND attendance_date = :attendance_date LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
            $stmt->bindParam(':attendance_date', $attendance_date, PDO::PARAM_STR);
            $stmt->execute();
    
            return ["exists" => ($stmt->rowCount() > 0)];
        } catch (PDOException $e) {
            error_log("Attendance checkAttendance Error: " . $e->getMessage());
            return ["error" => "Database error"];
        }
    } 
    
    public function checkDeparture($employee_id) {
        try {
            $today = date('Y-m-d');
            $query = "SELECT id FROM " . $this->table_name . " 
                      WHERE employee_id = :employee_id 
                      AND attendance_date = :today 
                      AND departure_time IS NOT NULL LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
            $stmt->bindParam(':today', $today, PDO::PARAM_STR);
            $stmt->execute();
    
            return ["exists" => ($stmt->rowCount() > 0)];
        } catch (PDOException $e) {
            error_log("Attendance checkDeparture Error: " . $e->getMessage());
            return ["error" => "Database error"];
        }
    }    

    public function update()
    {
        if (empty($this->departure_time) || empty($this->id)) {
            return false;
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET departure_time = :departure_time,
                      latitude = IFNULL(latitude, :latitude),
                      longitude = IFNULL(longitude, :longitude)
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':departure_time', $this->departure_time, PDO::PARAM_STR);
        $stmt->bindParam(':latitude', $this->latitude);
        $stmt->bindParam(':longitude', $this->longitude);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete()
    {
        if (empty($this->id)) return false;

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
