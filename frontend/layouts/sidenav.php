<?php
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['userInfo']['role'] ?? 'employee';
?>
<div class="offcanvas-md offcanvas-start sidebar vh-100 sticky-top" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header d-md-none border-bottom">
        <h5 class="offcanvas-title fw-bold text-dark" id="sidebarMenuLabel"><i class="fas fa-clock text-primary me-2"></i> Attendance</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
    </div>
    
    <div class="offcanvas-body d-flex flex-column p-3 h-100">
        <a href="#" class="d-none d-md-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none p-2">
            <i class="fas fa-clock fa-2x text-primary me-2"></i>
            <span class="fs-5 fw-bold sidebar-brand-text">Attendance</span>
        </a>
        <hr class="d-none d-md-block">
        
        <ul class="nav nav-pills flex-column mb-auto w-100">
            <?php if ($role === 'admin'): ?>
                <li class="nav-item">
                    <a href="../admin/admin_dashboard.php" class="nav-link <?= $current_page == 'admin_dashboard.php' ? 'active' : '' ?>">
                        <i class="fas fa-chart-line"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="../admin/attendanceReport.php" class="nav-link <?= $current_page == 'attendanceReport.php' ? 'active' : '' ?>">
                        <i class="fas fa-file-alt"></i> <span>รายงานเข้าออกงาน</span>
                    </a>
                </li>
                <li>
                    <a href="../admin/userAll.php" class="nav-link <?= $current_page == 'userAll.php' ? 'active' : '' ?>">
                        <i class="fas fa-users"></i> <span>จัดการพนักงาน</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a href="../employee/index.php" class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>">
                        <i class="fas fa-home"></i> <span>หน้าหลัก</span>
                    </a>
                </li>
                <li>
                    <a href="../employee/reportAttendance.php" class="nav-link <?= $current_page == 'reportAttendance.php' ? 'active' : '' ?>">
                        <i class="fas fa-history"></i> <span>ประวัติการเข้างาน</span>
                    </a>
                </li>
            <?php endif; ?>
            
            <li>
                <a href="<?= $role === 'admin' ? '../admin/profile.php' : '../employee/profile.php' ?>" class="nav-link <?= $current_page == 'profile.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-circle"></i> <span>โปรไฟล์ส่วนตัว</span>
                </a>
            </li>
        </ul>
        
        <hr>
        <div class="dropdown mt-auto">
            <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="../../src/img/undraw_profile.svg" onerror="this.src='../../public/img/user2.png'; this.onerror=null;" alt="" width="32" height="32" class="rounded-circle me-2">
                <strong><?= htmlspecialchars($_SESSION['userInfo']['username'] ?? 'User') ?></strong>
            </a>
            <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
                <li><a class="dropdown-item" href="../../server/logout.php">ออกจากระบบ</a></li>
            </ul>
        </div>
    </div>
</div>