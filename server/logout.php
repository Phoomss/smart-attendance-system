<?php
session_start();
require_once 'LineLogin.php';

// Revoke Line Token if exists
if (isset($_SESSION['profile'])) {
    try {
        $profile = $_SESSION['profile'];
        $line = new LineLogin();
        $line->revoke($profile->access_token);
    } catch (Exception $e) {
        error_log("Logout Error (Line Revoke): " . $e->getMessage());
    }
}

// Clear all session data
$_SESSION = [];

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();

header('Location: ../index.php');
exit();
