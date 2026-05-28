<?php
/**
 * Smart Attendance System - Mock Data Generator
 * Seeds users, attendances (with coordinates), leaves (with reasons), and leave quotas.
 */

require_once 'server/conn.php';

$database = new Conn();
$db = $database->getConnection();

if (!$db) {
    die("<h1 style='color:red'>Connection Failed: Could not connect to database.</h1>");
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock Data Seeder - Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .status-box { max-height: 400px; overflow-y: auto; background-color: #0f172a; color: #38bdf8; font-family: monospace; font-size: 0.9rem; border-radius: 12px; padding: 20px; }
        .text-success-custom { color: #4ade80; }
        .text-warning-custom { color: #facc15; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                        <i class="fas fa-database fa-2x"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-0">System Mock Data Generator</h2>
                        <p class="text-muted mb-0">Populating database with realistic, high-quality records</p>
                    </div>
                </div>
                <hr>
                <div class="alert alert-warning border-0 bg-warning-subtle text-warning-emphasis rounded-3">
                    <i class="fas fa-exclamation-triangle me-2"></i><strong>Warning:</strong> Running this script will clear current records inside <code>users</code>, <code>attendances</code>, <code>leaves</code>, and <code>leave_quotas</code> tables to generate a clean, cohesive simulation dataset.
                </div>

                <form method="POST" class="mb-4">
                    <button type="submit" name="seed" class="btn btn-primary rounded-pill px-4 py-2fw-bold">
                        <i class="fas fa-play me-2"></i>Start Seeding Mock Data
                    </button>
                </form>

                <?php
                if (isset($_POST['seed'])) {
                    echo '<h5 class="fw-bold mb-3"><i class="fas fa-spinner fa-spin me-2 text-primary"></i>Execution Logs:</h5>';
                    echo '<div class="status-box mb-4">';
                    
                    try {
                        // 1. Disable Foreign Key Checks to allow unrestricted schema upgrades and safe truncation
                        $db->exec("SET FOREIGN_KEY_CHECKS = 0");
                        echo "<span class='text-success-custom'>[REPAIR]</span> Temporary disabled database foreign key constraints.<br>";

                        // Auto-Heal/Create missing tables and columns if they don't exist yet
                        echo "<span class='text-warning-custom'>[REPAIR]</span> Ensuring database structure is up to date...<br>";

                        // Create leave_quotas table
                        $db->exec("CREATE TABLE IF NOT EXISTS leave_quotas (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            employee_id INT NOT NULL,
                            sick_limit INT DEFAULT 30,
                            personal_limit INT DEFAULT 6,
                            year INT NOT NULL,
                            UNIQUE KEY (employee_id, year)
                        )");
                        echo "<span class='text-success-custom'>[REPAIR]</span> Table 'leave_quotas' verified/created.<br>";

                        // Ensure primary keys and AUTO_INCREMENT exist on users, attendances, and leaves
                        $tablesToFix = ['users', 'attendances', 'leaves'];
                        foreach ($tablesToFix as $t) {
                            try {
                                $db->exec("ALTER TABLE $t MODIFY id INT(11) NOT NULL AUTO_INCREMENT");
                                echo "<span class='text-success-custom'>[REPAIR]</span> Verified/enabled auto-increment on '$t' table.<br>";
                            } catch (PDOException $e) {
                                try {
                                    $db->exec("ALTER TABLE $t ADD PRIMARY KEY (id)");
                                    $db->exec("ALTER TABLE $t MODIFY id INT(11) NOT NULL AUTO_INCREMENT");
                                    echo "<span class='text-success-custom'>[REPAIR]</span> Added primary key & enabled auto-increment on '$t' table.<br>";
                                } catch (Exception $ex) {
                                    echo "<span class='text-danger'>[REPAIR WARNING]</span> Failed to apply auto-increment on '$t': " . $ex->getMessage() . "<br>";
                                }
                            }
                        }

                        // Add latitude column to attendances
                        try {
                            $db->exec("ALTER TABLE attendances ADD COLUMN latitude DECIMAL(10, 8) NULL AFTER status");
                            echo "<span class='text-success-custom'>[REPAIR]</span> Added 'latitude' column to 'attendances' table.<br>";
                        } catch (PDOException $e) {
                            // Column might already exist
                        }

                        // Add longitude column to attendances
                        try {
                            $db->exec("ALTER TABLE attendances ADD COLUMN longitude DECIMAL(11, 8) NULL AFTER latitude");
                            echo "<span class='text-success-custom'>[REPAIR]</span> Added 'longitude' column to 'attendances' table.<br>";
                        } catch (PDOException $e) {
                            // Column might already exist
                        }

                        // Add attachment_path column to leaves
                        try {
                            $db->exec("ALTER TABLE leaves ADD COLUMN attachment_path VARCHAR(255) NULL AFTER reason");
                            echo "<span class='text-success-custom'>[REPAIR]</span> Added 'attachment_path' column to 'leaves' table.<br>";
                        } catch (PDOException $e) {
                            // Column might already exist
                        }

                        // 2. Truncate tables safely
                        $db->exec("TRUNCATE TABLE attendances");
                        $db->exec("TRUNCATE TABLE leaves");
                        $db->exec("TRUNCATE TABLE leave_quotas");
                        $db->exec("TRUNCATE TABLE users");
                        echo "<span class='text-success-custom'>[SUCCESS]</span> All database tables truncated successfully.<br>";

                        // 2. Insert Admin Account
                        $password_hash = password_hash('password', PASSWORD_DEFAULT);
                        $adminSql = "INSERT INTO users (id, employee_code, title, firstname, surname, name, username, phone, email, password, role) 
                                     VALUES (1, 'EMP001', 'Mr.', 'Admin', 'System', 'Admin System', 'admin', '0812345678', 'admin@admin.com', :password, 'admin')";
                        $adminStmt = $db->prepare($adminSql);
                        $adminStmt->execute([':password' => $password_hash]);
                        echo "<span class='text-success-custom'>[SUCCESS]</span> Seeded Admin Account: <strong class='text-white'>admin</strong> / <strong class='text-white'>password</strong><br>";

                        // 3. Insert 5 Employees
                        $employees = [
                            ['EMP002', 'Mr.', 'Somchai', 'Jaidee', 'somchai', '0821234567', 'somchai@company.com'],
                            ['EMP003', 'Ms.', 'Somsri', 'Rakdee', 'somsri', '0831234567', 'somsri@company.com'],
                            ['EMP004', 'Mr.', 'John', 'Doe', 'john', '0841234567', 'john@company.com'],
                            ['EMP005', 'Mrs.', 'Jane', 'Smith', 'jane', '0851234567', 'jane@company.com'],
                            ['EMP006', 'Mr.', 'Wichai', 'Dee', 'wichai', '0861234567', 'wichai@company.com']
                        ];

                        $employeeIds = [];
                        $empInsertSql = "INSERT INTO users (employee_code, title, firstname, surname, name, username, phone, email, password, role) 
                                         VALUES (:code, :title, :first, :last, :name, :username, :phone, :email, :password, 'employee')";
                        $empStmt = $db->prepare($empInsertSql);

                        foreach ($employees as $emp) {
                            $fullName = $emp[1] . ' ' . $emp[2] . ' ' . $emp[3];
                            $empStmt->execute([
                                ':code' => $emp[0],
                                ':title' => $emp[1],
                                ':first' => $emp[2],
                                ':last' => $emp[3],
                                ':name' => $fullName,
                                ':username' => $emp[4],
                                ':phone' => $emp[5],
                                ':email' => $emp[6],
                                ':password' => $password_hash
                            ]);
                            $employeeIds[] = $db->lastInsertId();
                        }
                        echo "<span class='text-success-custom'>[SUCCESS]</span> Seeded 5 Employee Accounts (passwords: 'password').<br>";

                        // 4. Seed Leave Quotas for 2026
                        $quotaSql = "INSERT INTO leave_quotas (employee_id, sick_limit, personal_limit, year) 
                                     VALUES (:emp_id, 30, 6, 2026)";
                        $quotaStmt = $db->prepare($quotaSql);
                        foreach ($employeeIds as $empId) {
                            $quotaStmt->execute([':emp_id' => $empId]);
                        }
                        echo "<span class='text-success-custom'>[SUCCESS]</span> Configured annual Leave Quotas (Sick: 30 days, Personal: 6 days) for all employees.<br>";

                        // 5. Generate past 30 days of Weekday Attendance & Leaves
                        echo "<br><span class='text-warning-custom'>[GENERATING]</span> Calculating 30 days of dynamic history logs...<br>";

                        // Setup date ranges
                        $currentDate = new DateTime();
                        $daysToGenerate = 30;
                        $generatedDaysCount = 0;
                        
                        $attendanceStmt = $db->prepare("INSERT INTO attendances (employee_id, attendance_date, attendance_time, departure_time, status, latitude, longitude) 
                                                        VALUES (:emp_id, :date, :time_in, :time_out, :status, :lat, :lng)");

                        $leaveStmt = $db->prepare("INSERT INTO leaves (employee_id, leave_type, leave_date, leave_end_date, reason, status, attachment_path) 
                                                   VALUES (:emp_id, :type, :date, :end_date, :reason, :status, :attachment)");

                        // Realistic Thai reasons
                        $sickReasons = [
                            "มีไข้สูง ตัวร้อน เป็นหวัด คัดจมูก",
                            "ปวดท้อง ท้องเสีย ร่างกายอ่อนเพลีย ไปพบแพทย์",
                            "ปวดศีรษะ ไมเกรนกำเริบอย่างรุนแรง",
                            "ประสบอุบัติเหตุลื่นล้ม ข้อเท้าแพลง ขยับตัวลำบาก"
                        ];
                        
                        $personalReasons = [
                            "มีนัดทำธุระจัดการเอกสารส่วนตัวที่ที่ทำการอำเภอ",
                            "พาคุณแม่ไปพบแพทย์ตามนัดของโรงพยาบาล",
                            "ติดต่อทำเรื่องโอนกรรมสิทธิ์ที่ดินและที่อยู่อาศัย",
                            "จัดการงานแต่งงานของญาติสนิทในครอบครัว"
                        ];

                        // Bangkok Base coordinates (around central area)
                        $baseLat = 13.736717;
                        $baseLng = 100.523186;

                        for ($i = $daysToGenerate; $i >= 0; $i--) {
                            $date = new DateTime();
                            $date->sub(new DateInterval("P{$i}D"));
                            
                            // Skip Weekends for attendance
                            $dayOfWeek = $date->format('N'); // 1 (Mon) - 7 (Sun)
                            if ($dayOfWeek >= 6) {
                                continue;
                            }
                            
                            $dateStr = $date->format('Y-m-d');
                            $generatedDaysCount++;

                            foreach ($employeeIds as $empId) {
                                // Decide state: 0 = Present, 1 = On Leave, 2 = Absent
                                $randState = rand(0, 100);
                                
                                if ($randState < 5) {
                                    // 5% Chance of Absent
                                    echo "  - Employee ID $empId marked Absent on $dateStr<br>";
                                } 
                                elseif ($randState >= 5 && $randState < 12) {
                                    // 7% Chance of Leave
                                    $leaveType = (rand(0, 1) === 0) ? 'ลาป่วย' : 'ลากิจ';
                                    $reason = ($leaveType === 'ลาป่วย') ? $sickReasons[array_rand($sickReasons)] : $personalReasons[array_rand($personalReasons)];
                                    $status = ($i > 5) ? 'approved' : 'pending'; // older leaves are approved, recent might be pending
                                    $attachment = ($leaveType === 'ลาป่วย' && rand(0, 1) === 1) ? 'leave_mock_doc.pdf' : null;

                                    $leaveStmt->execute([
                                        ':emp_id' => $empId,
                                        ':type' => $leaveType,
                                        ':date' => $dateStr,
                                        ':end_date' => $dateStr,
                                        ':reason' => $reason,
                                        ':status' => $status,
                                        ':attachment' => $attachment
                                    ]);
                                    echo "  - Employee ID $empId requested Leave ($leaveType) on $dateStr (Status: $status)<br>";
                                } 
                                else {
                                    // 88% Chance of Present
                                    // Clock-in time calculation
                                    $isLate = (rand(0, 100) < 20); // 20% chance of being late
                                    
                                    if ($isLate) {
                                        $hour = rand(8, 9);
                                        $minute = ($hour == 8) ? rand(31, 59) : rand(0, 30);
                                        $status = 'late';
                                    } else {
                                        $hour = 8;
                                        $minute = rand(0, 30);
                                        $status = 'on_time';
                                    }
                                    $second = rand(10, 59);
                                    
                                    $clockInTimeStr = sprintf("%s %02d:%02d:%02d", $dateStr, $hour, $minute, $second);
                                    
                                    // Clock-out time (except ~3% chance they forgot)
                                    $forgotClockOut = (rand(0, 100) < 3);
                                    $clockOutTimeStr = null;
                                    
                                    if (!$forgotClockOut) {
                                        $outHour = rand(17, 18);
                                        $outMinute = rand(0, 59);
                                        $outSecond = rand(0, 59);
                                        $clockOutTimeStr = sprintf("%s %02d:%02d:%02d", $dateStr, $outHour, $outMinute, $outSecond);
                                    }

                                    // GPS Location with minor randomized variations
                                    $latOffset = (rand(-100, 100) / 10000);
                                    $lngOffset = (rand(-100, 100) / 10000);
                                    $empLat = $baseLat + $latOffset;
                                    $empLng = $baseLng + $lngOffset;

                                    $attendanceStmt->execute([
                                        ':emp_id' => $empId,
                                        ':date' => $dateStr,
                                        ':time_in' => $clockInTimeStr,
                                        ':time_out' => $clockOutTimeStr,
                                        ':status' => $status,
                                        ':lat' => $empLat,
                                        ':lng' => $empLng
                                    ]);
                                }
                            }
                        }

                        // Re-enable foreign key constraints
                        $db->exec("SET FOREIGN_KEY_CHECKS = 1");
                        echo "<span class='text-success-custom'>[SUCCESS]</span> Re-enabled database foreign key constraints.<br>";

                        echo "<span class='text-success-custom'>[SUCCESS]</span> Seeded $generatedDaysCount weekdays of attendance & leave history logs.<br>";
                        echo "<hr>";
                        echo "<span class='text-success-custom fw-bold'>[COMPLETE] Seeding completed beautifully! Database is fully populated with mock records.</span>";
                    } catch (PDOException $e) {
                        // Ensure foreign keys are re-enabled even on failure
                        try {
                            $db->exec("SET FOREIGN_KEY_CHECKS = 1");
                        } catch (Exception $ex) {}
                        
                        echo "<span class='text-danger'>[ERROR] PDO Exception: " . $e->getMessage() . "</span><br>";
                    }
                    
                    echo '</div>';
                    echo '<div class="alert alert-success border-0 bg-success-subtle text-success-emphasis rounded-3">';
                    echo '<i class="fas fa-check-circle me-2"></i><strong>Next Steps:</strong> You can now log into the system using the following accounts:';
                    echo '<ul class="mb-0 mt-2">';
                    echo '<li><strong>Admin Portal:</strong> Use username: <code>admin</code> / password: <code>password</code></li>';
                    echo '<li><strong>Employee Dashboard:</strong> Use any employee username: <code>somchai</code>, <code>somsri</code>, <code>john</code>, <code>jane</code>, <code>wichai</code> / password: <code>password</code></li>';
                    echo '</ul>';
                    echo '</div>';
                }
                ?>
            </div>
            
            <div class="text-center text-muted small">
                Smart Attendance & Leave Management System &copy; 2026. Seeder Page.
            </div>
        </div>
    </div>
</div>
</body>
</html>
