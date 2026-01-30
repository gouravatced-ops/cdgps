<?php

require_once __DIR__ . '/../database/Database.php';
session_start();

$userId          = $_SESSION['user_id'] ?? null;
$newPassword     = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';
$userOldPassword = $_POST['useroldpassword'] ?? '';
$page = $_POST['page'] ?? '';

if ($page && $page == 'expirypage') {
    $redirectpath = '../../expire-update-password.php';
} else {
    $redirectpath = '../../update-password.php';
}

if (!$userId) {
    $_SESSION['error'] = 'Unable to process request';
    header("Location: $redirectpath");
    exit;
}

/* New password mismatch */
if ($newPassword !== $confirmPassword) {
    $_SESSION['error'] = 'Unable to update password';
    header("Location: $redirectpath");
    exit;
}


try {
    $database = new Database();
    $pdo = $database->getConnection();

    /* Fetch OLD password hash from DB */
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id AND is_deleted = '0'");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($userOldPassword, $user['password'])) {
        $_SESSION['error'] = 'Incorrect old password. Please try again.';
        header("Location: $redirectpath");
        exit;
    }

    date_default_timezone_set('Asia/Kolkata');
    $passwordSet = date('Y-m-d H:i:s');

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    /* Update new password */
    $stmt = $pdo->prepare("
        UPDATE users 
        SET password = :password,
            password_set_date = :password_set_date
        WHERE id = :id
    ");

    $stmt->execute([
        ':password'          => $hashedPassword,
        ':password_set_date' => $passwordSet,
        ':id'                => $userId
    ]);

    $_SESSION['message'] = 'Password updated successfully';
} catch (PDOException $e) {
    // log internally
    $_SESSION['error'] = 'Unable to update password';
}

header("Location: $redirectpath");
exit;
