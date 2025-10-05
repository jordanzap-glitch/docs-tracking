<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>General Dashboard</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="../assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="../assets/modules/weather-icon/css/weather-icons.min.css">
  <link rel="stylesheet" href="../assets/modules/weather-icon/css/weather-icons-wind.min.css">
  <link rel="stylesheet" href="../assets/modules/summernote/summernote-bs4.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">

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
      <?php include '../../assets/includes/sysadmin/navbar.php'; ?>
      
      <?php include '../../assets/includes/sysadmin/sidebar.php'; ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Dashboard for super admin (change)</h1>
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
      <?php include '../../assets/includes/sysadmin/footer.php'; ?>
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
  
  <!-- JS Libraies -->
  <script src="../assets/modules/simple-weather/jquery.simpleWeather.min.js"></script>
  <script src="../assets/modules/chart.min.js"></script>
  <script src="../assets/modules/jqvmap/dist/jquery.vmap.min.js"></script>
  <script src="../assets/modules/jqvmap/dist/maps/jquery.vmap.world.js"></script>
  <script src="../assets/modules/summernote/summernote-bs4.js"></script>
  <script src="../assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>

  <!-- Page Specific JS File -->
  <script src="../assets/js/page/index-0.js"></script>
  <!-- Template JS File -->
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>
</body>
</html>
