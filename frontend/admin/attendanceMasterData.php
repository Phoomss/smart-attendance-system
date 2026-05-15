<?php
session_start();
require_once '../../server/conn.php';
require_once '../../server/user.php';
require_once '../../server/attendance.php';
require_once '../../server/detailWork.php';

$database = new Conn();
$db = $database->getConnection();

$id = $_GET['id'] ?? 0;
if ($id <= 0) {
    header('Location: attendanceReport.php');
    exit();
}

$userModel = new User($db);
$userData = $userModel->getUserInfo($id)['data'] ?? null;
$detailWork = new DetailWork($db);
$info = $detailWork->readInfo($id);

if (!$userData) {
    header('Location: attendanceReport.php');
    exit();
}
ob_start();
?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 fw-bold text-dark mb-1">ประวัติการเข้างาน</h1>
                        <p class="text-muted small">ข้อมูลของ: <?= htmlspecialchars(($userData['title'] ?? '') . $userData['firstname'] . ' ' . $userData['surname']) ?></p>
                    </div>
                    <a href="attendanceReport.php" class="btn btn-light rounded-pill px-3"><i class="fas fa-arrow-left me-2"></i> กลับ</a>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="px-4 py-3">#</th>
                                        <th class="py-3">ประเภท</th>
                                        <th class="py-3">วัน/เวลา</th>
                                        <th class="py-3">เวลาออก</th>
                                        <th class="px-4 py-3 text-center">สถานะ/เหตุผล</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($info): ?>
                                        <?php $idx = 1; ?>
                                        <?php foreach ($info['attendance'] as $row): ?>
                                            <tr>
                                                <td class="px-4"><?= $idx++ ?></td>
                                                <td><span class="badge bg-primary-subtle text-primary rounded-pill px-3">เข้างาน</span></td>
                                                <td><div class="fw-bold text-dark"><?= htmlspecialchars($row['attendance_date']) ?></div><div class="small text-muted"><?= (!empty($row['attendance_time'])) ? htmlspecialchars(date('H:i', strtotime($row['attendance_time']))) : '--:--' ?> น.</div></td>
                                                <td><?= $row['departure_time'] ? htmlspecialchars(date('H:i', strtotime($row['departure_time']))) . ' น.' : '<span class="text-muted small italic">ยังไม่บันทึก</span>' ?></td>
                                                <td class="text-center px-4">
                                                    <?php if ($row['status'] == 'on_time'): ?>
                                                        <span class="badge bg-success-subtle text-success rounded-pill px-3">ปกติ</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-3">สาย</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php foreach ($info['leave'] as $row): ?>
                                            <tr class="bg-light-subtle">
                                                <td class="px-4"><?= $idx++ ?></td>
                                                <td><span class="badge bg-info-subtle text-info rounded-pill px-3">ลา (<?= $row['leave_type'] ?>)</span></td>
                                                <td><div class="fw-bold text-dark"><?= htmlspecialchars($row['leave_date']) ?></div><div class="small text-muted">ถึง <?= htmlspecialchars($row['leave_end_date'] ?? $row['leave_date']) ?></div></td>
                                                <td class="text-muted">-</td>
                                                <td class="px-4">
                                                    <div class="small text-dark fw-bold"><?= htmlspecialchars($row['reason'] ?? '-') ?></div>
                                                    <div class="small text-muted italic">(<?= htmlspecialchars($row['status']) ?>)</div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted">ไม่พบข้อมูลการทำงาน</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
<?php
$content = ob_get_clean();

ob_start();
?>
<?php
$scripts = ob_get_clean();

require_once '../layouts/core/app.php';
renderLayout('ประวัติพนักงาน', $content, $scripts);
?>
