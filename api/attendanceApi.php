<?php
require_once '../server/conn.php';
require_once '../server/attendance.php';

session_start();

// Centralized Connection Management
$database = new Conn();
$db = $database->getConnection();

// Pass connection via Dependency Injection
$attendance = new Attendance($db);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            if (empty($_POST['employee_id']) || empty($_POST['attendance_date']) || empty($_POST['attendance_time'])) {
                echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน", "status_code" => 400]);
                exit();
            }

            $attendance->employee_id = $_POST['employee_id'];
            $attendance->attendance_date = $_POST['attendance_date'];
            $attendance->attendance_time = $_POST['attendance_date'] . ' ' . $_POST['attendance_time'];

            $result = $attendance->create();
            echo json_encode(array_merge($result, ["status_code" => $result['success'] ? 200 : 500]));
            break;

        case 'update':
            if (empty($_POST['id']) || empty($_POST['departure_time'])) {
                echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบถ้วน", "status_code" => 400]);
                exit();
            }

            $attendance->id = $_POST['id'];
            $attendance->departure_time = date('Y-m-d') . ' ' . $_POST['departure_time'];

            if ($attendance->update()) {
                echo json_encode(["success" => true, "message" => "บันทึกเวลาออกงานสำเร็จ", "status_code" => 200]);
            } else {
                echo json_encode(["success" => false, "message" => "ไม่สามารถบันทึกข้อมูลได้", "status_code" => 500]);
            }
            break;

        case 'delete':
            $attendance->id = $_POST['id'] ?? 0;
            if ($attendance->delete()) {
                echo json_encode(["success" => true, "message" => "ลบข้อมูลสำเร็จ", "status_code" => 200]);
            } else {
                echo json_encode(["success" => false, "message" => "ไม่สามารถลบข้อมูลได้", "status_code" => 500]);
            }
            break;

        case 'checkAttendance':
            echo json_encode($attendance->checkAttendance($_POST['employee_id'] ?? 0));
            break;

        case 'checkDeparture':
            echo json_encode($attendance->checkDeparture($_POST['employee_id'] ?? 0));
            break;

        default:
            echo json_encode(["success" => false, "message" => "Action not found", "status_code" => 404]);
            break;
    }
}
