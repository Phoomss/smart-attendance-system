/**
 * Authentication Logic - Login & Registration
 */

$(document).ready(function() {
    // Login Logic
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        const data = {
            identifier: $('#identifier').val(),
            password: $('#password').val()
        };

        apiPost('./api/loginApi.php', data)
            .done((res) => {
                if (res.status_code === 200) {
                    Swal.fire({ icon: 'success', title: 'เข้าสู่ระบบสำเร็จ', timer: 1000, showConfirmButton: false })
                        .then(() => {
                            if (res.role === 'admin') window.location.href = './frontend/admin/admin_dashboard.php';
                            else window.location.href = './frontend/employee/index.php';
                        });
                } else {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message || 'เข้าสู่ระบบไม่สำเร็จ' });
                }
            })
            .fail(() => Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ' }));
    });

    // Registration Logic
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        const formData = {
            title: $('#title').val(),
            firstname: $('#firstname').val(),
            surname: $('#surname').val(),
            username: $('#username').val(),
            email: $('#email').val(),
            password: $('#password').val()
        };

        apiPost('./api/registerApi.php', formData)
            .done((res) => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'สมัครสมาชิกสำเร็จ', text: res.message })
                        .then(() => window.location.href = 'index.php');
                } else {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message });
                }
            })
            .fail(() => Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'เกิดข้อผิดพลาดในการลงทะเบียน' }));
    });
});
