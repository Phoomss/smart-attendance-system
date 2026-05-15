<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['userInfo']) || $_SESSION['userInfo']['role'] !== 'admin') {
    die('Unauthorized');
}

$database = new Conn();
$db = $database->getConnection();

$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

$filename = "Attendance_Report_{$year}_{$month}.csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
// Add BOM to fix UTF-8 in Excel
fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, ['รหัสพนักงาน', 'ชื่อ-นามสกุล', 'วันที่', 'เวลาเข้า', 'เวลาออก', 'สถานะ', 'ประเภท']);

$query = "
    SELECT 
        u.employee_code,
        CONCAT(IFNULL(u.title, ''), u.firstname, ' ', u.surname) as full_name,
        a.attendance_date as log_date,
        a.attendance_time,
        a.departure_time,
        a.status,
        'เข้างาน' as type
    FROM attendances a
    JOIN users u ON a.employee_id = u.id
    WHERE MONTH(a.attendance_date) = :month AND YEAR(a.attendance_date) = :year

    UNION ALL

    SELECT 
        u.employee_code,
        CONCAT(IFNULL(u.title, ''), u.firstname, ' ', u.surname) as full_name,
        l.leave_date as log_date,
        NULL,
        NULL,
        l.status,
        l.leave_type as type
    FROM leaves l
    JOIN users u ON l.employee_id = u.id
    WHERE MONTH(l.leave_date) = :month AND YEAR(l.leave_date) = :year

    ORDER BY log_date ASC, employee_code ASC
";

$stmt = $db->prepare($query);
$stmt->execute([':month' => $month, ':year' => $year]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $clockIn = !empty($row['attendance_time']) ? date('H:i', strtotime($row['attendance_time'])) : '-';
    $clockOut = !empty($row['departure_time']) ? date('H:i', strtotime($row['departure_time'])) : '-';
    $statusText = $row['status'];
    
    // Translate status
    if ($statusText === 'on_time') $statusText = 'ปกติ';
    if ($statusText === 'late') $statusText = 'สาย';
    if ($statusText === 'pending') $statusText = 'รออนุมัติ';
    if ($statusText === 'approved') $statusText = 'อนุมัติแล้ว';
    if ($statusText === 'rejected') $statusText = 'ไม่อนุมัติ';

    fputcsv($output, [
        $row['employee_code'],
        $row['full_name'],
        $row['log_date'],
        $clockIn,
        $clockOut,
        $statusText,
        $row['type']
    ]);
}

fclose($output);
exit();