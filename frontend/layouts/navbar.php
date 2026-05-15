<?php
// Modern Topbar - Minimalist
?>
<nav class="navbar topbar mb-4 px-4 py-3">
    <div class="container-fluid">
        <!-- Desktop Toggle -->
        <button id="sidebarToggle" class="btn btn-light rounded-circle me-3 d-none d-md-block">
            <i class="fa fa-bars"></i>
        </button>
        
        <!-- Mobile Toggle (Offcanvas) -->
        <button class="btn btn-light rounded-circle me-3 d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <i class="fa fa-bars"></i>
        </button>
        
        <div class="ms-auto d-flex align-items-center">
            <span class="text-muted me-3 d-none d-md-block">
                <?= date('l, d M Y') ?>
            </span>
            <div class="vr me-3"></div>
            <a href="../../server/logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                <i class="fas fa-sign-out-alt me-1"></i> <span class="d-none d-sm-inline">ออกจากระบบ</span>
            </a>
        </div>
    </div>
</nav>