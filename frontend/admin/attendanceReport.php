<?php
session_start();
require_once '../../server/conn.php';
require_once '../../server/user.php';
require_once '../../server/attendance.php';
require_once '../../server/detailWork.php';

// Initialize Database Connection once
$database = new Conn();
$db = $database->getConnection();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบเข้าออกงาน</title>
    <?php include_once '../layouts/config/libary.php' ?>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include_once '../layouts/sidenav.php' ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <?php include_once '../layouts/navbar.php' ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">รายงานเข้าออกงาน</h1>
                    </div>

                    <?php 
                    // The required file will use the initialized $db connection
                    require_once 'attendanceDetail.php';
                    ?>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include_once '../layouts/footer.php' ?>
        </div>
    </div>


    <?php include_once '../layouts/config/script.php' ?>
</body>

</html>
