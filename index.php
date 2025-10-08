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

    // Check in tbl_user table
    $query_user = "
        SELECT * FROM tbl_user 
        WHERE (email = '$username' OR username = '$username') 
        AND password = '$password'
        LIMIT 1
    ";
    $rs_user = $conn->query($query_user);
    $num_user = $rs_user->num_rows;

    if ($num_user > 0) {
        $rows_user = $rs_user->fetch_assoc();

        $_SESSION['userId'] = $rows_user['id'];
        $_SESSION['firstname'] = $rows_user['firstname'];
        $_SESSION['lastname'] = $rows_user['lastname'];
        $_SESSION['email'] = $rows_user['email'];
        $_SESSION['usertype_id'] = $rows_user['usertype_id'];
        $_SESSION['department_id'] = $rows_user['department_id'];

        // Determine redirect based on usertype_id
        switch ($rows_user['usertype_id']) {
            case 1:
                // Municipal Admin
                header('Location: SysAdmin/index.php');
                break;

            case 2:
                // Department Admin
                if ($rows_user['department_id'] == 1) {
                    header('Location: DepartmentAdmin/MayorsOffice/index.php');
                } elseif ($rows_user['department_id'] == 2) {
                    header('Location: SubAdmin_AGRI/index.php');
                } elseif ($rows_user['department_id'] == 3) {
                    header('Location: SubAdmin_ACCT/index.php');
                } else {
                    header('Location: DepartmentAdmin/index.php');
                }
                break;

            case 3:
                // Department Employee
                if ($rows_user['department_id'] == 1) {
                    header('Location: DepartmentAdmin/MayorsOffice/index.php');
                } elseif ($rows_user['department_id'] == 2) {
                    header('Location: User_AGRI/index.php');
                } elseif ($rows_user['department_id'] == 3) {
                    header('Location: SubAdmin_ACCT/index.php');
                } else {
                    header('Location: DepartmentAdmin/index.php');
                }
                break;

            case 4:
                // Regular Employee
                header('Location: RegularEmployee/index.php');
                break;

            default:
                // Unknown user type
                $error_message = "Invalid user type configuration!";
                break;
        }

        exit();
    } else {
        // Invalid login
        $error_message = "Invalid Username/Password!";
    }
}

ob_end_flush();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login &mdash; User Portal</title>

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
  <!-- /END GA -->
</head>

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
                        <a href="auth-forgot-password.html" class="text-small"></a>
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

            <div class="mt-5 text-muted text-center"></div>
            <div class="simple-footer"></div>
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
  
  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
</body>
</html>
