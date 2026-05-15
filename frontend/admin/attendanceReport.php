<?php
session_start();
require_once '../../server/conn.php';
require_once '../../server/user.php';
require_once '../../server/attendance.php';
require_once '../../server/detailWork.php';

$database = new Conn();
$db = $database->getConnection();
$userModel = new User($db);
$response = $userModel->getAllUser();
$users = $response['success'] ? $response['data'] : [];
ob_start();
?>
                <div class="mb-4 d-flex justify-content-between align-items-end flex-wrap gap-3">
                    <div>
                        <h1 class="h3 fw-bold text-dark">รายงานการเข้างาน</h1>
                        <p class="text-muted small mb-0">ตรวจสอบข้อมูลการเข้า-ออกงาน และสถิติการลาของพนักงานรายบุคคล</p>
                    </div>
                    
                    <!-- Global Export Section -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-2 px-3 d-flex align-items-center gap-2">
                            <span class="small fw-bold text-muted me-2"><i class="fas fa-file-export me-1"></i> ส่งออกข้อมูลรวม</span>
                            <select id="exportMonth" class="form-select form-select-sm" style="width: auto;">
                                <?php
                                $months = ['01'=>'ม.ค.','02'=>'ก.พ.','03'=>'มี.ค.','04'=>'เม.ย.','05'=>'พ.ค.','06'=>'มิ.ย.','07'=>'ก.ค.','08'=>'ส.ค.','09'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.'];
                                $currentMonth = date('m');
                                foreach ($months as $m => $label) {
                                    $selected = ($m === $currentMonth) ? 'selected' : '';
                                    echo "<option value=\"$m\" $selected>$label</option>";
                                }
                                ?>
                            </select>
                            <select id="exportYear" class="form-select form-select-sm" style="width: auto;">
                                <?php
                                $currentYear = date('Y');
                                for ($y = $currentYear; $y >= $currentYear - 2; $y--) {
                                    echo "<option value=\"$y\">$y</option>";
                                }
                                ?>
                            </select>
                            <button onclick="exportCsv()" class="btn btn-success btn-sm fw-bold">
                                <i class="fas fa-file-excel me-1"></i> ดาวน์โหลด CSV
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <div class="col-xl-4 col-md-6">
                                <div class="card border-0 shadow-sm h-100 transition-hover">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px;">
                                                <i class="fas fa-user-circle fa-2x"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1">
                                                    <?= htmlspecialchars(($user['title'] ?? '') . $user['firstname'] . ' ' . $user['surname']) ?>
                                                </h6>
                                                <span class="badge bg-secondary-subtle text-secondary small fw-normal"><?= htmlspecialchars($user['employee_code'] ?? '-') ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-2 mb-4">
                                            <div class="d-flex justify-content-between small text-muted mb-2">
                                                <span><i class="fas fa-envelope me-2"></i> อีเมล</span>
                                                <span class="text-dark"><?= htmlspecialchars($user['email']) ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between small text-muted">
                                                <span><i class="fas fa-tag me-2"></i> ตำแหน่ง</span>
                                                <span class="text-dark"><?= htmlspecialchars($user['role'] ?? 'พนักงาน') ?></span>
                                            </div>
                                        </div>

                                        <a href="attendanceMasterData.php?id=<?= urlencode($user['id']) ?>" class="btn btn-primary-subtle text-primary w-100 rounded-pill fw-bold">
                                            <i class="fas fa-file-invoice me-2"></i> ดูประวัติการทำงาน
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <div class="text-muted">ไม่พบข้อมูลพนักงาน</div>
                        </div>
                    <?php endif; ?>
                </div>
<?php
$content = ob_get_clean();

ob_start();
?>
    <style>
        .transition-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .transition-hover:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important; }
    </style>
    <script>
        function exportCsv() {
            const month = document.getElementById('exportMonth').value;
            const year = document.getElementById('exportYear').value;
            window.location.href = `../../server/exportCsv.php?month=${month}&year=${year}`;
        }
    </script>
<?php
$scripts = ob_get_clean();

require_once '../layouts/core/app.php';
renderLayout('รายงานเข้าออกงาน', $content, $scripts);
?>
