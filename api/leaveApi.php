<?php
require_once '../server/conn.php';
require_once '../server/leave.php';

session_start();

$database = new Conn();
$db = $database->getConnection();
$leave = new Leave($db);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            $leave->employee_id = $_POST['employee_id'] ?? 0;
            $leave->leave_type = $_POST['leave_type'] ?? '';
            $leave->leave_date = $_POST['leave_date'] ?? '';
            $leave->leave_end_date = $_POST['leave_end_date'] ?? null;
            $leave->reason = $_POST['reason'] ?? '';

            if ($leave->create()) {
                echo json_encode(["success" => true, "message" => "บันทึกการลาสำเร็จ", "status_code" => 200]);
            } else {
                echo json_encode(["success" => false, "message" => "บันทึกไม่สำเร็จ", "status_code" => 500]);
            }
            break;

        case 'update':
            $leave->id = $_POST['id'] ?? 0;
            $leave->employee_id = $_POST['employee_id'] ?? 0;
            $leave->leave_type = $_POST['leave_type'] ?? '';
            $leave->leave_date = $_POST['leave_date'] ?? '';
            $leave->leave_end_date = $_POST['leave_end_date'] ?? null;
            $leave->reason = $_POST['reason'] ?? '';
            $leave->status = $_POST['status'] ?? 'pending';

            if ($leave->update()) {
                echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลสำเร็จ", "status_code" => 200]);
            } else {
                echo json_encode(["success" => false, "message" => "แก้ไขไม่สำเร็จ", "status_code" => 500]);
            }
            break;

        case 'delete':
            $leave->id = $_POST['id'] ?? 0;
            if ($leave->delete()) {
                echo json_encode(["success" => true, "message" => "ลบข้อมูลสำเร็จ", "status_code" => 200]);
            } else {
                echo json_encode(["success" => false, "message" => "ลบไม่สำเร็จ", "status_code" => 500]);
            }
            break;

        default:
            echo json_encode(["success" => false, "message" => "Invalid action", "status_code" => 404]);
            break;
    }
}
