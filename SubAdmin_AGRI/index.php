<?php
include '../assets/includes/session.php';

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>General Dashboard</title>

  <?php include '../assets/includes/sysadmin/link.php'; ?>

  <style>
    /* Gradient backgrounds for card icons */
    .gradient-1 {
      background: linear-gradient(45deg, #667eea, #764ba2);
      color: #fff !important;
    }
    .gradient-2 {
      background: linear-gradient(45deg, #ff758c, #ff7eb3);
      color: #fff !important;
    }
    .gradient-3 {
      background: linear-gradient(45deg, #43e97b, #38f9d7);
      color: #fff !important;
    }
    .gradient-4 {
      background: linear-gradient(45deg, #f7971e, #ffd200);
      color: #fff !important;
    }
    .card-statistic-1 {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transition: transform .2s ease;
    }
    .card-statistic-1:hover {
      transform: translateY(-5px);
    }
    .card-statistic-1 .card-icon {
      border-radius: 12px 0 0 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
    }
    .card-statistic-1 .card-wrap {
      padding-left: 10px;
    }
    .card-statistic-1 .card-header h4 {
      font-size: 1rem;
      font-weight: 600;
    }
    .card-statistic-1 .card-body {
      font-size: 1.5rem;
      font-weight: bold;
    }
  </style>

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
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <?php include '../assets/includes/subadmin_agri/navbar.php'; ?>
      
      <?php include '../assets/includes/subadmin_agri/sidebar.php'; ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Department of Agriculture Admin</h1>
          </div>

          <div class="section-body">
            <div class="row">
              <!-- Total Users -->
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                  <div class="card-icon gradient-1">
                    <i class="fas fa-users"></i>
                  </div>
                  <div class="card-wrap">
                    <div class="card-header">
                      <h4>Total Users</h4>
                    </div>
                    <div class="card-body">
                      120
                    </div>
                  </div>
                </div>
              </div>

              <!-- Total Admins -->
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                  <div class="card-icon gradient-2">
                    <i class="fas fa-user-shield"></i>
                  </div>
                  <div class="card-wrap">
                    <div class="card-header">
                      <h4>Total Admins</h4>
                    </div>
                    <div class="card-body">
                      10
                    </div>
                  </div>
                </div>
              </div>

              <!-- Total Files -->
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                  <div class="card-icon gradient-3">
                    <i class="fas fa-folder-open"></i>
                  </div>
                  <div class="card-wrap">
                    <div class="card-header">
                      <h4>Total Files</h4>
                    </div>
                    <div class="card-body">
                      320
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pending Files -->
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                  <div class="card-icon gradient-4">
                    <i class="fas fa-clock"></i>
                  </div>
                  <div class="card-wrap">
                    <div class="card-header">
                      <h4>Pending Files</h4>
                    </div>
                    <div class="card-body">
                      15
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
      <?php include '../assets/includes/subadmin_agri/footer.php'; ?>
    </div>
  </div>


  <?php include '../assets/includes/sysadmin/scripts.php'; ?>
  <!-- General JS Scripts -->
 
</body>
</html>
