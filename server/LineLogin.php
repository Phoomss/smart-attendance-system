<?php
require_once 'conn.php';
require_once 'user.php';
require_once 'config.php';

class LineLogin
{
    private const CLIENT_ID = LINE_CLIENT_ID;
    private const CLIENT_SECRET = LINE_CLIENT_SECRET;
    private const REDIRECT_URL = LINE_REDIRECT_URL;

    private const AUTH_URL = 'https://access.line.me/oauth2/v2.1/authorize';
    private const PROFILE_URL = 'https://api.line.me/v2/profile';
    private const TOKEN_URL = 'https://api.line.me/oauth2/v2.1/token';
    private const REVOKE_URL = 'https://api.line.me/oauth2/v2.1/revoke';
    private const VERIFYTOKEN_URL = 'https://api.line.me/oauth2/v2.1/verify';

    private function saveUser($profile)
    {
        $database = new Conn();
        $db = $database->getConnection();
        $userModel = new User($db);

        if (empty($profile->email)) {
            error_log('LineLogin Error: Email is required.');
            return;
        }

        // Check if user exists
        $stmt = $db->prepare("SELECT id, employee_code FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $profile->email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            // Update existing user profile info from Line
            $stmt = $db->prepare("UPDATE users SET name = :name, picture = :picture, access_token = :access_token, refresh_token = :refresh_token WHERE email = :email");
            $stmt->execute([
                ':name' => $profile->name,
                ':picture' => $profile->picture,
                ':access_token' => $profile->access_token,
                ':refresh_token' => $profile->refresh_token,
                ':email' => $profile->email
            ]);
        } else {
            // Create new user using User model logic to ensure employee_code is generated
            $userModel->email = $profile->email;
            $userModel->firstname = $profile->name;
            $userModel->username = 'line_' . bin2hex(random_bytes(4));
            $userModel->password = bin2hex(random_bytes(16)); // Random password for Line users
            $userModel->employee_code = 'EMP' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            
            // Manual insert to handle Line-specific fields if needed, or expand User model
            $stmt = $db->prepare("INSERT INTO users (employee_code, name, email, picture, access_token, refresh_token, role) 
                                  VALUES (:code, :name, :email, :pic, :at, :rt, 'employee')");
            $stmt->execute([
                ':code' => $userModel->employee_code,
                ':name' => $profile->name,
                ':email' => $profile->email,
                ':pic' => $profile->picture,
                ':at' => $profile->access_token,
                ':rt' => $profile->refresh_token
            ]);
        }
    }

    public function getLink()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION['state'] = hash('sha256', microtime(TRUE) . rand() . $_SERVER['REMOTE_ADDR']);
        return self::AUTH_URL . '?response_type=code&client_id=' . self::CLIENT_ID . '&redirect_uri=' . urlencode(self::REDIRECT_URL) . '&scope=profile%20openid%20email&state=' . $_SESSION['state'];
    }

    public function token($code, $state)
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (($_SESSION['state'] ?? '') != $state) return false;

        $data = [
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => self::REDIRECT_URL,
            "client_id" => self::CLIENT_ID,
            "client_secret" => self::CLIENT_SECRET
        ];

        $response = $this->sendCURL(self::TOKEN_URL, ['Content-Type: application/x-www-form-urlencoded'], 'POST', $data);
        $token = json_decode($response);

        if (isset($token->id_token)) {
            $profile = $this->profileFormIdToken($token);
            $this->saveUser($profile);
        }
        return $token;
    }

    public function profileFormIdToken($token = null)
    {
        $payload = explode('.', $token->id_token);
        if (count($payload) != 3) return (object)[];

        $data = json_decode(base64_decode($payload[1]));
        return (object) [
            'access_token' => $token->access_token,
            'refresh_token' => $token->refresh_token,
            'name' => $data->name ?? '',
            'picture' => $data->picture ?? '',
            'email' => $data->email ?? ''
        ];
    }

    private function sendCURL($url, $header, $type, $data = NULL)
    {
        $request = curl_init();
        if ($header) curl_setopt($request, CURLOPT_HTTPHEADER, $header);
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

        if (strtoupper($type) === 'POST') {
            curl_setopt($request, CURLOPT_POST, true);
            curl_setopt($request, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response = curl_exec($request);
        curl_close($request);
        return $response;
    }
}
