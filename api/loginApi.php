<?php
require_once '../server/conn.php';
require_once '../server/auth.php';

header('Content-Type: application/json');
session_start();

$database = new Conn();
$db = $database->getConnection();

$auth = new Auth($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? ''; // Do not trim passwords

    if (!empty($identifier) && !empty($password)) {
        $user_data = $auth->login($identifier, $password);

        if ($user_data['success']) {
            session_regenerate_id(true);

            $_SESSION['userInfo'] = [
                'id' => $user_data['data']['id'],
                'username' => $user_data['data']['username'],
                'email' => $user_data['data']['email'],
                'role' => $user_data['data']['role'],
            ];

            echo json_encode([
                "success" => true,
                "message" => "เข้าสู่ระบบสำเร็จ",
                "status_code" => 200,
                "role" => $user_data['data']['role'],
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => $user_data['message'],
                "status_code" => 401,
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "กรุณากรอกข้อมูลให้ครบถ้วน",
            "status_code" => 400,
        ]);
    }
}

