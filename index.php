<!DOCTYPE html>
<?php 
// Start the session at the very beginning of the file
session_start();

// Check for redirects early
if (isset($_SESSION['user_id'])) {
  header("Location: " . $base_url . "dashboard_view.php");
  exit; // Always exit after a redirect
}
?>
<html lang="en">
<head>
  <title>Admin Panel -- Login</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #6366f1;
      --primary-hover: #4f46e5;
      --secondary: #f9fafb;
      --dark: #1e293b;
      --text-muted: #94a3b8;
      --shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;
      --transition: all 0.3s ease;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    
    body {
      background-color: #f8fafc;
    }
    
    a {
      text-decoration: none;
    }
    
    .login-page {
      width: 100%;
      min-height: 100vh;
      display: flex;
      align-items: center;
      background-size: cover;
      background-position: center;
      position: relative;
      z-index: 1;
      padding: 2rem 0;
    }
    
    .login-page::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(45deg, rgba(17, 24, 39, 0.8), rgba(17, 24, 39, 0.6));
      z-index: -1;
    }
    
    .login-container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 0 1rem;
    }
    
    .login-card {
      background: white;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: var(--shadow);
      transition: var(--transition);
    }
    
    .login-card:hover {
      box-shadow: rgba(0, 0, 0, 0.2) 0px 10px 25px;
      transform: translateY(-5px);
    }
    
    .form-left {
      padding: 3rem;
      background-size: cover;
      background-position: center;
      position: relative;
      /* z-index: 1; */
      background-image: url('assets/images/backgrounds/bg1.jpg');
    }
    
    .form-left::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(8px);
      z-index: -1;
    }
    
    .page-title {
      font-weight: 700;
      color: white;
      margin-bottom: 2rem;
      font-size: 2.5rem;
      text-shadow: 0px 2px 4px rgba(0,0,0,0.3);
    }
    
    .form-group {
      margin-bottom: 1.5rem;
    }
    
    .form-label {
      font-weight: 500;
      color: var(--dark);
      margin-bottom: 0.5rem;
      display: block;
    }
    
    .input-group {
      position: relative;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      transition: var(--transition);
    }
    
    .input-group:focus-within {
      box-shadow: 0 0 0 2px var(--primary);
    }
    
    .input-group-text {
      background-color: white;
      border: none;
      color: var(--text-muted);
      padding-left: 1.25rem;
    }
    
    .form-control {
      border: none;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      background: white;
    }
    
    .form-control:focus {
      box-shadow: none;
      border: none;
    }
    
    .btn-primary {
      background-color: var(--primary);
      border: none;
      padding: 0.75rem 2rem;
      font-weight: 500;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
      transition: var(--transition);
    }
    
    .btn-primary:hover {
      background-color: var(--primary-hover);
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(79, 70, 229, 0.4);
    }
    
    .form-right {
      background-color: var(--secondary);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100%;
      padding: 3rem;
    }
    
    .brand-logo {
      max-width: 85%;
      height: auto;
      border-radius: 12px;
      box-shadow: var(--shadow);
      transition: var(--transition);
    }
    
    .brand-logo:hover {
      transform: scale(1.02);
    }
    
    .alert {
      border-radius: 10px;
      padding: 1rem;
      margin-bottom: 1.5rem;
      border: none;
    }
    
    .g-recaptcha {
      margin: 1rem 0;
    }
    
    /* Glass morphism effect for form content */
    .glass-form {
      background: rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(5px);
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.18);
      margin-bottom: 1rem;
    }
    
    @media (max-width: 768px) {
      .form-left {
        padding: 2rem;
      }
      
      .page-title {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>
  <div class="login-page" style="background-image: url('assets/images/backgrounds/bg.jpg');">
    <div class="login-container">
      <!-- <h2 class="page-title">Admin Panel</h2> -->
      <div class="login-card">
        <div class="row g-0">
          <div class="col-md-7">
            <div class="form-left">
              <div class="glass-form">
                <h3 class="mb-4 text-dark fw-bold">Welcome Back</h3>
                <p class="text-muted mb-4">Please sign in to access your dashboard</p>
                
                <form action="src/controllers/LoginController.php" method="post">
                  <?php
                  if (isset($_SESSION['login_error'])) {
                    echo "<div class='alert alert-danger' role='alert'>"
                      . $_SESSION['login_error'] .
                      "</div>";
                    unset($_SESSION['login_error']);
                  }
                  ?>
                  
                  <div class="form-group">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <div class="input-group-text"><i class="bi bi-person"></i></div>
                      <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label class="form-label d-flex justify-content-between">
                      <span>Password <span class="text-danger">*</span></span>
                      <!-- <a href="#" class="text-primary">Forgot Password?</a> -->
                    </label>
                    <div class="input-group">
                      <div class="input-group-text"><i class="bi bi-lock"></i></div>
                      <input type="password" name="password" class="form-control" id="password" placeholder="Enter your password">
                      <button class="input-group-text bg-transparent border-0" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                      </button>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="6Le0XSErAAAAAHgNYP936uIqoJmd48KfM23yyWMw"></div>
                  </div>
                  
                  <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary w-100">
                      Sign In <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-5 d-none d-md-block">
            <div class="form-right">
              <img src="assets/images/logos/ced_right_mast.jpg" alt="Logo" class="brand-logo">
              <div class="mt-5 text-center">
                <h4 class="mb-3">Admin Control Panel</h4>
                <p class="text-muted">Manage your resources efficiently with our powerful admin tools</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const icon = this.querySelector('i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
  </script>
</body>
</html>