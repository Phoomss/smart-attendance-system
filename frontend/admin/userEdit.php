<?php
session_start();
require_once '../../server/conn.php';
require_once '../../server/user.php';

$database = new Conn();
$db = $database->getConnection();

$id = $_GET['id'] ?? 0;
$userModel = new User($db);
$response = $userModel->getUserInfo($id);
$user = $response['success'] ? $response['data'] : null;

if (!$user) {
    header('Location: userAll.php');
    exit();
}
ob_start();
?>
                <div class="mb-4">
                    <h1 class="h3 fw-bold text-dark">แก้ไขข้อมูลพนักงาน</h1>
                    <p class="text-muted small">อัปเดตข้อมูลส่วนตัวและสิทธิ์การใช้งานของพนักงาน</p>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm p-4">
                            <form id="editUserForm">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                                
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold text-muted">คำนำหน้า</label>
                                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($user['title']) ?>" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold text-muted">ชื่อจริง</label>
                                        <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($user['firstname']) ?>" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold text-muted">นามสกุล</label>
                                        <input type="text" name="surname" class="form-control" value="<?= htmlspecialchars($user['surname']) ?>" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">รหัสพนักงาน</label>
                                        <input type="text" name="employee_code" class="form-control bg-light" value="<?= htmlspecialchars($user['employee_code']) ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">ตำแหน่ง (Role)</label>
                                        <select name="role" class="form-select" disabled>
                                            <option value="employee" <?= $user['role'] == 'employee' ? 'selected' : '' ?>>Employee</option>
                                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">เบอร์โทรศัพท์</label>
                                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">อีเมล</label>
                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                                    </div>

                                    <div class="col-md-12 mt-4 pt-3 border-top">
                                        <h6 class="fw-bold text-dark mb-3">Account Security</h6>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">ชื่อผู้ใช้งาน (Username)</label>
                                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">รหัสผ่านใหม่ (เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</label>
                                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                                    </div>
                                </div>

                                <div class="mt-5 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                                        <i class="fas fa-save me-2"></i> บันทึกข้อมูล
                                    </button>
                                    <a href="userAll.php" class="btn btn-light rounded-pill px-4">ยกเลิก</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
<?php
$content = ob_get_clean();

ob_start();
?>
    <script src="../../public/js/users.js"></script>
<?php
$scripts = ob_get_clean();

require_once '../layouts/core/app.php';
renderLayout('แก้ไขข้อมูลพนักงาน', $content, $scripts);
?>
