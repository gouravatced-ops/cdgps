<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../database/Database.php';
// error_reporting(E_ALL);
//   	ini_set('display_errors', '1');

class LoginController
{
    private $recaptchaSecret = '6Le0XSErAAAAAPDVakBSJTbBnUqaybavXaaNsjwv';
    
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            exit;
        }
        
        session_start();
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        
        // Validate CAPTCHA
        // $captchaResponse = $_POST['g-recaptcha-response'] ?? '';
        // if (!$this->validateCaptcha($captchaResponse)) {
        //     $_SESSION['login_error'] = 'Please complete the CAPTCHA verification.';
        //     header('Location: ../../index.php');
        //     exit;
        // }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['login_error'] = 'Invalid email address.';
            header('Location: ../../index.php');
            exit;
        }
        
        if (empty($password)) {
            $_SESSION['login_error'] = 'Invalid password.';
            header('Location: ../../index.php');
            exit;
        }
        
        $database = new Database();
        $pdo = $database->getConnection();
        $userModel = new UserModel($pdo);
        $user = $userModel->getUserByEmail($email);
        $testHash = password_hash($password, PASSWORD_DEFAULT);
        if ($user && password_verify($password, $testHash)) {
            // Log the successful login activity
            $userModel->logActivity($user['id'], 'User logged in successfully');

            $usersRoles = [
                'superadmin' => 'Super Admin',
                'admin' => 'Admin',
                'coadmin' => 'Co-Admin'
            ];
            
            // Set session variables
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_mail'] = $user['email'];
            $_SESSION['user_name'] = $user['username'];
            $_SESSION['user_role'] = isset($user['role']) ? $usersRoles[$user['role']] : 'Admin'; // Default to Admin
            date_default_timezone_set('Asia/Kolkata');
            $_SESSION['login_time']  = date('Y-m-d H:i:s');
            $_SESSION['exp_session']  = 60 * 60; // Session expiration 15 minutes

            header("Location: ../../dashboard_view.php");
            exit;
        } else {
            // Log failed login attempt if email exists
            if ($user) {
                $userModel->logActivity($user['id'], 'Failed login attempt');
            }
            
            $_SESSION['login_error'] = 'Invalid email or password.';
            header('Location: ../../index.php');
            exit;
        }
    }
    
    /**
     * Validates reCAPTCHA response
     * 
     * @param string $captchaResponse The g-recaptcha-response from the form
     * @return bool Whether the CAPTCHA is valid
     */
    private function validateCaptcha($captchaResponse)
    {
        if (empty($captchaResponse)) {
            return false;
        }
        
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $this->recaptchaSecret,
            'response' => $captchaResponse,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $resultJson = json_decode($result);
        
        return $resultJson->success;
    }
    
}

$controller = new LoginController();
$controller->login();