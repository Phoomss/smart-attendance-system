<?php
session_start();
require_once '../../server/conn.php';
require_once '../../server/user.php';

$database = new Conn();
$db = $database->getConnection();
$userModel = new User($db);
$response = $userModel->getAllUser();
$users = ($response['success']) ? $response['data'] : [];
ob_start();
?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 fw-bold text-dark mb-0">จัดการพนักงาน</h1>
                        <p class="text-muted small">รายชื่อและข้อมูลพื้นฐานของพนักงานทั้งหมดในระบบ</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="px-4 py-3">พนักงาน</th>
                                        <th class="py-3">รหัสพนักงาน</th>
                                        <th class="py-3">ติดต่อ</th>
                                        <th class="py-3">ตำแหน่ง</th>
                                        <th class="px-4 py-3 text-end">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($users)) : ?>
                                        <?php foreach ($users as $user) : ?>
                                            <tr>
                                                <td class="px-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark"><?= htmlspecialchars(($user['title'] ?? '') . $user['firstname'] . ' ' . $user['surname']) ?></div>
                                                            <div class="text-muted small">@<?= htmlspecialchars($user['username']) ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-secondary-subtle text-secondary px-3"><?= htmlspecialchars($user['employee_code'] ?? '-') ?></span></td>
                                                <td>
                                                    <div class="small"><i class="fas fa-envelope me-1 text-muted"></i> <?= htmlspecialchars($user['email']) ?></div>
                                                    <div class="small"><i class="fas fa-phone me-1 text-muted"></i> <?= htmlspecialchars($user['phone'] ?? '-') ?></div>
                                                </td>
                                                <td><span class="badge bg-info-subtle text-info"><?= htmlspecialchars($user['role'] ?? 'พนักงาน') ?></span></td>
                                                <td class="px-4 text-end">
                                                    <a href="userEdit.php?id=<?= urlencode($user['id']) ?>" class="btn btn-light btn-sm rounded-pill px-3 me-1">
                                                        <i class="fas fa-edit me-1"></i> แก้ไข
                                                    </a>
                                                    <button data-id="<?= $user['id']; ?>" class="btn btn-light text-danger btn-sm rounded-pill px-3 deleteBtn">
                                                        <i class="fas fa-trash-alt me-1"></i> ลบ
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted">ไม่มีข้อมูลพนักงานในระบบ</td></tr>
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
    <script src="../../public/js/users.js"></script>
<?php
$scripts = ob_get_clean();

require_once '../layouts/core/app.php';
renderLayout('จัดการพนักงาน', $content, $scripts);
?>
