<?php
session_start();
require_once '../../server/conn.php';
require_once '../../server/user.php';

$database = new Conn();
$db = $database->getConnection();

if (!isset($_SESSION['profile']) && !isset($_SESSION['userInfo'])) {
    header('Location: ../../index.php');
    exit();
}

$userEmail = ($_SESSION['profile']->email ?? '') ?: ($_SESSION['userInfo']['email'] ?? '');

if (!$userEmail) die("No email found in session data.");

$stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $userEmail]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) die("User not found.");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>โปรไฟล์ - แอดมิน</title>
    <?php include_once '../layouts/config/libary.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include_once '../layouts/sidenav.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include_once '../layouts/navbar.php'; ?>
                <div class="container mt-5">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white"><h5>ข้อมูลส่วนตัว</h5></div>
                        <div class="card-body">
                            <form id="profileForm">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($userData['id']); ?>">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label>รหัสพนักงาน</label>
                                        <input type="text" class="form-control" name="employee_code" value="<?= htmlspecialchars($userData['employee_code'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label>คำนำหน้า</label>
                                        <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($userData['title'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label>ชื่อ</label>
                                        <input type="text" class="form-control" name="firstname" value="<?= htmlspecialchars($userData['firstname'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label>นามสกุล</label>
                                        <input type="text" class="form-control" name="surname" value="<?= htmlspecialchars($userData['surname'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>อีเมล</label>
                                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($userData['email'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>เบอร์โทร</label>
                                        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($userData['phone'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>ชื่อผู้ใช้งาน</label>
                                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($userData['username'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>รหัสผ่าน (เว้นว่างไว้ถ้าไม่เปลี่ยน)</label>
                                        <input type="password" class="form-control" name="password">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once '../layouts/footer.php'; ?>
        </div>
    </div>
    <?php include_once '../layouts/config/script.php'; ?>
<script>
$(document).ready(function() {
    $("#profileForm").submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "../../api/userApi.php",
            data: {
                action: "update",
                id: $("input[name='id']").val(),
                employee_code: $("input[name='employee_code']").val(),
                title: $("input[name='title']").val(),
                firstname: $("input[name='firstname']").val(),
                surname: $("input[name='surname']").val(),
                username: $("input[name='username']").val(),
                phone: $("input[name='phone']").val(),
                email: $("input[name='email']").val(),
                password: $("input[name='password']").val() || null
            },
            dataType: "json",
            success: function(res) {
                if (res.success) Swal.fire('สำเร็จ!', res.message, 'success').then(() => location.reload());
                else Swal.fire('ผิดพลาด!', res.message, 'error');
            }
        });
    });
});
</script>
</body>
</html>
