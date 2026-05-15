<?php
/**
 * System Footer Component
 */
$currentYear = date('Y');
?>
<footer class="footer mt-auto py-3 bg-white border-top">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">
                Copyright &copy; Smart Attendance System <?= $currentYear ?>. All Rights Reserved.
            </div>
            <div>
                <a href="#" class="text-muted text-decoration-none">Privacy Policy</a>
                &middot;
                <a href="#" class="text-muted text-decoration-none">Terms &amp; Conditions</a>
            </div>
        </div>
    </div>
</footer>