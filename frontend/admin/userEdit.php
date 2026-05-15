<?php
session_start();
require_once '../../server/conn.php';
require_once '../../server/user.php';

$database = new Conn();
$db = $database->getConnection();

$employee_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($employee_id > 0) {
    $userModel = new User($db);
    $response = $userModel->getUserInfo($employee_id);

    if ($response['success']) {
        $userInfo = $response['data'];
    } else {
        $userInfo = [];
        error_log($response['message']);
    }
} else {
    header('Location: userAll.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลพนักงาน</title>
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
                        <div class="card-header bg-primary text-white">
                            <h5 class="m-0">แก้ไขข้อมูลพนักงาน: <?= htmlspecialchars($userInfo['firstname'] ?? ''); ?></h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($userInfo)): ?>
                                <form id="userForm">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($userInfo['id']); ?>">

                                    <fieldset class="border p-3 mb-3">
                                        <legend class="w-auto px-2">ข้อมูลส่วนตัว</legend>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="employee_code">รหัสพนักงาน</label>
                                                    <input type="text" class="form-control" id="employee_code" name="employee_code" value="<?= htmlspecialchars($userInfo['employee_code'] ?? ''); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="title">คำนำหน้า</label>
                                                    <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($userInfo['title'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="firstname">ชื่อ</label>
                                                    <input type="text" class="form-control" id="firstname" name="firstname" value="<?= htmlspecialchars($userInfo['firstname'] ?? ''); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="surname">นามสกุล</label>
                                                    <input type="text" class="form-control" id="surname" name="surname" value="<?= htmlspecialchars($userInfo['surname'] ?? ''); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <fieldset class="border p-3 mb-3">
                                        <legend class="w-auto px-2">ข้อมูลติดต่อ & บัญชี</legend>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="phone">เบอร์โทร</label>
                                                    <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($userInfo['phone'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="email">อีเมล</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($userInfo['email'] ?? ''); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="username">ชื่อผู้ใช้งาน</label>
                                                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($userInfo['username'] ?? ''); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">รหัสผ่าน (เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</label>
                                                    <input type="password" class="form-control" id="password" name="password" placeholder="********">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <button type="submit" class="btn btn-success btn-block">บันทึกการเปลี่ยนแปลง</button>
                                    <a href="userAll.php" class="btn btn-secondary btn-block">ยกเลิก</a>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-danger">ไม่พบข้อมูลพนักงาน</div>
                            <?php endif; ?>
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
    $("#userForm").submit(function(e) {
        e.preventDefault();

        const formData = {
            action: "update",
            id: $("input[name='id']").val(),
            employee_code: $("#employee_code").val(),
            title: $("#title").val(),
            firstname: $("#firstname").val(),
            surname: $("#surname").val(),
            username: $("#username").val(),
            phone: $("#phone").val(),
            email: $("#email").val(),
            password: $("#password").val() || null,
        };

        $.ajax({
            type: "POST",
            url: "../../api/userApi.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'สำเร็จ!',
                        text: response.message,
                        icon: 'success',
                    }).then(() => {
                        window.location.href = 'userAll.php';
                    });
                } else {
                    Swal.fire({
                        title: 'ข้อผิดพลาด!',
                        text: response.message,
                        icon: 'error',
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'ข้อผิดพลาด!',
                    text: 'ไม่สามารถส่งข้อมูลได้',
                    icon: 'error',
                });
            }
        });
    });
});
</script>
</body>
</html>
