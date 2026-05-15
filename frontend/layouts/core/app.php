<?php
// Session Timeout Security (30 Minutes)
$timeout_duration = 1800;
if (isset($_SESSION['LAST_ACTIVITY'])) {
    if (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: ../../index.php?timeout=1");
        exit();
    }
}
$_SESSION['LAST_ACTIVITY'] = time();

function renderLayout($title, $content, $scripts = '') {
    // Determine the base path for assets depending on the inclusion depth.
    $basePath = '../../';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Attendance System</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Modern SaaS Overrides -->
    <link href="<?= $basePath ?>public/css/main.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <?php include_once __DIR__ . '/../sidenav.php'; ?>
        
        <div class="flex-grow-1 overflow-auto d-flex flex-column vh-100">
            <?php include_once __DIR__ . '/../navbar.php'; ?>
            
            <main class="container-fluid px-4 flex-grow-1 py-4">
                <?= $content ?>
            </main>
            
            <?php include_once __DIR__ . '/../footer.php'; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= $basePath ?>public/js/app.js"></script>
    <script src="<?= $basePath ?>public/js/attendance.js"></script>
    <?= $scripts ?>
</body>
</html>
<?php } ?>