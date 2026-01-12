<?php
require_once __DIR__ . '/src/models/UserModel.php';
require_once __DIR__ . '/src/database/Database.php';
date_default_timezone_set('Asia/Kolkata');
$session_duration = $_SESSION['exp_session'];
$login_time = strtotime($_SESSION['login_time']); // convert to timestamp
$current_time = time();

$database = new Database();
$pdo = $database->getConnection();
$userModel = new UserModel($pdo);
// Log the logout activity
if (($current_time - $login_time) > $session_duration) {
    $userModel->logActivity($_SESSION['user_id'], 'Session Timeout');
}

if (($current_time - $login_time) > $session_duration) {
    session_unset();
    session_destroy();
    $_SESSION['login_error'] = 'Session Timeout, Please Login Again.';
    header('Location: index.php');
    exit;
}
