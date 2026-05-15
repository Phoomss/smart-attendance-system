<?php
// Note: $db is expected to be defined by the parent file (attendanceReport.php)
$userModel = new User($db);
$response = $userModel->getAllUser();

$users = ($response['success']) ? $response['data'] : [];
if (!$response['success']) {
    error_log($response['message']);
}
?>
<div class="row justify-content-center">
    <?php if (!empty($users)): ?>
        <?php foreach ($users as $user): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-user-circle fa-3x text-primary me-3 "></i>
                            <h5 class="card-title mb-0 ml-2">
                                <?= htmlspecialchars(($user['title'] ?? '') . ' ' . ($user['firstname'] ?? '') . ' ' . ($user['surname'] ?? '')) ?>
                            </h5>
                        </div>
                        <p class="text-muted mb-1">
                            <strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? 'N/A') ?>
                        </p>
                        <p class="text-muted mb-0">
                            <strong>Position:</strong> <?= htmlspecialchars($user['role'] ?? 'N/A') ?>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-start">
                        <a href="attendanceMasterData.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-primary btn-sm">รายละเอียดการทำงาน</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-warning text-center">
                <?= htmlspecialchars($response['message'] ?? 'ไม่พบข้อมูลผู้ใช้') ?>
            </div>
        </div>
    <?php endif; ?>
</div>
