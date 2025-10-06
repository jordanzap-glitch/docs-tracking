<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'assets/includes/db/dbcon.php';

// Variable to hold error messages
$error_message = "";

if (isset($_POST['login'])) {
    // Get the submitted username/email and password
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check in Super Admin table
    $query_superadmin = "SELECT * FROM superadmin WHERE username = '$username' AND password = '$password'";
    $rs_superadmin = $conn->query($query_superadmin);
    $num_superadmin = $rs_superadmin->num_rows;

    if ($num_superadmin > 0) {
        $rows_superadmin = $rs_superadmin->fetch_assoc();
        $_SESSION['userId'] = $rows_superadmin['id'];
        $_SESSION['fullname'] = $rows_superadmin['fullname'];
        $_SESSION['user_type'] = 'superadmin';

        header('Location:SysAdmin/index.php');
        exit();
    } else {
        // Check in Admin table
        $query_admin = "SELECT * FROM tbl_admin WHERE (email = '$username' OR username = '$username') AND password = '$password'";
        $rs_admin = $conn->query($query_admin);
        $num_admin = $rs_admin->num_rows;

        if ($num_admin > 0) {
            $rows_admin = $rs_admin->fetch_assoc();
            $_SESSION['userId'] = $rows_admin['id'];
            $_SESSION['firstname'] = $rows_admin['firstname'];
            $_SESSION['lastname'] = $rows_admin['lastname'];
            $_SESSION['email'] = $rows_admin['email'];
            $_SESSION['user_type'] = 'admin';

            header('Location:Admin/index.php');
            exit();
        } else {
            // Check in Customer table
            $query_customer = "SELECT * FROM tbl_customer WHERE (email = '$username' OR username = '$username') AND password = '$password'";
            $rs_customer = $conn->query($query_customer);
            $num_customer = $rs_customer->num_rows;

            if ($num_customer > 0) {
                $rows_customer = $rs_customer->fetch_assoc();
                $_SESSION['userId'] = $rows_customer['id'];
                $_SESSION['firstname'] = $rows_customer['firstname'];
                $_SESSION['lastname'] = $rows_customer['lastname'];
                $_SESSION['email'] = $rows_customer['email'];
                $_SESSION['user_type'] = 'customer';

                header('Location:Customer/index.php');
                exit();
            } else {
                // Invalid login
                $error_message = "Invalid Username/Password!";
            }
        }
    }
}

ob_end_flush();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login &mdash; Stisla</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/modules/bootstrap-social/bootstrap-social.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
<!-- Start GA -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-94034622-3');
</script>
<!-- /END GA --></head>

<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
              <img src="assets/img/logo.jpg" alt="logo" width="100" class="shadow-light rounded-circle">
            </div>

            <div class="card card-primary">
              <div class="card-header"><h4>Login</h4></div>

              <div class="card-body">
                <?php if (!empty($error_message)) { ?>
                  <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                  </div>
                <?php } ?>
                
                <form method="POST" action="" class="needs-validation" novalidate="">
                  <div class="form-group">
                    <label for="username">Email / Username</label>
                    <input id="username" type="text" class="form-control" name="username" tabindex="1" required autofocus>
                    <div class="invalid-feedback">
                      Please fill in your email or username
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="d-block">
                    	<label for="password" class="control-label">Password</label>
                      <div class="float-right">
                        <a href="auth-forgot-password.html" class="text-small">

                        </a>
                      </div>
                    </div>
                    <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                    <div class="invalid-feedback">
                      Please fill in your password
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                      <label class="custom-control-label" for="remember-me">Remember Me</label>
                    </div>
                  </div>

                  <div class="form-group">
                    <button type="submit" name="login" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      Login
                    </button>
                  </div>
                </form>
               

              </div>
            </div>
            <div class="mt-5 text-muted text-center">

            </div>
            <div class="simple-footer">

            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- General JS Scripts -->
  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/popper.js"></script>
  <script src="assets/modules/tooltip.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="assets/modules/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>
  
  <!-- JS Libraies -->

  <!-- Page Specific JS File -->
  
  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
</body>
</html>
