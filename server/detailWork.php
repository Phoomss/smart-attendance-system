<?php
class DetailWork
{
    private $conn;
    private $table_attendances = "attendances";
    private $table_users = "users";
    private $table_leaves = "leaves";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function readInfo($employee_id)
    {
        try {
            // Fetch attendance data (Optimized with JOIN and explicit column selection)
            $attendanceQuery = "SELECT a.attendance_date, a.attendance_time, a.created_at, a.departure_time, a.status, a.latitude, a.longitude
                                 FROM " . $this->table_attendances . " a
                                 WHERE a.employee_id = :employee_id
                                 ORDER BY a.attendance_date DESC
                                 LIMIT 10";
            $attendanceStmt = $this->conn->prepare($attendanceQuery);
            $attendanceStmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
            $attendanceStmt->execute();
            $attendanceResult = $attendanceStmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch leave data
            $leaveQuery = "SELECT l.leave_type, l.leave_date, l.leave_end_date, l.reason, l.status, l.attachment_path
                           FROM " . $this->table_leaves . " l
                           WHERE l.employee_id = :employee_id
                           ORDER BY l.leave_date DESC";
            $leaveStmt = $this->conn->prepare($leaveQuery);
            $leaveStmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
            $leaveStmt->execute();
            $leaveResult = $leaveStmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'attendance' => $attendanceResult,
                'leave' => $leaveResult
            ];
        } catch (PDOException $e) {
            error_log("DetailWork readInfo Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get aggregate statistics for an employee in one query
     */
    public function getEmployeeStats($employee_id)
    {
        try {
            $stats = [];
            
            // Count Attendance
            $q1 = "SELECT COUNT(*) FROM " . $this->table_attendances . " WHERE employee_id = :id";
            $s1 = $this->conn->prepare($q1);
            $s1->execute([':id' => $employee_id]);
            $stats['attendance_count'] = $s1->fetchColumn();

            // Count Leaves by Type
            $q2 = "SELECT leave_type, COUNT(*) as count FROM " . $this->table_leaves . " 
                   WHERE employee_id = :id GROUP BY leave_type";
            $s2 = $this->conn->prepare($q2);
            $s2->execute([':id' => $employee_id]);
            $leaveData = $s2->fetchAll(PDO::FETCH_KEY_PAIR);
            
            $stats['sick_leave_count'] = $leaveData['ลาป่วย'] ?? 0;
            $stats['personal_leave_count'] = $leaveData['ลากิจ'] ?? 0;

            return $stats;
        } catch (PDOException $e) {
            error_log("DetailWork getEmployeeStats Error: " . $e->getMessage());
            return false;
        }
    }

    public function getEmployeeMonthlyStats($employee_id)
    {
        try {
            $stats = [];
            $month = date('m');
            $year = date('Y');
            
            $q1 = "SELECT COUNT(*) FROM " . $this->table_attendances . " WHERE employee_id = :id AND MONTH(attendance_date) = :m AND YEAR(attendance_date) = :y";
            $s1 = $this->conn->prepare($q1);
            $s1->execute([':id' => $employee_id, ':m' => $month, ':y' => $year]);
            $stats['total_days'] = $s1->fetchColumn();

            $q2 = "SELECT COUNT(*) FROM " . $this->table_attendances . " WHERE employee_id = :id AND status = 'late' AND MONTH(attendance_date) = :m AND YEAR(attendance_date) = :y";
            $s2 = $this->conn->prepare($q2);
            $s2->execute([':id' => $employee_id, ':m' => $month, ':y' => $year]);
            $stats['late_days'] = $s2->fetchColumn();

            $q3 = "SELECT COUNT(*) FROM " . $this->table_leaves . " WHERE employee_id = :id AND status = 'approved' AND MONTH(leave_date) = :m AND YEAR(leave_date) = :y";
            $s3 = $this->conn->prepare($q3);
            $s3->execute([':id' => $employee_id, ':m' => $month, ':y' => $year]);
            $stats['leave_days'] = $s3->fetchColumn();

            return $stats;
        } catch (PDOException $e) {
            error_log("DetailWork getEmployeeMonthlyStats Error: " . $e->getMessage());
            return ['total_days' => 0, 'late_days' => 0, 'leave_days' => 0];
        }
    }

    public function getLeaveQuotas($employee_id)
    {
        try {
            $year = date('Y');
            
            // 1. Get Limits (Default to 30/6 if table missing or record missing)
            $qLimit = "SELECT sick_limit, personal_limit FROM leave_quotas WHERE employee_id = :id AND year = :year LIMIT 1";
            $sLimit = $this->conn->prepare($qLimit);
            $sLimit->execute([':id' => $employee_id, ':year' => $year]);
            $limits = $sLimit->fetch(PDO::FETCH_ASSOC) ?: ['sick_limit' => 30, 'personal_limit' => 6];

            // 2. Get Approved Counts for the year
            $qUsed = "SELECT 
                        COUNT(CASE WHEN leave_type = 'ลาป่วย' THEN 1 END) as sick_used,
                        COUNT(CASE WHEN leave_type = 'ลากิจ' THEN 1 END) as personal_used
                      FROM leaves 
                      WHERE employee_id = :id AND status = 'approved' AND YEAR(leave_date) = :year";
            $sUsed = $this->conn->prepare($qUsed);
            $sUsed->execute([':id' => $employee_id, ':year' => $year]);
            $used = $sUsed->fetch(PDO::FETCH_ASSOC);

            return [
                'sick' => [
                    'limit' => $limits['sick_limit'],
                    'used' => $used['sick_used'] ?? 0,
                    'remaining' => $limits['sick_limit'] - ($used['sick_used'] ?? 0)
                ],
                'personal' => [
                    'limit' => $limits['personal_limit'],
                    'used' => $used['personal_used'] ?? 0,
                    'remaining' => $limits['personal_limit'] - ($used['personal_used'] ?? 0)
                ]
            ];
        } catch (PDOException $e) {
            // Fallback for missing table
            return [
                'sick' => ['limit' => 30, 'used' => 0, 'remaining' => 30],
                'personal' => ['limit' => 6, 'used' => 0, 'remaining' => 6]
            ];
        }
    }

    public function getDailyStats()
    {
        try {
            $today = date('Y-m-d');
            
            // Attendances today
            $q1 = "SELECT COUNT(*) FROM " . $this->table_attendances . " WHERE attendance_date = :today";
            $s1 = $this->conn->prepare($q1);
            $s1->execute([':today' => $today]);
            $total_attendances = $s1->fetchColumn();

            // Departures today
            $q2 = "SELECT COUNT(*) FROM " . $this->table_attendances . " WHERE attendance_date = :today AND departure_time IS NOT NULL";
            $s2 = $this->conn->prepare($q2);
            $s2->execute([':today' => $today]);
            $total_departures = $s2->fetchColumn();

            // Leaves today
            $q3 = "SELECT 
                    COUNT(CASE WHEN leave_type = 'ลาป่วย' THEN 1 END) AS sick_count,
                    COUNT(CASE WHEN leave_type = 'ลากิจ' THEN 1 END) AS personal_count
                   FROM " . $this->table_leaves . "
                   WHERE leave_date = :today";
            $s3 = $this->conn->prepare($q3);
            $s3->execute([':today' => $today]);
            $leaveStats = $s3->fetch(PDO::FETCH_ASSOC);

            return [
                'attendances' => $total_attendances,
                'departures' => $total_departures,
                'sick_leaves' => $leaveStats['sick_count'] ?? 0,
                'personal_leaves' => $leaveStats['personal_count'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("DetailWork getDailyStats Error: " . $e->getMessage());
            return false;
        }
    }
}
