<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - Attendance System</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="public/css/main.css" rel="stylesheet">
</head>
<body class="auth-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card auth-card p-5">
                    <div class="text-center mb-5">
                        <h3 class="fw-bold text-dark">สร้างบัญชีผู้ใช้งาน</h3>
                        <p class="text-muted small">กรอกข้อมูลด้านล่างเพื่อเข้าร่วมระบบ Attendance</p>
                    </div>

                    <form id="registerForm">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">คำนำหน้า</label>
                                <div class="input-group">
                                    <span class="input-group-text text-muted"><i class="fas fa-id-card"></i></span>
                                    <select class="form-select" id="title" required>
                                        <option value="" disabled selected>เลือกคำนำหน้า</option>
                                        <option value="นาย">นาย</option>
                                        <option value="นาง">นาง</option>
                                        <option value="นางสาว">นางสาว</option>
                                        <option value="Mr.">Mr.</option>
                                        <option value="Ms.">Ms.</option>
                                        <option value="Mrs.">Mrs.</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">ชื่อจริง</label>
                                <div class="input-group">
                                    <span class="input-group-text text-muted"><i class="far fa-user"></i></span>
                                    <input type="text" id="firstname" class="form-control" placeholder="ชื่อจริง" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">นามสกุล</label>
                                <div class="input-group">
                                    <span class="input-group-text text-muted"><i class="far fa-user"></i></span>
                                    <input type="text" id="surname" class="form-control" placeholder="นามสกุล" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">ชื่อผู้ใช้งาน</label>
                                <div class="input-group">
                                    <span class="input-group-text text-muted"><i class="fas fa-at"></i></span>
                                    <input type="text" id="username" class="form-control" placeholder="username" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">อีเมล</label>
                                <div class="input-group">
                                    <span class="input-group-text text-muted"><i class="far fa-envelope"></i></span>
                                    <input type="email" id="email" class="form-control" placeholder="example@email.com" required>
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label small fw-bold text-muted">รหัสผ่าน</label>
                                <div class="input-group">
                                    <span class="input-group-text text-muted"><i class="fas fa-lock"></i></span>
                                    <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mb-4">
                            <button type="submit" class="btn btn-primary rounded-pill">สมัครสมาชิก</button>
                        </div>

                        <div class="text-center">
                            <p class="small text-muted">มีบัญชีอยู่แล้ว? <a href="index.php" class="text-primary fw-bold text-decoration-none">เข้าสู่ระบบ</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const apiPost = (url, data) => $.ajax({ type: "POST", url: url, data: data, dataType: "json" });
    </script>
    <script src="public/js/auth.js"></script>
</body>
</html>
