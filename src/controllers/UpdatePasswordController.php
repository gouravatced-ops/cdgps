<?php

require_once __DIR__ . '/../database/Database.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$userId = $_SESSION['user_id'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

if ($password !== $confirmPassword) {
    $_SESSION['error'] = 'Passwords do not match';
    header("Location: ../../update-password.php");
    exit;
}

try {
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $database = new Database();
    $pdo = $database->getConnection();

    date_default_timezone_set('Asia/Kolkata');
    $passwordSet = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("UPDATE users SET password = ? , password_set_date = ? WHERE id = ?");

    if ($stmt->execute([$hashedPassword, $passwordSet,  $userId])) {
        $_SESSION['message'] = 'Password updated successfully';
    } else {
        $_SESSION['error'] = 'Error updating password';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
}

header("Location: ../../update-password.php");
exit;
