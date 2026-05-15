<?php
/**
 * Smart Database Connection
 * Automatically detects environment and handles fallbacks
 */

class Conn
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: "localhost";
        $this->db_name = getenv('DB_NAME') ?: "attendance_db";
        $this->username = getenv('DB_USER') ?: "root";
        $this->password = getenv('DB_PASSWORD') ?: "";
    }

    private function tryConnect($h)
    {
        try {
            $dsn = "mysql:host=$h;dbname=$this->db_name;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 2, // Fast timeout for quick fallback
            ];
            return new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            return $e;
        }
    }

    public function getConnection()
    {
        $this->conn = null;

        // 1. Try primary host (e.g., 'db' from env)
        $res = $this->tryConnect($this->host);

        if ($res instanceof PDO) {
            $this->conn = $res;
            return $this->conn;
        }

        // 2. Fallback to 127.0.0.1 if primary failed and wasn't already local
        if ($this->host !== 'localhost' && $this->host !== '127.0.0.1') {
            $res = $this->tryConnect('127.0.0.1');
            if ($res instanceof PDO) {
                $this->conn = $res;
                return $this->conn;
            }
        }

        // 3. If everything failed, log and show error
        error_log("All DB connection attempts failed: " . $res->getMessage());
        return null;
    }
}

// Global connection instance for procedural parts of the app
$databaseInstance = new Conn();
$conn = $databaseInstance->getConnection();

if (!$conn) {
    header('Content-Type: text/plain; charset=utf-8');
    die("Error: Could not connect to the database.\n" .
        "Please ensure your MySQL service is running or Docker containers are started.\n" .
        "Command: docker-compose up -d");
}
