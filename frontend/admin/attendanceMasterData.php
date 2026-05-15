<?php
session_start();
require_once '../../server/conn.php';
require_once '../../server/user.php';
require_once '../../server/attendance.php';
require_once '../../server/detailWork.php';

$database = new Conn();
$db = $database->getConnection();

$employee_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($employee_id > 0) {
    $detailWork = new DetailWork($db);
    $info = $detailWork->readInfo($employee_id);

    $user = new User($db);
    $response = $user->getUserInfo($employee_id);
    $userInfo = $response['success'] ? $response['data'] : [];
} else {
    header('Location: attendanceReport.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการทำงาน - <?php echo htmlentities($userInfo['firstname'] ?? ''); ?></title>
    <?php include_once '../layouts/config/libary.php' ?>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include_once '../layouts/sidenav.php' ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include_once '../layouts/navbar.php' ?>
             
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">รายงานเข้าออกงาน</h1>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">ข้อมูลของ: <?php echo htmlentities(($userInfo['title'] ?? '') . ' ' . ($userInfo['firstname'] ?? '') . ' ' . ($userInfo['surname'] ?? '')); ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>ประเภท</th>
                                            <th>วันที่/เวลา</th>
                                            <th>เวลาออก (ถ้ามี)</th>
                                            <th>สถานะ/เหตุผล</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($info) {
                                            $index = 1;
                                            foreach ($info['attendance'] as $row) {
                                                $status_text = ($row['status'] == 'on_time') ? '<span class="badge badge-success">ปกติ</span>' : '<span class="badge badge-warning">สาย</span>';
                                                echo "<tr>
                                                        <td>{$index}</td>
                                                        <td>เข้างาน</td>
                                                        <td>" . htmlspecialchars($row['created_at']) . "</td>
                                                        <td>" . htmlspecialchars($row['departure_time'] ?? '-') . "</td>
                                                        <td>{$status_text}</td>
                                                      </tr>";
                                                $index++;
                                            }
                                            foreach ($info['leave'] as $row) {
                                                echo "<tr>
                                                        <td>{$index}</td>
                                                        <td>ลา ({$row['leave_type']})</td>
                                                        <td>" . htmlspecialchars($row['leave_date']) . " ถึง " . htmlspecialchars($row['leave_end_date'] ?? $row['leave_date']) . "</td>
                                                        <td>-</td>
                                                        <td>" . htmlspecialchars($row['reason'] ?? '-') . " (" . htmlspecialchars($row['status']) . ")</td>
                                                      </tr>";
                                                $index++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='5' class='text-center'>ไม่พบข้อมูล</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once '../layouts/footer.php' ?>
        </div>
    </div>
    <?php include_once '../layouts/config/script.php' ?>
</body>
</html>
