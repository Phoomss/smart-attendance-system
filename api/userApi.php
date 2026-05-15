<?php
require_once '../server/conn.php';
require_once '../server/user.php';

session_start();

$database = new Conn();
$db = $database->getConnection();
$user = new User($db);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'getAll':
            echo json_encode($user->getAllUser());
            break;

        case 'getInfo':
            $id = $_POST['id'] ?? 0;
            echo json_encode($user->getUserInfo($id));
            break;

        case 'update':
            $id = $_POST['id'] ?? 0;
            $user->employee_code = trim($_POST['employee_code'] ?? '');
            $user->title = trim($_POST['title'] ?? '');
            $user->firstname = trim($_POST['firstname'] ?? '');
            $user->surname = trim($_POST['surname'] ?? '');
            $user->username = trim($_POST['username'] ?? '');
            $user->phone = trim($_POST['phone'] ?? '');
            $user->email = trim($_POST['email'] ?? '');
            $user->password = $_POST['password'] ?? ''; // No trim on password

            if ($user->update($id)) {
                echo json_encode(["success" => true, "message" => "อัปเดตข้อมูลสำเร็จ", "status_code" => 200]);
            } else {
                echo json_encode(["success" => false, "message" => "อัปเดตไม่สำเร็จ", "status_code" => 500]);
            }
            break;

        case 'delete':
            $id = $_POST['id'] ?? 0;
            if ($user->delete($id)) {
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
