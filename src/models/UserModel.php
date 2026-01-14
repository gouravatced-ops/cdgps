<?php

class UserModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        date_default_timezone_set('Asia/Kolkata');
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

    public function create(array $data)
    {
        try {
            // print_r($data); die();
            $this->pdo->beginTransaction();
            $passwordSet = date('Y-m-d H:i:s');
            $ip = $_SERVER['REMOTE_ADDR'];

            // Convert permission array to JSON
            $permissions = json_encode($data['permission'], JSON_UNESCAPED_UNICODE);

            // Insert user
            $sql = "INSERT INTO users (
                        domain_id,
                        username,
                        email,
                        mobile,
                        role,
                        password,
                        password_expire_in_days,
                        password_set_date,
                        permission,
                        created_by,
                        created_at,
                        user_ip
                    ) VALUES (
                        :domain_id,
                        :name,
                        :email,
                        :phone,
                        :role,
                        :password,
                        :password_expire_in_days,
                        :password_set_date,
                        :permission,
                        :created_by,
                        :created_at,
                        :userIp
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':domain_id' => $data['domain_id'],
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':phone' => $data['phone'],
                ':role' => $data['role'],
                ':password' => $data['password'],
                ':password_expire_in_days' => $data['password_expire_in_days'],
                ':password_set_date' => $passwordSet,
                ':permission' => $permissions,
                ':created_by' => $data['created_by'],
                ':created_at' => $passwordSet,
                ':userIp' => $ip
            ]);
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            echo "<pre>";
            echo "Message: " . $e->getMessage() . "\n";
            echo "Code: " . $e->getCode() . "\n";
            print_r($e->errorInfo);
            echo "</pre>";
            die();
        }
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetch();
    }

    public function logActivity($userId, $action)
    {

        if ($action == 'User logged out' || $action == 'Session Timeout') {
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
