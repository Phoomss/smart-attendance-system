<?php
require_once '../server/conn.php';

class AttendanceMonthly
{
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function getMonthlyAttendanceData()
    {
        $query = "SELECT DATE_FORMAT(attendance_time, '%Y-%m') AS attendance_month, COUNT(*) AS count
                  FROM attendances GROUP BY attendance_month ORDER BY attendance_month";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[$row['attendance_month']] = $row['count'];
        }
        return $data;
    }
}

$db = (new Conn())->getConnection();
echo json_encode((new AttendanceMonthly($db))->getMonthlyAttendanceData());
?>
