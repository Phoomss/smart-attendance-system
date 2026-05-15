<?php
class Auth
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $employee_code;
    public $title;
    public $firstname;
    public $surname;
    public $username;
    public $email;
    public $password;
    public $role;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function login($identifier, $password)
    {
        try {
            $query = "SELECT id, username, email, role, password FROM " . $this->table_name . "
                      WHERE username = :identifier 
                      OR email = :identifier 
                      OR employee_code = :identifier 
                      LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':identifier', $identifier);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                return [
                    'success' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'data' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ];
            }
            return ['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'];
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาดระหว่างการเข้าสู่ระบบ'];
        }
    }

    public function register()
    {
        try {
            // Check for existing user
            $query = "SELECT id FROM " . $this->table_name . " 
                      WHERE username = :username OR email = :email OR employee_code = :employee_code LIMIT 1";
            $stmt = $this->conn->prepare($query);

            if (empty($this->employee_code)) {
                $this->employee_code = 'EMP' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            }

            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':employee_code', $this->employee_code);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ["success" => false, "message" => "ชื่อผู้ใช้ อีเมล หรือรหัสพนักงานนี้ถูกใช้แล้ว"];
            }

            $query = "INSERT INTO " . $this->table_name . " (employee_code, title, firstname, surname, username, email, password, role) 
                      VALUES (:employee_code, :title, :firstname, :surname, :username, :email, :password, :role)";
            
            $stmt = $this->conn->prepare($query);
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
            $defaultRole = $this->role ?? 'employee';

            $stmt->bindParam(':employee_code', $this->employee_code);
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':firstname', $this->firstname);
            $stmt->bindParam(':surname', $this->surname);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':role', $defaultRole);

            if ($stmt->execute()) {
                return ["success" => true, "message" => "ลงทะเบียนสำเร็จ", "employee_code" => $this->employee_code];
            }
            return ["success" => false, "message" => "ไม่สามารถลงทะเบียนได้"];
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ["success" => false, "message" => "เกิดข้อผิดพลาดในการลงทะเบียน"];
        }
    }
}
