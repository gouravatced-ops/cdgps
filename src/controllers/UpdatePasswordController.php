<?php

require_once __DIR__ . '/../database/Database.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /cdrms");
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

    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");

    if ($stmt->execute([$hashedPassword, $userId])) {
        $_SESSION['message'] = 'Password updated successfully';
    } else {
        $_SESSION['error'] = 'Error updating password';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
}

header("Location: ../../update-password.php");
exit;
