<?php
session_start();
require_once '../../server/conn.php';
require_once '../../server/detailWork.php';

$database = new Conn();
$db = $database->getConnection();

if (!isset($_SESSION['profile']) && !isset($_SESSION['userInfo'])) {
    header('Location: ../../index.php');
    exit();
}

$userEmail = ($_SESSION['profile']->email ?? '') ?: ($_SESSION['userInfo']['email'] ?? '');

$stmt = $db->prepare("SELECT id, title, firstname, surname FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $userEmail]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) die("User not found.");

$employee_id = $userData['id'];
$detailWork = new DetailWork($db);
$info = $detailWork->readInfo($employee_id);

$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startLimit = ($currentPage - 1) * $itemsPerPage;

$combinedData = array_merge($info['attendance'], $info['leave']);

// Filtering
$searchDate = $_GET['searchDate'] ?? '';
$leaveType = $_GET['leave_type'] ?? '';

if ($searchDate || $leaveType) {
    $combinedData = array_filter($combinedData, function ($row) use ($searchDate, $leaveType) {
        $matchDate = !$searchDate || ($row['leave_date'] ?? date('Y-m-d', strtotime($row['created_at']))) == $searchDate;
        $matchType = !$leaveType || ($row['leave_type'] ?? '') == $leaveType;
        return $matchDate && $matchType;
    });
}

$totalItems = count($combinedData);
$totalPages = ceil($totalItems / $itemsPerPage);
$pageData = array_slice($combinedData, $startLimit, $itemsPerPage);

function renderRow($index, $row, $userData)
{
    $isLeave = isset($row['leave_type']);
    $typeText = $isLeave ? "ลา ({$row['leave_type']})" : "เข้างาน";
    $timeIn = $isLeave ? "-" : ($row['created_at'] ?? '-');
    $timeOut = $isLeave ? "-" : ($row['departure_time'] ?? '-');
    $statusReason = $isLeave ? ($row['reason'] ?? '-') : (($row['status'] ?? '') == 'on_time' ? 'ปกติ' : 'สาย');

    echo "<tr>
            <td>{$index}</td>
            <td>{$userData['title']}{$userData['firstname']} {$userData['surname']}</td>
            <td>{$timeIn}</td>
            <td>{$timeOut}</td>
            <td>{$typeText}</td>
            <td>" . ($isLeave ? $row['leave_date'] : "-") . "</td>
            <td>{$statusReason}</td>
          </tr>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>รายงานการทำงาน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once 'navbar.php'; ?>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-3">
                <?php require_once 'reportCard.php' ?>
            </div>
            <div class="col-md-9">
                <form method="GET" class="card p-3 mb-4 shadow-sm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="date" class="form-control" name="searchDate" value="<?= $searchDate ?>">
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" name="leave_type">
                                <option value="">-- ทั้งหมด --</option>
                                <option value="ลาป่วย" <?= $leaveType == 'ลาป่วย' ? 'selected' : '' ?>>ลาป่วย</option>
                                <option value="ลากิจ" <?= $leaveType == 'ลากิจ' ? 'selected' : '' ?>>ลากิจ</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">ค้นหา</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th><th>ชื่อ</th><th>เวลาเข้า</th><th>เวลาออก</th><th>ประเภท</th><th>วันที่ลา</th><th>สถานะ/เหตุผล</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($pageData) {
                                $idx = $startLimit + 1;
                                foreach ($pageData as $row) renderRow($idx++, $row, $userData);
                            } else {
                                echo "<tr><td colspan='7' class='text-center py-4'>ไม่พบข้อมูล</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= $p == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $p ?>&searchDate=<?= $searchDate ?>&leave_type=<?= $leaveType ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
