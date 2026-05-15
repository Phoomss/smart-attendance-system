<?php
session_start();
require_once '../../server/conn.php';

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
    <title>โปรไฟล์พนักงาน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once 'navbar.php'; ?>
    <main class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white"><h5>แก้ไขข้อมูลส่วนตัว</h5></div>
                    <div class="card-body">
                        <form id="profileForm">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($userData['id']); ?>">
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label class="form-label">คำนำหน้า</label>
                                    <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($userData['title'] ?? ''); ?>">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">ชื่อ</label>
                                    <input type="text" class="form-control" name="firstname" value="<?= htmlspecialchars($userData['firstname'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">นามสกุล</label>
                                    <input type="text" class="form-control" name="surname" value="<?= htmlspecialchars($userData['surname'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">อีเมล</label>
                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($userData['email'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">เบอร์โทร</label>
                                    <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($userData['phone'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อผู้ใช้งาน</label>
                                    <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($userData['username'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">รหัสผ่านใหม่ (เว้นว่างไว้ถ้าไม่เปลี่ยน)</label>
                                    <input type="password" class="form-control" name="password">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">บันทึกการเปลี่ยนแปลง</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

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
                if (res.success) {
                    Swal.fire('สำเร็จ!', res.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('ผิดพลาด!', res.message, 'error');
                }
            }
        });
    });
});
</script>
</body>
</html>
