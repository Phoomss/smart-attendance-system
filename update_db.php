<?php
require_once 'server/conn.php';
$database = new Conn();
$db = $database->getConnection();

if (!$db) die("Connection failed");

try {
    echo "Updating database schema...\n";
    
    // 1. Add GPS columns to attendances
    $db->exec("ALTER TABLE attendances ADD COLUMN latitude DECIMAL(10, 8) NULL AFTER status, ADD COLUMN longitude DECIMAL(11, 8) NULL AFTER latitude");
    echo "- Added GPS columns to attendances table\n";

    // 2. Add attachment column to leaves
    $db->exec("ALTER TABLE leaves ADD COLUMN attachment_path VARCHAR(255) NULL AFTER reason");
    echo "- Added attachment column to leaves table\n";

    // 3. Create leave_quotas table
    $db->exec("CREATE TABLE IF NOT EXISTS leave_quotas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        sick_limit INT DEFAULT 30,
        personal_limit INT DEFAULT 6,
        year INT NOT NULL,
        UNIQUE KEY (employee_id, year)
    )");
    echo "- Created leave_quotas table\n";

    echo "\nSuccess! Database is now up to date.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Note: Some columns already existed. Skipping.\n";
        echo "Success! Database is up to date.";
    } else {
        echo "Error updating database: " . $e->getMessage();
    }
}
