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

    public function updateUser(array $data): bool
    {
        try {
            $this->pdo->beginTransaction();
            $now = date('Y-m-d H:i:s');
            $ip  = $_SERVER['REMOTE_ADDR'] ?? null;

            // Convert permissions array to JSON
            $permissions = !empty($data['permission'])
                ? json_encode($data['permission'], JSON_UNESCAPED_UNICODE)
                : json_encode([]);

            // Base SQL
            $sql = "UPDATE users SET
                    email = :email,
                    role = :role,
                    password_expire_in_days = :password_expire_in_days,
                    permission = :permission,
                    updated_by = :updated_by,
                    updated_at = :updated_at,
                    user_ip = :user_ip";

            // Optional fields
            if (!empty($data['username'])) {
                $sql .= ", username = :username";
            }

            if (!empty($data['phone'])) {
                $sql .= ", mobile = :mobile";
            }

            // Password update only if provided
            if (!empty($data['password'])) {
                $sql .= ", password = :password,
                      password_set_date = :password_set_date";
            }

            $sql .= " WHERE id = :user_id AND is_deleted = '0'";
            
            $stmt = $this->pdo->prepare($sql);

            // Required bindings
            $params = [
                ':email' => $data['email'],
                ':username' => $data['username'],
                ':role' => $data['role'],
                ':password_expire_in_days' => $data['password_expire_in_days'],
                ':permission' => $permissions,
                ':updated_by' => $data['update_by'],
                ':updated_at' => $now,
                ':user_ip' => $ip,
                ':user_id' => $data['user_id'],
            ];

            // Optional bindings
            if (!empty($data['username'])) {
                $params[':username'] = $data['username'];
            }

            if (!empty($data['phone'])) {
                $params[':mobile'] = $data['phone'];
            }

            if (!empty($data['password'])) {
                $params[':password'] = $data['password']; // already hashed
                $params[':password_set_date'] = $now;
            }

            $stmt->execute($params);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            // Log this in real projects instead of echo
            echo "<pre>";
            echo "Message: {$e->getMessage()}\n";
            echo "Code: {$e->getCode()}\n";
            print_r($e->errorInfo);
            echo "</pre>";

            return false;
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
