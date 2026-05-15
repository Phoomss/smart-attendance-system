<?php
require_once '../server/conn.php';
class LeaveCountApi
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getLeaveData()
    {
        $sql = "SELECT 
                    DATE_FORMAT(leave_date, '%Y-%m') AS leave_month,
                    COUNT(CASE WHEN leave_type = 'ลาป่วย' THEN 1 END) AS sick_leave_count,
                    COUNT(CASE WHEN leave_type = 'ลากิจ' THEN 1 END) AS personal_leave_count
                FROM leaves
                GROUP BY leave_month
                ORDER BY leave_month DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function prepareChartData()
    {
        $leaveData = $this->getLeaveData();
        $res = ['months' => [], 'sickLeaveCounts' => [], 'personalLeaveCounts' => []];

        foreach ($leaveData as $data) {
            $res['months'][] = $data['leave_month'];
            $res['sickLeaveCounts'][] = $data['sick_leave_count'];
            $res['personalLeaveCounts'][] = $data['personal_leave_count'];
        }
        return $res;
    }
}

$database = new Conn();
$db = $database->getConnection();
$leaveCount = new LeaveCountApi($db);
echo json_encode($leaveCount->prepareChartData());  
?>
