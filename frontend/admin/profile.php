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
<?php ob_start(); ?>
                <div class="mb-4">
                    <h1 class="h3 fw-bold text-dark">ข้อมูลส่วนตัว</h1>
                    <p class="text-muted small">จัดการข้อมูลส่วนตัวและรหัสผ่านของคุณสำหรับผู้ดูแลระบบ</p>
                </div>

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm text-center p-4">
                            <div class="card-body">
                                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                                    <i class="fas fa-user-shield fa-3x"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars(($userData['title'] ?? '') . $userData['firstname'] . ' ' . $userData['surname']) ?></h5>
                                <p class="text-muted mb-3"><?= htmlspecialchars($userData['email']) ?></p>
                                <div class="d-flex justify-content-center gap-2">
                                    <span class="badge bg-primary text-white rounded-pill px-3">ADMIN</span>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3"><?= htmlspecialchars($userData['employee_code'] ?? '-') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm p-4">
                            <form id="profileForm">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($userData['id']); ?>">
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">รหัสพนักงาน</label>
                                        <input type="text" name="employee_code" class="form-control" value="<?= htmlspecialchars($userData['employee_code'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold text-muted">คำนำหน้า</label>
                                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($userData['title']) ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted">ชื่อจริง</label>
                                        <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($userData['firstname']) ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted">นามสกุล</label>
                                        <input type="text" name="surname" class="form-control" value="<?= htmlspecialchars($userData['surname']) ?>" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">เบอร์โทรศัพท์</label>
                                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($userData['phone']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">อีเมล</label>
                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($userData['email']) ?>" required>
                                    </div>

                                    <div class="col-md-12 mt-4 pt-3 border-top">
                                        <h6 class="fw-bold text-dark mb-3">การเข้าสู่ระบบ</h6>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">ชื่อผู้ใช้งาน</label>
                                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($userData['username']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">รหัสผ่านใหม่ (เว้นว่างไว้ถ้าไม่เปลี่ยน)</label>
                                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                                    </div>
                                </div>

                                <div class="mt-5">
                                    <button type="submit" class="btn btn-primary rounded-pill px-5">
                                        <i class="fas fa-save me-2"></i> บันทึกข้อมูล
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include_once '../layouts/config/script.php' ?>
    <script src="../../public/js/users.js"></script>
</body>
</html>
it" class="btn btn-primary rounded-pill px-5">
                                        <i class="fas fa-save me-2"></i> บันทึกข้อมูล
                                    </button>
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
renderLayout('โปรไฟล์ส่วนตัว', $content, $scripts);
?>
