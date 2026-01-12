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
  <link rel="stylesheet" href="./assets/css/login.css">
</head>

<body>
  <div class="login-page">
    <!-- Floating Particles -->
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="login-container">
      <!-- <h2 class="page-title">Admin Panel</h2> -->
      <div class="login-card">
        <div class="row g-0">
          <div class="col-md-7">
            <div class="form-left">
              <div class="glass-form">
                <div class="page-header">
                  <h3>Welcome Back! 👋</h3>
                  <p>Please sign in to access your admin dashboard</p>
                </div>

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
                      <button class="input-group-text btn-primary text-white border-0" type="button" id="togglePassword">
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
              <div class="info-section">
                <h4>Admin Control Panel</h4>
                <p>Manage your resources efficiently with our powerful admin tools</p>
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