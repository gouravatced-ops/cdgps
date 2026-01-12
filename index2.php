<!DOCTYPE html>
<html lang="en">

<head>
  <title>Admin Panel -- Login</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    a {
      text-decoration: none;
    }

    .login-page {
      width: 100%;
      height: 100vh;
      display: inline-block;
      display: flex;
      align-items: center;
    }

    .form-right i {
      font-size: 100px;
    }
  </style>
</head>

<body>

  <div class="login-page" style="background-image: url('assets/images/backgrounds/bg.jpg');">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 offset-lg-1">
          <h3 class="mb-3 text-light">Login Now</h3>
          <div class="bg-white shadow rounded">
            <div class="row">
              <div class="col-md-7 pe-0" style="background-image: url('assets/images/backgrounds/bg1.jpg');">
                <div class="form-left h-100 py-5 px-5">
                  <form action="src/controllers/LoginController.php" method="post" class="row g-4">

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

                    <div class="col-12">
                      <label>Username<span class="text-danger">*</span></label>
                      <div class="input-group">
                        <div class="input-group-text"><i class="bi bi-person-fill"></i></div>
                        <input type="email" name="email" class="form-control form-control-lg" id="email" aria-describedby="emailHelp">
                      </div>
                    </div>

                    <div class="col-12">
                      <label>Password<span class="text-danger">*</span></label>
                      <div class="input-group">
                        <div class="input-group-text"><i class="bi bi-lock-fill"></i></div>
                        <input type="password" name="password" class="form-control form-control-lg" id="password">
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="inlineFormCheck">
                        <label class="form-check-label" for="inlineFormCheck">Remember me</label>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <a href="#" class="float-end text-primary">Forgot Password?</a>
                    </div>

                    <div class="col-12">
                      <button type="submit" class="btn btn-primary px-4 float-end mt-4">login</button>
                    </div>
                  </form>
                </div>
              </div>
              <div class="col-md-5 ps-0 d-none d-md-block">
                <div class="form-right h-100 pt-5">
                  <img src="assets/images/logos/ced_right_mast.jpg" alt="">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->

</body>

</html>