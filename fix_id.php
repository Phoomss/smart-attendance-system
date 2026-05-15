<?php
require_once 'server/conn.php';
$database = new Conn();
$db = $database->getConnection();

if (!$db) die("Connection failed");

try {
    echo "Attempting to fix Users table structure...\n";
    
    // 1. Ensure ID is Primary Key (if not already) and set to AUTO_INCREMENT
    // We use a multi-step approach to be safe
    $db->exec("ALTER TABLE users MODIFY id INT(11) NOT NULL AUTO_INCREMENT");
    
    echo "Success! The 'id' column is now set to AUTO_INCREMENT.\n";
    echo "You can now register new users.";
} catch (PDOException $e) {
    echo "Error fixing table: " . $e->getMessage() . "\n";
    echo "Trying alternative fix...\n";
    try {
        $db->exec("ALTER TABLE users ADD PRIMARY KEY (id)");
        $db->exec("ALTER TABLE users MODIFY id INT(11) NOT NULL AUTO_INCREMENT");
        echo "Alternative fix success!";
    } catch (PDOException $e2) {
        echo "Failed: " . $e2->getMessage();
    }
}
