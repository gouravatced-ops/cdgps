<?php

class UserModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function isEmailRegistered($email)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function registerUser($email, $password)
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        return $stmt->execute();
    }

    public function getUserByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function logActivity($userId, $action)
    {

        if ($action == 'User logged out' || $action == 'Session Timeout') {
            date_default_timezone_set('Asia/Kolkata');
            $loginTime = $_SESSION['login_time'];
            $logoutTime = date('Y-m-d H:i:s');
            $diffInSeconds = strtotime($logoutTime) - strtotime($loginTime);
            $minutes = floor($diffInSeconds / 60);
        } else {
            $minutes = null;
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $this->pdo->prepare("INSERT INTO activity_logs (user_id, ip, action, duration) VALUES (:user_id, :ip, :action, :duration)");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindParam(':action', $action, PDO::PARAM_STR);
        $stmt->bindParam(':duration', $minutes, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
