/**
 * Attendance & Leave Management - Employee Logic
 */

const getThaiTime = () => {
    return new Date().toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', hour12: false });
};

$(document).ready(function() {
    // Clock-in Logic
    $('#attendanceModel').on('shown.bs.modal', function() {
        $('#attendance_time').val(getThaiTime());
        
        apiPost("../../api/attendanceApi.php", { action: 'checkAttendance', employee_id: $('#employee_id').val() })
            .done((res) => {
                if (res.exists) {
                    $('#saveAttendanceBtn').prop('disabled', true).text('บันทึกแล้ว');
                    Swal.fire({ icon: 'info', title: 'แจ้งเตือน', text: 'คุณได้บันทึกเวลาเข้างานแล้วในวันนี้' });
                }
            });
    });

    $('#saveAttendanceBtn').on('click', function() {
        const data = {
            action: 'create',
            employee_id: $('#employee_id').val(),
            attendance_date: $('#attendance_date').val(),
            attendance_time: $('#attendance_time').val()
        };

        if (!data.attendance_date || !data.attendance_time) {
            return Swal.fire({ icon: 'warning', title: 'กรุณากรอกข้อมูลให้ครบถ้วน' });
        }

        apiPost("../../api/attendanceApi.php", data)
            .done((res) => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message });
                }
            });
    });

    // Clock-out Logic
    $('#departureModel').on('shown.bs.modal', function() {
        apiPost("../../api/attendanceApi.php", { action: 'checkDeparture', employee_id: $('#employee_id_depart').val() })
            .done((res) => {
                if (res.exists) {
                    $('#departure_time').prop('disabled', true);
                    Swal.fire({ icon: 'info', title: 'แจ้งเตือน', text: 'คุณได้บันทึกเวลาออกงานแล้วในวันนี้' });
                }
            });
    });

    $('#saveDepart_time').on('click', function() {
        const data = {
            action: 'update',
            id: $('#attendance_id').val(),
            departure_time: $('#departure_time').val()
        };

        if (!data.departure_time) return Swal.fire({ icon: 'warning', title: 'กรุณาระบุเวลาออกงาน' });

        apiPost("../../api/attendanceApi.php", data)
            .done((res) => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message });
                }
            });
    });

    // Leave Logic
    $('#saveLeaveBtn').on('click', function() {
        const data = {
            action: 'create',
            employee_id: $('#employee_id_leave').val(),
            leave_type: $('#leave_type').val(),
            leave_date: $('#leave_date').val(),
            reason: $('#reason').val()
        };

        apiPost("../../api/leaveApi.php", data)
            .done((res) => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message });
                }
            });
    });
});
