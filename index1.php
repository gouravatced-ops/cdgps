<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RIMS: Admin Login</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/rims_logo.jpg" />
  <link rel="stylesheet" href="assets/css/styles.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background-image: url('assets/images/backgrounds/bg.jpg');">
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-12 col-lg-6 col-xxl-3">
            <div class="card mb-0 w-100" style="background-image: url('assets/images/backgrounds/bg1.jpg');width: 50rem !important">
              <div class="card-body">
                <a href="index.html" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="assets/images/logos/rims_logo.jpg" width="120" alt="">
                </a>
                <form action="src/controllers/LoginController.php" method="POST" id="loginForm">

                  <?php session_start();
                  if (isset($_SESSION['login_error'])) {
                    echo "<div class='alert alert-danger' role='alert'>"
                      . $_SESSION['login_error'] .
                      "</div>";
                    unset($_SESSION['login_error']);
                  }

                  if (isset($_SESSION['user_id'])) {
                    header("Location: " . $base_url . "dashboard_view.php");
                  }

                  ?>

                  <div class="mb-3">
                    <label for="email" class="form-label">Username</label>
                    <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp">
                  </div>
                  <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password">
                  </div>
                  
                  <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Login</button>
                  <!-- <a href="index.html" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign In</a> -->
                  <div class="d-flex align-items-center justify-content-center">
					  Technology Partner <img src="assets/images/logos/insta-logo.jpg" width='50px' alt="ComputerEd"> <b>ComputerEd</b>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
