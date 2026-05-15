<?php
session_start();
require_once '../../server/conn.php';
require_once '../../server/detailWork.php';

$database = new Conn();
$db = $database->getConnection();
$detailWork = new DetailWork($db);
$dailyStats = $detailWork->getDailyStats();

ob_start();
?>
                <div class="mb-4">
                    <h1 class="h3 fw-bold text-dark">Dashboard Overview</h1>
                    <p class="text-muted small">ยินดีต้อนรับกลับ, <?= htmlspecialchars($_SESSION['userInfo']['username'] ?? 'User') ?></p>
                </div>

                <!-- Stats Cards -->
                <div class="row">
                    <?php include_once '../components/cards/stat_card.php' ?>
                </div>

                <!-- Charts -->
                <div class="row mt-4">
                    <div class="col-lg-8 mb-4">
                        <div class="card border-0 shadow-sm p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="fw-bold mb-0">Monthly Attendance Trends</h6>
                                <span class="badge bg-primary-subtle text-primary rounded-pill">Year 2025</span>
                            </div>
                            <div style="height: 300px;">
                                <canvas id="attendanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm p-4">
                            <h6 class="fw-bold mb-4">Leave Distribution</h6>
                            <div style="height: 300px;" class="d-flex align-items-center justify-content-center">
                                <canvas id="leaveChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
<?php
$content = ob_get_clean();

ob_start();
?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../public/js/dashboard.js"></script>
<?php
$scripts = ob_get_clean();

require_once '../layouts/core/app.php';
renderLayout('Admin Dashboard', $content, $scripts);
?>
