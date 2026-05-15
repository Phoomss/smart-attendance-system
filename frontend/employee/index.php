<?php
session_start();
require_once '../../server/conn.php';
require_once '../../server/user.php';
require_once '../../server/attendance.php';
require_once '../../server/detailWork.php';

// Initialize Database Connection via Conn class for DI
$database = new Conn();
$db = $database->getConnection();

// Ensure session variables are set
if (!isset($_SESSION['profile']) && !isset($_SESSION['userInfo'])) {
    header('Location: ../../index.php');
    exit();
}

$userEmail = ($_SESSION['profile']->email ?? '') ?: ($_SESSION['userInfo']['email'] ?? '');

if (!$userEmail) {
    die("No email found in session data.");
}

// Fetch user data using User model logic or direct query
$stmt = $db->prepare("SELECT id, title, firstname, surname, name, username, phone, email, picture, role FROM users WHERE email = :email");
$stmt->bindParam(':email', $userEmail);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - พนักงาน</title>
    <?php require_once '../../script/script.js' ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require_once 'navbar.php'; ?>
    <?php require_once 'popup.php'; ?>

    <main class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-4 mb-4">
                <div class="card shadow profile-card text-center">
                    <?php
                    $defaultImage = "user2.png";
                    $profilePicture = !empty($userData['picture']) ? htmlspecialchars($userData['picture']) : $defaultImage;
                    $name = !empty($userData['firstname']) 
                        ? htmlspecialchars(($userData['title'] ?? '') . $userData['firstname'] . ' ' . $userData['surname'])
                        : htmlspecialchars($userData['name'] ?? 'Unknown');
                    ?>
                    <div class="card-body">
                        <img src="<?php echo $profilePicture; ?>" class="rounded-circle mb-3" alt="Profile" style="width: 120px; height: 120px; object-fit: cover;">
                        <h5 class="card-title text-primary"><?php echo $name; ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($userData['email']); ?></p>
                        <p class="card-text text-muted"><span class="badge bg-info"><?php echo $userData['role'] === 'employee' ? 'พนักงาน' : 'แอดมิน'; ?></span></p>
                        <a href="profile.php" class="btn btn-outline-primary w-100">แก้ไขข้อมูลส่วนตัว</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">รายละเอียดการเข้างาน</div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <?php require_once 'attendanceCreate.php' ?>
                            <?php require_once 'attendanceUpdate.php' ?>
                            <?php require_once 'createLeave.php' ?>
                        </div>
                        <div class="my-4">
                            <?php require_once 'attendanceDetail.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
