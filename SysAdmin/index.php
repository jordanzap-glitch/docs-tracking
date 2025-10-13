<?php
include '../assets/includes/db/dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Dashboard — Stisla</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

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
      width: 80px;
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

    /* Activities timeline style */
    .activities {
      position: relative;
      padding-left: 40px;
    }
    .activity {
      position: relative;
      margin-bottom: 20px;
    }
    .activity-icon {
      position: absolute;
      left: 0;
      top: 0;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
    }
    .activity-detail {
      margin-left: 45px;
    }
  </style>
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
          <div class="section-body">
            
            <!-- DASHBOARD CARDS -->
            <div class="row mb-4">
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
                      <?php
                      $countUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_user"));
                      echo $countUsers['total'];
                      ?>
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
                      <?php
                      $countAdmins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_user WHERE usertype_id = 1"));
                      echo $countAdmins['total'];
                      ?>
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
                      <?php
                      $countFiles = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_files"));
                      echo $countFiles['total'];
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- RECENT FILE ACTIVITIES -->
            <h2 class="section-title">Recent File Activities</h2>
            <p class="section-lead">
              This section shows all actions performed on files — including views, approvals, forwards, and returns — grouped by file.
            </p>

            <!-- Department Tabs -->
            <ul class="nav nav-pills mb-3" id="deptTabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="true">
                  <i class="fas fa-globe"></i> All Departments
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="agri-tab" data-toggle="tab" href="#agri" role="tab" aria-controls="agri" aria-selected="false">
                  <i class="fas fa-seedling"></i> Department of Agriculture
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="acct-tab" data-toggle="tab" href="#acct" role="tab" aria-controls="acct" aria-selected="false">
                  <i class="fas fa-calculator"></i> Department of Accounting
                </a>
              </li>
            </ul>

            <div class="tab-content" id="deptTabsContent">
              
              <!-- ALL DEPARTMENTS TAB -->
              <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                <?php include 'alltab.php'; ?>
              </div>

              <!-- AGRICULTURE TAB -->
              <div class="tab-pane fade" id="agri" role="tabpanel" aria-labelledby="agri-tab">
                <?php include 'agriculturetab.php'; ?>
              </div>

              <!-- ACCOUNTING TAB -->
              <div class="tab-pane fade" id="acct" role="tabpanel" aria-labelledby="acct-tab">
                <?php include 'accountingtab.php'; ?>
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
