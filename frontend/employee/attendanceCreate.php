<!-- บันทึกเวลาเข้างาน -->
<div class="col-md-4">
    <button class="btn btn-primary w-100 rounded-pill py-3 shadow-sm transition-hover" data-bs-toggle="modal" data-bs-target="#attendanceModel">
        <i class="fas fa-sign-in-alt me-2"></i> บันทึกเวลาเข้างาน
    </button>
</div>

<!-- Modal Clock-in -->
<div class="modal fade" id="attendanceModel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">บันทึกเวลาเข้างาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="attendanceForm">
                    <input type="hidden" id="employee_id" value="<?= htmlspecialchars($userData['id']); ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">ชื่อ-นามสกุล</label>
                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars(($userData['title'] ?? '') . ' ' . $userData['firstname'] . ' ' . $userData['surname']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">วันที่</label>
                        <input type="date" class="form-control" id="attendance_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">เวลาเข้า</label>
                        <input type="time" class="form-control" id="attendance_time" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="saveAttendanceBtn">ยืนย6นบันทึก</button>
            </div>
        </div>
    </div>
</div>
