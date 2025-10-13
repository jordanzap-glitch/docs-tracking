<?php
include '../assets/includes/session.php';
include '../assets/includes/db/dbcon.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs safely
    $firstname  = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname   = mysqli_real_escape_string($conn, $_POST['lastname']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $username   = mysqli_real_escape_string($conn, $_POST['username']);
    $password   = mysqli_real_escape_string($conn, $_POST['password']);
    $date_created = date("Y-m-d H:i:s");

    // Insert into tbl_admin
    $sql_admin = "INSERT INTO tbl_admin (firstname, lastname, email, department, username, password, status, date_created) 
                  VALUES ('$firstname', '$lastname', '$email', '$department', '$username', '$password','active', '$date_created')";

    // Insert into tbl_user
    $sql_user = "INSERT INTO tbl_usertype (email, username, password, user_type, date_created) 
                 VALUES ('$email', '$username', '$password', 'admin', '$date_created')";

    if (mysqli_query($conn, $sql_admin) && mysqli_query($conn, $sql_user)) {
        echo "<script>alert('Admin added successfully!'); window.location.href='add_admin.php';</script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Add Admin</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">

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
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <?php include '../assets/includes/sysadmin/navbar.php'; ?>
      <?php include '../assets/includes/sysadmin/sidebar.php'; ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Add Admin</h1>
          </div>

          <div class="section-body">
            <div class="card">
              <div class="card-header">
                <h4>Admin Information</h4>
              </div>
              <div class="card-body">
                <form method="POST" action="">
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>First Name</label>
                      <input type="text" name="firstname" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                      <label>Last Name</label>
                      <input type="text" name="lastname" class="form-control" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                  </div>

                  <div class="form-group">
                    <label>Department</label>
                    <select name="department" class="form-control" required>
                      <option value="">-- Select Department --</option>
                      <option value="IT">IT</option>
                      <option value="HR">HR</option>
                      <option value="Finance">Finance</option>
                      <option value="Library">Library</option>
                      <option value="Registrar">Registrar</option>
                    </select>
                  </div>

                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label>Username</label>
                      <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                      <label>Password</label>
                      <input type="password" name="password" class="form-control" required>
                    </div>
                  </div>

                  <button type="submit" class="btn btn-primary">Add Admin</button>
                </form>
              </div>
            </div>
          </div>
        </section>
      </div>

      <?php include '../assets/includes/sysadmin/footer.php'; ?>
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="../assets/modules/jquery.min.js"></script>
  <script src="../assets/modules/popper.js"></script>
  <script src="../assets/modules/tooltip.js"></script>
  <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="../assets/modules/moment.min.js"></script>
  <script src="../assets/js/stisla.js"></script>
  
  <!-- Template JS File -->
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>
</body>
</html>
