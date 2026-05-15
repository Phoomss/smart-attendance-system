<?php
session_start();
require_once '../../server/conn.php';
require_once '../../server/detailWork.php';

$database = new Conn();
$db = $database->getConnection();

if (!isset($_SESSION['profile']) && !isset($_SESSION['userInfo'])) {
    header('Location: ../../index.php');
    exit();
}

$userEmail = ($_SESSION['profile']->email ?? '') ?: ($_SESSION['userInfo']['email'] ?? '');
$stmt = $db->prepare("SELECT id, title, firstname, surname FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $userEmail]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) die("User not found.");

$employee_id = $userData['id'];
$detailWork = new DetailWork($db);
$info = $detailWork->readInfo($employee_id);

$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startLimit = ($currentPage - 1) * $itemsPerPage;

$combinedData = array_merge($info['attendance'], $info['leave']);

// Filtering logic
$searchDate = $_GET['searchDate'] ?? '';
$leaveType = $_GET['leave_type'] ?? '';

if ($searchDate || $leaveType) {
    $combinedData = array_filter($combinedData, function ($row) use ($searchDate, $leaveType) {
        $rowDate = $row['leave_date'] ?? date('Y-m-d', strtotime($row['created_at']));
        $matchDate = !$searchDate || ($rowDate == $searchDate);
        $matchType = !$leaveType || ($row['leave_type'] ?? '') == $leaveType;
        return $matchDate && $matchType;
    });
}

$totalItems = count($combinedData);
$totalPages = ceil($totalItems / $itemsPerPage);
$pageData = array_slice($combinedData, $startLimit, $itemsPerPage);
ob_start();
?>
                <div class="mb-4">
                    <h1 class="h3 fw-bold text-dark">ประวัติการเข้างานและลางาน</h1>
                    <p class="text-muted small">ตรวจสอบและค้นหาประวัติการทำงานของคุณย้อนหลัง</p>
                </div>

                <div class="row g-4">
                    <div class="col-lg-3">
                        <?php include_once 'reportCard.php' ?>
                        
                        <div class="card border-0 shadow-sm mt-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark mb-3">ตัวกรองข้อมูล</h6>
                                <form method="GET" class="space-y-3">
                                    <div class="mb-3">
                                        <label class="form-label small text-muted">วันที่</label>
                                        <input type="date" class="form-control" name="searchDate" value="<?= htmlspecialchars($searchDate) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small text-muted">ประเภทการลา</label>
                                        <select class="form-select text-sm" name="leave_type">
                                            <option value="">-- ทั้งหมด --</option>
                                            <option value="ลาป่วย" <?= $leaveType == 'ลาป่วย' ? 'selected' : '' ?>>ลาป่วย</option>
                                            <option value="ลากิจ" <?= $leaveType == 'ลากิจ' ? 'selected' : '' ?>>ลากิจ</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                        <i class="fas fa-search me-2"></i> ค้นหา
                                    </button>
                                    <?php if($searchDate || $leaveType): ?>
                                        <a href="reportAttendance.php" class="btn btn-light w-100 rounded-pill mt-2 small">ล้างค่า</a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-9">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th class="px-4 py-3">ประเภท</th>
                                                <th class="py-3">วัน/เวลา</th>
                                                <th class="py-3">เวลาออก</th>
                                                <th class="px-4 py-3">สถานะ/เหตุผล</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($pageData): ?>
                                                <?php foreach ($pageData as $row): ?>
                                                    <?php $isLeave = isset($row['leave_type']); ?>
                                                    <tr>
                                                        <td class="px-4">
                                                            <?php if($isLeave): ?>
                                                                <span class="badge bg-info-subtle text-info rounded-pill px-3">ลา (<?= $row['leave_type'] ?>)</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3">เข้างาน</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="fw-bold text-dark"><?= htmlspecialchars($isLeave ? $row['leave_date'] : $row['attendance_date']) ?></div>
                                                            <?php if(!$isLeave): ?>
                                                                <div class="small text-muted"><?= htmlspecialchars(date('H:i', strtotime($row['attendance_time']))) ?> น.</div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?= (!$isLeave && $row['departure_time']) ? htmlspecialchars(date('H:i', strtotime($row['departure_time']))) . ' น.' : '-' ?>
                                                        </td>
                                                        <td class="px-4">
                                                            <?php if($isLeave): ?>
                                                                <div class="small text-dark fw-bold"><?= htmlspecialchars($row['reason'] ?? '-') ?></div>
                                                                <div class="small text-muted italic">(<?= htmlspecialchars($row['status']) ?>)</div>
                                                            <?php else: ?>
                                                                <?php if ($row['status'] == 'on_time'): ?>
                                                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">ปกติ</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3">สาย</span>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="4" class="text-center py-5 text-muted">ไม่พบข้อมูลการทำงานในระบบ</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <?php if ($totalPages > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                        <li class="page-item <?= $p == $currentPage ? 'active' : '' ?>">
                                            <a class="page-link border-0 shadow-sm mx-1 rounded-circle" href="?page=<?= $p ?>&searchDate=<?= urlencode($searchDate) ?>&leave_type=<?= urlencode($leaveType) ?>"><?= $p ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
<?php
$content = ob_get_clean();

ob_start();
?>
<?php
$scripts = ob_get_clean();

require_once '../layouts/core/app.php';
renderLayout('ประวัติการทำงาน', $content, $scripts);
?>
