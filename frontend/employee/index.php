<?php
session_start();
require_once '../../server/conn.php';
require_once '../../server/user.php';
require_once '../../server/attendance.php';
require_once '../../server/detailWork.php';

$database = new Conn();
$db = $database->getConnection();

if (!isset($_SESSION['profile']) && !isset($_SESSION['userInfo'])) {
    header('Location: ../../index.php');
    exit();
}

$userEmail = ($_SESSION['profile']->email ?? '') ?: ($_SESSION['userInfo']['email'] ?? '');
$stmt = $db->prepare("SELECT id, title, firstname, surname, name, picture, role, phone, employee_code FROM users WHERE email = :email");
$stmt->execute([':email' => $userEmail]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) die("User not found.");

$detailWork = new DetailWork($db);
$monthlyStats = $detailWork->getEmployeeMonthlyStats($userData['id']);
$leaveQuotas = $detailWork->getLeaveQuotas($userData['id']);

// Check for profile completeness
$isProfileIncomplete = empty($userData['phone']) || empty($userData['employee_code']) || empty($userData['title']);

ob_start();
?>
                <?php if ($isProfileIncomplete): ?>
                <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center" role="alert">
                    <div class="bg-warning-subtle text-warning rounded-circle p-3 me-3">
                        <i class="fas fa-user-edit fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">กรุณากรอกข้อมูลส่วนตัวให้ครบถ้วน</h6>
                        <p class="small mb-2 text-muted">ข้อมูลบางส่วนของคุณยังไม่สมบูรณ์ (เช่น เบอร์โทรศัพท์ หรือคำนำหน้า) เพื่อความถูกต้องของระบบรายงาน</p>
                        <a href="profile.php" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold">ไปที่หน้าโปรไฟล์</a>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Left Column: Profile & Stats -->
                    <div class="col-lg-4 mb-4">
                        <div class="card text-center p-4 mb-4">
                            <div class="card-body">
                                <img src="<?= !empty($userData['picture']) ? $userData['picture'] : '../../public/assets/img/user2.png' ?>" 
                                     class="rounded-circle mb-3 shadow-sm" 
                                     style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #fff;">
                                <h5 class="fw-bold"><?= htmlspecialchars(($userData['title'] ?? '') . $userData['firstname'] . ' ' . $userData['surname']) ?></h5>
                                <p class="text-muted small mb-3"><?= htmlspecialchars($userEmail) ?></p>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 mb-4">พนักงาน</span>
                                <a href="profile.php" class="btn btn-outline-primary btn-sm w-100 rounded-pill">จัดการบัญชี</a>
                            </div>
                        </div>

                        <!-- Leave Quotas -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom pt-4 pb-3">
                                <h6 class="fw-bold mb-0">สิทธิ์การลาคงเหลือ (ปี <?= date('Y') ?>)</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small fw-bold text-muted">ลาป่วย</span>
                                        <span class="small fw-bold"><?= $leaveQuotas['sick']['remaining'] ?> / <?= $leaveQuotas['sick']['limit'] ?> วัน</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?= ($leaveQuotas['sick']['remaining'] / $leaveQuotas['sick']['limit']) * 100 ?>%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small fw-bold text-muted">ลากิจ</span>
                                        <span class="small fw-bold"><?= $leaveQuotas['personal']['remaining'] ?> / <?= $leaveQuotas['personal']['limit'] ?> วัน</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?= ($leaveQuotas['personal']['remaining'] / $leaveQuotas['personal']['limit']) * 100 ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Stats -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom pt-4 pb-3">
                                <h6 class="fw-bold mb-0">สรุปการทำงานเดือนนี้ (<?= date('M') ?>)</h6>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center p-3 border-bottom-0">
                                        <span class="text-muted small"><i class="fas fa-calendar-check text-primary me-2"></i> เข้างานทั้งหมด</span>
                                        <span class="fw-bold"><?= $monthlyStats['total_days'] ?> วัน</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center p-3 border-bottom-0 bg-light">
                                        <span class="text-muted small"><i class="fas fa-clock text-warning me-2"></i> มาสาย</span>
                                        <span class="fw-bold <?= $monthlyStats['late_days'] > 0 ? 'text-warning' : '' ?>"><?= $monthlyStats['late_days'] ?> วัน</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                        <span class="text-muted small"><i class="fas fa-user-md text-danger me-2"></i> ลางาน (อนุมัติแล้ว)</span>
                                        <span class="fw-bold <?= $monthlyStats['leave_days'] > 0 ? 'text-danger' : '' ?>"><?= $monthlyStats['leave_days'] ?> วัน</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Actions -->
                    <div class="col-lg-8">
                        <div class="card p-4">
                            <h6 class="fw-bold mb-4">บันทึกเวลาทำงาน</h6>
                            <div class="row g-3 mb-4">
                                <?php include_once 'attendanceCreate.php' ?>
                                <?php include_once 'attendanceUpdate.php' ?>
                                <?php include_once 'createLeave.php' ?>
                            </div>
                            
                            <h6 class="fw-bold mt-2 mb-4">ประวัติล่าสุด</h6>
                            <?php include_once 'attendanceDetail.php' ?>
                        </div>
                    </div>
                </div>
<?php
$content = ob_get_clean();

ob_start();
?>
<?php if ($isProfileIncomplete): ?>
<script>
    $(document).ready(function() {
        if (!sessionStorage.getItem('profile_reminder_shown')) {
            Swal.fire({
                title: 'โปรไฟล์ยังไม่สมบูรณ์',
                text: 'กรุณากรอกข้อมูลส่วนตัวให้ครบถ้วน เพื่อการใช้งานระบบที่สมบูรณ์',
                icon: 'info',
                confirmButtonText: 'ไปที่หน้าโปรไฟล์',
                showCancelButton: true,
                cancelButtonText: 'ไว้ทีหลัง',
                confirmButtonColor: '#4F46E5',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'profile.php';
                }
            });
            sessionStorage.setItem('profile_reminder_shown', 'true');
        }
    });
</script>
<?php endif; ?>
<?php
$scripts = ob_get_clean();

require_once '../layouts/core/app.php';
renderLayout('Employee Portal', $content, $scripts);
?>