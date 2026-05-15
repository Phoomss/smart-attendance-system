<?php
// Note: $db and $userData are expected to be defined by the parent file (index.php)
$employee_id = $userData['id'];

$attendanceModel = new Attendance($db);
$attendanceUpdate = $attendanceModel->readInfo($employee_id);
?>

<!-- ปุ่มบันทึกเวลาออกงาน -->
<div class="col-md-6 mb-2" id="model">
    <a role="button" class="btn btn-outline-danger w-100" type="button" data-bs-toggle="modal" data-bs-target="#departureModel" id="departureButton">บันทึกเวลาออกงาน</a>
</div>

<!-- Modal for Departure -->
<div class="modal fade" id="departureModel" tabindex="-1" aria-labelledby="departureModelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="departureModelLabel">บันทึกเวลาออกงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($attendanceUpdate): ?>
                <form id="departureForm">
                    <div class="mb-3">
                        <label class="form-label">ชื่อ-นามสกุล</label>
                        <input type="hidden" id="attendance_id" value="<?php echo htmlentities($attendanceUpdate['id']); ?>">
                        <input type="hidden" id="employee_id_depart" value="<?php echo htmlentities($userData['id']); ?>">
                        <input type="text" class="form-control" value="<?php echo htmlentities(($userData['title'] ?? '') . ' ' . $userData['firstname'] . ' ' . $userData['surname']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">วันที่</label>
                        <input type="date" class="form-control" value="<?php echo htmlentities($attendanceUpdate['attendance_date']) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">เวลาเข้า</label>
                        <input type="time" class="form-control" value="<?php echo htmlentities(date('H:i', strtotime($attendanceUpdate['attendance_time']))); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="departure_time" class="form-label">เวลาออก</label>
                        <input type="time" class="form-control" id="departure_time" required>
                    </div>
                </form>
                <?php else: ?>
                    <div class="alert alert-warning">ไม่พบข้อมูลการเข้างานของวันนี้</div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <?php if ($attendanceUpdate): ?>
                <button type="button" class="btn btn-primary" id="saveDepart_time">บันทึก</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function checkTimeToShowButton() {
        const currentTime = new Date();
        const button = document.getElementById('departureButton');
        if (button) {
            // Show button after 12:00 PM
            button.style.display = (currentTime.getHours() >= 12) ? 'block' : 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', checkTimeToShowButton);

    $('#departureModel').on('shown.bs.modal', function() {
        $.ajax({
            type: "POST",
            url: "../../api/attendanceApi.php",
            data: {
                action: 'checkDeparture',
                employee_id: $('#employee_id_depart').val(),
            },
            dataType: "json",
            success: function(response) {
                if (response.exists) {
                    $('#departure_time').prop('disabled', true);
                    Swal.fire({ icon: 'info', title: 'แจ้งเตือน', text: 'คุณได้บันทึกเวลาออกงานแล้วในวันนี้' });
                } else {
                    $('#departure_time').prop('disabled', false);
                }
            }
        });
    });

    $('#saveDepart_time').on('click', function(e) {
        e.preventDefault();
        const departure_time = $('#departure_time').val();
        if (!departure_time) {
            Swal.fire({ icon: 'warning', title: 'กรุณาระบุเวลาออกงาน' });
            return;
        }

        $.ajax({
            type: "POST",
            url: "../../api/attendanceApi.php",
            data: {
                action: 'update',
                id: $('#attendance_id').val(),
                departure_time: departure_time
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ', text: response.message }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: response.message });
                }
            }
        });
    });
</script>
