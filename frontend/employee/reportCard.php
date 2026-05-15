<?php
// Note: $db and $employee_id are expected to be defined by the parent file (reportAttendance.php)
$detailWorkModel = new DetailWork($db);
$stats = $detailWorkModel->getEmployeeStats($employee_id);
?>
<div class="row">
    <!-- Attendance Card -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">การเข้าทำงาน</h5>
            </div>
            <div class="card-body">
                <p class="card-text h4 text-center"><?= htmlspecialchars($stats['attendance_count'] ?? 0) ?> ครั้ง</p>
            </div>
        </div>
    </div>

    <!-- Sick Leave Card -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">ลาป่วย</h5>
            </div>
            <div class="card-body">
                <p class="card-text h4 text-center"><?= htmlspecialchars($stats['sick_leave_count'] ?? 0) ?> วัน</p> 
            </div>
        </div>
    </div>

    <!-- Personal Leave Card -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0">ลากิจ</h5>
            </div>
            <div class="card-body">
                <p class="card-text h4 text-center"><?= htmlspecialchars($stats['personal_leave_count'] ?? 0) ?> วัน</p>
            </div>
        </div>
    </div>
</div>
