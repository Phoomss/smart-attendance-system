<?php
require_once '../server/conn.php';

class AttendanceDailyApi {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function getAttendanceData() {
        $query = "SELECT u.firstname, DATE_FORMAT(a.attendance_time, '%Y-%m') AS month, COUNT(*) AS count
                  FROM attendances a INNER JOIN users u ON a.employee_id = u.id
                  GROUP BY u.id, month ORDER BY u.firstname, month";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[$row['firstname']][$row['month']] = $row['count'];
        }
        return $data;
    }
}

$db = (new Conn())->getConnection();
echo json_encode((new AttendanceDailyApi($db))->getAttendanceData());
?>
