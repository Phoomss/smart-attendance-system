/**
 * User Management - Admin Logic
 */

$(document).on('click', '.deleteBtn', function() {
    const id = $(this).data('id');
    Swal.fire({
        title: 'ยืนยันการลบ?',
        text: "ข้อมูลพนักงานและประวัติการทำงานจะถูกลบถาวร",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        confirmButtonText: 'ยืนยัน ลบข้อมูล',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            apiPost("../../api/userApi.php", { action: 'delete', id: id })
                .done((res) => {
                    if (res.success) {
                        Swal.fire({ icon: 'success', title: 'ลบข้อมูลสำเร็จ', timer: 1500 })
                            .then(() => location.reload());
                    } else {
                        Swal.fire('ผิดพลาด', res.message, 'error');
                    }
                })
                .fail(() => Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error'));
        }
    });
});

$('#editUserForm, #profileForm').on('submit', function(e) {
    e.preventDefault();
    const formData = $(this).serialize() + '&action=update';
    
    apiPost("../../api/userApi.php", formData)
        .done((res) => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ', timer: 1500 })
                    .then(() => {
                        if($(this).attr('id') === 'profileForm') location.reload();
                        else window.location.href = 'userAll.php';
                    });
            } else {
                Swal.fire('ผิดพลาด', res.message, 'error');
            }
        })
        .fail(() => Swal.fire('ผิดพลาด', 'ไม่สามารถส่งข้อมูลได้', 'error'));
});
