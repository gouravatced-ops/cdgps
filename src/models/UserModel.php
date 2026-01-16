<?php

class UserModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        date_default_timezone_set('Asia/Kolkata');
    }

    public function isUserBlocked($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT blocked_until 
            FROM users 
            WHERE id = :user_id 
            AND blocked_until > NOW()
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() !== false;
    }

    public function incrementFailedAttempt($userId)
    {
        $stmt = $this->pdo->prepare("
            UPDATE users 
            SET failed_attempts = failed_attempts + 1,
                last_failed_attempt = NOW()
            WHERE id = :user_id
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Return current attempt count
        $stmt = $this->pdo->prepare("SELECT failed_attempts FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function blockUser($userId, $minutes = 60)
    {
        $blockUntil = date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"));

        $stmt = $this->pdo->prepare("
            UPDATE users 
            SET blocked_until = :block_until
            WHERE id = :user_id
        ");
        $stmt->bindParam(':block_until', $blockUntil);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function resetFailedAttempts($userId)
    {
        $stmt = $this->pdo->prepare("
            UPDATE users 
            SET failed_attempts = 0,
                blocked_until = NULL,
                last_failed_attempt = NULL
            WHERE id = :user_id
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getBlockedTimeRemaining($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT TIMESTAMPDIFF(MINUTE, NOW(), blocked_until) as minutes_remaining
            FROM users 
            WHERE id = :user_id 
            AND blocked_until > NOW()
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    // Add this method to check both email and mobile
    public function isEmailOrMobileExists($email, $mobile = null)
    {
        if ($mobile) {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM users 
                WHERE email = :email OR mobile = :mobile
            ");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':mobile', $mobile);
        } else {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM users 
                WHERE email = :email
            ");
            $stmt->bindParam(':email', $email);
        }

        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getUserByEmail($email)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM users 
            WHERE email = :email 
            AND is_deleted = '0'
        ");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        try {
            // Check if email or mobile already exists
            if ($this->isEmailOrMobileExists($data['email'], $data['phone'])) {
                throw new Exception("Email or mobile number already registered.");
            }

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

    public function logActivity(int $userId, string $action, ?int $minutes = null)
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['HTTP_CLIENT_IP']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '0.0.0.0';

        $sql = "INSERT INTO users_logs (user_id, ip, action, duration)
            VALUES (:user_id, :ip, :action, :duration)";

        if ($action == 'User logged out' || $action == 'Session Timeout') {
            $loginTime = $_SESSION['login_time'];
            $logoutTime = date('Y-m-d H:i:s');
            $diffInSeconds = strtotime($logoutTime) - strtotime($loginTime);
            $minutes = floor($diffInSeconds / 60);
        } else {
            $minutes = null;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id'  => $userId,
            ':ip'       => $ip,
            ':action'   => $action,
            ':duration' => $minutes
        ]);

        // No insert ID needed for logout or timeout
        if (in_array($action, ['User logged out', 'Session Timeout'], true)) {
            return true;
        }

        return (int) $this->pdo->lastInsertId();
    }
}
