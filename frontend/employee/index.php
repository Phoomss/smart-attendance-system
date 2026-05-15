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
$stmt = $db->prepare("SELECT id, title, firstname, surname, name, picture, role FROM users WHERE email = :email");
$stmt->execute([':email' => $userEmail]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) die("User not found.");

ob_start();
?>
                <div class="row">
                    <!-- Profile Card -->
                    <div class="col-lg-4 mb-4">
                        <div class="card text-center p-4">
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

require_once '../layouts/core/app.php';
renderLayout('Employee Portal', $content);
?>
