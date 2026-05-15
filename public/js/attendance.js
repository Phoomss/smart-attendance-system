/**
 * Attendance & Leave Management - Employee Logic
 */

const getThaiTime = () => {
    return new Date().toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', hour12: false });
};

$(document).ready(function() {
    let userLocation = { lat: null, lng: null };

    const getLocation = () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                userLocation.lat = position.coords.latitude;
                userLocation.lng = position.coords.longitude;
            }, null, { enableHighAccuracy: true });
        }
    };

    // Clock-in Logic
    $('#attendanceModel').on('shown.bs.modal', function() {
        $('#attendance_time').val(getThaiTime());
        getLocation();
        
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
            attendance_time: $('#attendance_time').val(),
            latitude: userLocation.lat,
            longitude: userLocation.lng
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
        getLocation();
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
            departure_time: $('#departure_time').val(),
            latitude: userLocation.lat,
            longitude: userLocation.lng
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
        const formData = new FormData();
        formData.append('action', 'create');
        formData.append('employee_id', $('#employee_id_leave').val());
        formData.append('leave_type', $('#leave_type').val());
        formData.append('leave_date', $('#leave_date').val());
        formData.append('reason', $('#reason').val());
        
        const fileInput = document.getElementById('attachment');
        if (fileInput.files.length > 0) {
            formData.append('attachment', fileInput.files[0]);
        }

        $.ajax({
            url: "../../api/leaveApi.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: (res) => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message });
                }
            },
            error: () => Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ' })
        });
    });
});
