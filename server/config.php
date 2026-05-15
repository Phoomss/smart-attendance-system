<?php
/**
 * Centralized configuration for the Attendance System
 * Supports environment variables for Docker environments
 */

// Line Login Configuration
define('LINE_CLIENT_ID', getenv('LINE_CLIENT_ID') ?: '2006736200');
define('LINE_CLIENT_SECRET', getenv('LINE_CLIENT_SECRET') ?: '188f5e5c4b1c1300f65de3068aaba7bd');

// Determine correct redirect URL
$default_redirect = 'http://localhost:8080/server/callback.php';
define('LINE_REDIRECT_URL', getenv('LINE_REDIRECT_URL') ?: $default_redirect);

// Database configuration can also be moved here if needed
