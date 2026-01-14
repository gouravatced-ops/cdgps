<?php

if (!isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = 'Session Timeout, Please Login Again.';
    header('Location: index.php');
    exit;
}

require './src/database/Database.php';
$database = new Database();
$pdo = $database->getConnection();

$usersRoles = [
    'superadmin' => 'Super Admin',
    'admin' => 'Admin',
    'coadmin' => 'Co-Admin'
];


$stmt = $pdo->prepare("SELECT id, domain_id, username, email, role , permission FROM users WHERE id = ?");
$stmt->execute([(int) $_SESSION['user_id']]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // user not found → force logout
    session_destroy();
    header('Location: /login.php');
    exit;
}

// Map roles
$usersRoles = [
    'superadmin' => 'Super Admin',
    'admin'      => 'Admin',
    'coadmin'    => 'Co-Admin'
];

// Assign values safely
$domainId = (int) $user['domain_id'];
$username = $user['username'];
$email    = $user['email'];
$role     = $usersRoles[$user['role']] ?? 'Admin';
$authRole = $user['role'];
$domainName = getDomainNameById($domainId);
if($domainId > 0){
    $title = $domainName . ' | Dashboard';
} else {
    $title = 'Administration | Dashboard';
}
$permissions = json_decode($user['permission'] ?? '[]', true);

// print_r($permissionJson); die();

function getDomainNameById($domainId)
{
    $database = new Database();
    $pdo = $database->getConnection();
    $stmt = $pdo->prepare("SELECT eng_name FROM domains WHERE id = ?");
    $stmt->execute([$domainId]);
    return $stmt->fetchColumn();
}

function canAccess(string $module, array $permissions , string $role = null): bool
{   
    if ($role === 'superadmin') {
        return true;
    }
    return in_array($module, $permissions ?? [], true); 
}
