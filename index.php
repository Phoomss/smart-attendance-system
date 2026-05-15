<?php
session_start();
require_once './server/LineLogin.php';

$line = new LineLogin();
$lineLink = $line->getLink();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Attendance System</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="public/css/main.css" rel="stylesheet">
</head>
<body class="auth-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card auth-card">
                    <div class="row g-0">
                        <!-- Left Side: Form -->
                        <div class="col-md-6 p-5">
                            <div class="mb-5">
                                <h3 class="fw-bold text-dark">ยินดีต้อนรับ</h3>
                                <p class="text-muted small">กรุณาเข้าสู่ระบบเพื่อใช้งานระบบเข้าออกงาน</p>
                            </div>

                            <form id="loginForm">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">อีเมล หรือ ชื่อผู้ใช้งาน</label>
                                    <div class="input-group">
                                        <span class="input-group-text text-muted"><i class="far fa-user"></i></span>
                                        <input type="text" id="identifier" class="form-control" placeholder="example@email.com" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted">รหัสผ่าน</label>
                                    <div class="input-group">
                                        <span class="input-group-text text-muted"><i class="fas fa-lock"></i></span>
                                        <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mb-4">
                                    <button type="submit" class="btn btn-primary rounded-pill">เข้าสู่ระบบ</button>
                                    <?php if (empty($_SESSION['profile'])): ?>
                                        <a href="<?= $lineLink ?>" class="btn btn-line rounded-pill">
                                            <i class="fa-brands fa-line me-2"></i> เข้าสู่ระบบด้วย LINE
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <div class="text-center">
                                    <p class="small text-muted">ยังไม่มีบัญชี? <a href="register.php" class="text-primary fw-bold text-decoration-none">สมัครสมาชิก</a></p>
                                </div>
                            </form>
                        </div>

                        <!-- Right Side: Illustration -->
                        <div class="col-md-6 d-none d-md-flex illustration">
                            <div class="text-center text-white">
                                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.svg" class="img-fluid mb-4" alt="Login">
                                <h4 class="fw-bold">Smart Attendance</h4>
                                <p class="small opacity-75">บันทึกเวลาเข้างานง่ายๆ สะดวกและรวดเร็ว</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Centralized API post helper for auth pages
        const apiPost = (url, data) => $.ajax({ type: "POST", url: url, data: data, dataType: "json" });
    </script>
    <script src="public/js/auth.js"></script>
</body>
</html>
