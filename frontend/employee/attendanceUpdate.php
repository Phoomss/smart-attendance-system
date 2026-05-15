<?php
// Note: $db and $userData are from index.php
$attendanceModel = new Attendance($db);
$attendanceUpdate = $attendanceModel->readInfo($userData['id']);
?>

<!-- บันทึกเวลาออกงาน -->
<div class="col-md-4">
    <button class="btn btn-danger w-100 rounded-pill py-3 shadow-sm transition-hover" id="departureButton" data-bs-toggle="modal" data-bs-target="#departureModel">
        <i class="fas fa-sign-out-alt me-2"></i> บันทึกเวลาออกงาน
    </button>
</div>

<!-- Modal Clock-out -->
<div class="modal fade" id="departureModel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">บันทึกเวลาออกงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <?php if ($attendanceUpdate): ?>
                <form id="departureForm">
                    <input type="hidden" id="attendance_id" value="<?= htmlspecialchars($attendanceUpdate['id']); ?>">
                    <input type="hidden" id="employee_id_depart" value="<?= htmlspecialchars($userData['id']); ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">เข้างานเมื่อ</label>
                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($attendanceUpdate['attendance_date'] . ' ' . date('H:i', strtotime($attendanceUpdate['attendance_time']))) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">เวลาออก</label>
                        <input type="time" class="form-control" id="departure_time" value="<?= date('H:i') ?>" required>
                    </div>
                </form>
                <?php else: ?>
                    <div class="alert alert-warning border-0 small">ไม่พบข้อมูลการเข้างานของวันนี้ คุณต้องบันทึกเวลาเข้างานก่อน</div>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ปิด</button>
                <?php if ($attendanceUpdate): ?>
                <button type="button" class="btn btn-danger rounded-pill px-4" id="saveDepart_time">บันทึกเวลาออก</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
