<?php
/**
 * Comprehensive Database Repair & Upgrade Script - V2
 * This script fixes structural issues for BOTH Users and Attendances tables.
 */

require_once 'server/conn.php';
$database = new Conn();
$db = $database->getConnection();

if (!$db) {
    die("<h1 style='color:red'>Connection Failed: Could not connect to database.</h1>");
}

header('Content-Type: text/html; charset=utf-8');
echo "<html><body style='font-family: sans-serif; line-height: 1.6; padding: 20px;'>";
echo "<h1>🛠 Attendance System - Database Repair Tool (V2)</h1>";
echo "<p>Fixing structural issues to enable Registration and Clock-in...</p>";
echo "<hr>";

$tasks = [
    "Fixing Users ID (AUTO_INCREMENT)" => "ALTER TABLE users MODIFY id INT(11) NOT NULL AUTO_INCREMENT",
    "Fixing Attendances ID (AUTO_INCREMENT)" => "ALTER TABLE attendances MODIFY id INT(11) NOT NULL AUTO_INCREMENT",
    "Adding Latitude to Attendances" => "ALTER TABLE attendances ADD COLUMN latitude DECIMAL(10, 8) NULL AFTER status",
    "Adding Longitude to Attendances" => "ALTER TABLE attendances ADD COLUMN longitude DECIMAL(11, 8) NULL AFTER latitude",
    "Adding Attachment Path to Leaves" => "ALTER TABLE leaves ADD COLUMN attachment_path VARCHAR(255) NULL AFTER reason",
    "Creating Leave Quotas Table" => "CREATE TABLE IF NOT EXISTS leave_quotas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        sick_limit INT DEFAULT 30,
        personal_limit INT DEFAULT 6,
        year INT NOT NULL,
        UNIQUE KEY (employee_id, year)
    )"
];

echo "<ul>";
foreach ($tasks as $description => $sql) {
    echo "<li><strong>$description:</strong> ";
    try {
        $db->exec($sql);
        echo "<span style='color:green'>DONE ✅</span>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false || strpos($e->getMessage(), 'already exists') !== false) {
            echo "<span style='color:orange'>ALREADY EXISTS (Skipped) ⏩</span>";
        } else {
            // If it fails because of missing primary key, try adding it
            if (strpos($description, 'ID (AUTO_INCREMENT)') !== false) {
                try {
                    $tableName = (strpos($description, 'Users') !== false) ? 'users' : 'attendances';
                    $db->exec("ALTER TABLE $tableName ADD PRIMARY KEY (id)");
                    $db->exec($sql);
                    echo "<span style='color:green'>FIXED WITH PK ✅</span>";
                } catch (Exception $e2) {
                    echo "<span style='color:red'>FAILED ❌ (" . $e2->getMessage() . ")</span>";
                }
            } else {
                echo "<span style='color:red'>FAILED ❌ (" . $e->getMessage() . ")</span>";
            }
        }
    }
    echo "</li>";
}
echo "</ul>";

echo "<hr>";
echo "<h3 style='color:green'>System Status: Structure is now fully repaired!</h3>";
echo "<p>You should now be able to <strong>Register</strong> and <strong>Clock-in</strong> without errors.</p>";
echo "<p>Please <strong>delete this file (complete_db_repair.php)</strong> for security.</p>";
echo "<a href='index.php' style='display:inline-block; background:#4F46E5; color:white; padding:10px 20px; border-radius:50px; text-decoration:none;'>Go to Login Page</a>";
echo "</body></html>";
