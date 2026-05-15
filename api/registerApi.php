<?php
require_once '../server/conn.php';
require_once '../server/auth.php';

session_start();

$database = new Conn();
$db = $database->getConnection();
$auth = new Auth($db);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->employee_code = $_POST['employee_code'] ?? null;
    $auth->title = $_POST['title'] ?? '';
    $auth->firstname = $_POST['firstname'] ?? '';
    $auth->surname = $_POST['surname'] ?? '';
    $auth->username = $_POST['username'] ?? '';
    $auth->email = $_POST['email'] ?? '';
    $auth->password = $_POST['password'] ?? '';

    $result = $auth->register();
    $statusCode = $result['success'] ? 200 : 400;
    
    echo json_encode(array_merge($result, ["status_code" => $statusCode]));
}
