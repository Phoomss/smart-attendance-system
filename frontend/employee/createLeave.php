<!-- บันทึกการลา -->
<div class="col-md-4">
    <button class="btn btn-warning w-100 rounded-pill py-3 shadow-sm transition-hover" data-bs-toggle="modal" data-bs-target="#leaveModel">
        <i class="fas fa-calendar-times me-2"></i> บันทึกการลา
    </button>
</div>

<!-- Modal Leave -->
<div class="modal fade" id="leaveModel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">บันทึกการแจ้งลา</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="leaveForm">
                    <input type="hidden" id="employee_id_leave" value="<?= htmlspecialchars($userData['id']); ?>">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">ประเภทการลา</label>
                        <select class="form-select" id="leave_type" required>
                            <option value="ลาป่วย">ลาป่วย</option>
                            <option value="ลากิจ">ลากิจ</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">วันที่ลา</label>
                        <input type="date" class="form-control" id="leave_date" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">เหตุผลการลา</label>
                        <textarea class="form-control" id="reason" rows="3" placeholder="ระบุเหตุผลที่ต้องการลา..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-warning rounded-pill px-4" id="saveLeaveBtn">ยืนยันส่งใบลา</button>
            </div>
        </div>
    </div>
</div>
