<?php
require_once '../server/conn.php';
require_once '../server/auth.php';

session_start();

$database = new Conn();
$db = $database->getConnection();
$auth = new Auth($db);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->employee_code = trim($_POST['employee_code'] ?? '');
    if (empty($auth->employee_code)) $auth->employee_code = null;
    
    $auth->title = trim($_POST['title'] ?? '');
    $auth->firstname = trim($_POST['firstname'] ?? '');
    $auth->surname = trim($_POST['surname'] ?? '');
    $auth->username = trim($_POST['username'] ?? '');
    $auth->email = trim($_POST['email'] ?? '');
    $auth->password = $_POST['password'] ?? ''; // Do not trim passwords

    $result = $auth->register();
    $statusCode = $result['success'] ? 200 : 400;
    
    echo json_encode(array_merge($result, ["status_code" => $statusCode]));
}
