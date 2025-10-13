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
      margin-left: 60px;
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
                <?php
                // Get unique files that appear in audit trails
                $filesQuery = mysqli_query($conn, "
                  SELECT DISTINCT f.id AS file_id, f.filename 
                  FROM tbl_fileaudittrails a
                  INNER JOIN tbl_files f ON a.file_id = f.id
                  ORDER BY a.time_stamp DESC
                ");

                if (mysqli_num_rows($filesQuery) > 0) {
                  while ($file = mysqli_fetch_assoc($filesQuery)) {
                    $fileId = $file['file_id'];
                    $fileName = $file['filename'];

                    echo '<div class="card mb-4">
                            <div class="card-header">
                              <h4><i class="fas fa-file-alt"></i> File: ' . htmlspecialchars($fileName) . '</h4>
                            </div>
                            <div class="card-body">
                              <div class="activities">';

                    // Fetch all actions for this file
                    $activitiesQuery = mysqli_query($conn, "
                      SELECT a.*, 
                             u.firstname, u.lastname, 
                             d.department_name, 
                             ut.usertype
                      FROM tbl_fileaudittrails a
                      LEFT JOIN tbl_user u ON a.user_id = u.id
                      LEFT JOIN tbl_departments d ON a.user_department_id = d.id
                      LEFT JOIN tbl_usertype ut ON a.usertype_id = ut.id
                      WHERE a.file_id = '$fileId'
                      ORDER BY a.time_stamp DESC
                    ");

                    while ($activity = mysqli_fetch_assoc($activitiesQuery)) {
                      $fullname = htmlspecialchars($activity['firstname'] . ' ' . $activity['lastname']);
                      $department = htmlspecialchars($activity['department_name']);
                      $usertype = htmlspecialchars($activity['usertype']);
                      $status = htmlspecialchars($activity['status']);
                      $action = htmlspecialchars($activity['action_type']);
                      $remarks = htmlspecialchars($activity['remarks']);
                      $timestamp = date("M d, Y h:i A", strtotime($activity['time_stamp']));

                      // Determine icon based on action type
                      $icon = "fas fa-info-circle";
                      if (stripos($action, 'view') !== false) $icon = "fas fa-eye";
                      elseif (stripos($action, 'approve') !== false) $icon = "fas fa-check";
                      elseif (stripos($action, 'return') !== false) $icon = "fas fa-undo";
                      elseif (stripos($action, 'forward') !== false) $icon = "fas fa-share";
                      elseif (stripos($action, 'upload') !== false) $icon = "fas fa-upload";

                      echo '
                        <div class="activity">
                          <div class="activity-icon bg-primary text-white shadow-primary">
                            <i class="' . $icon . '"></i>
                          </div>
                          <div class="activity-detail">
                            <div class="mb-2">
                              <span class="text-job text-primary">' . $timestamp . '</span>
                              <span class="bullet"></span>
                              <span class="text-job">' . $status . '</span>
                              <div class="float-right dropdown">
                                <a href="#" data-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></a>
                                <div class="dropdown-menu">
                                  <div class="dropdown-title">Options</div>
                                  <a href="#" class="dropdown-item has-icon"><i class="fas fa-eye"></i> View Details</a>
                                  <div class="dropdown-divider"></div>
                                  <a href="#" class="dropdown-item has-icon text-danger"><i class="fas fa-trash-alt"></i> Archive</a>
                                </div>
                              </div>
                            </div>
                            <p><strong>' . $fullname . '</strong> (' . $usertype . ', ' . $department . ') performed <strong>' . $action . '</strong>';

                      if (!empty($remarks)) {
                        echo ' with remarks: "<em>' . $remarks . '</em>"';
                      }

                      echo '.</p>
                          </div>
                        </div>';
                    }

                    echo '</div></div></div>';
                  }
                } else {
                  echo '<div class="alert alert-info">No file activities found.</div>';
                }
                ?>
              </div>

              <!-- Agriculture TAB -->
              <div class="tab-pane fade" id="agri" role="tabpanel" aria-labelledby="agri-tab">
                <p>Department of Agriculture activities will appear here.</p>
              </div>

              <!-- Accounting TAB -->
              <div class="tab-pane fade" id="acct" role="tabpanel" aria-labelledby="acct-tab">
                <p>Department of Accounting activities will appear here.</p>
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














<?php
include '../assets/includes/db/dbcon.php';
?>

<!-- SEARCH BAR -->
<form method="GET" class="mb-4">
  <div class="input-group">
    <input type="text" name="search_filename" class="form-control" placeholder="Search by Filename (Agriculture Department)..." value="<?php echo isset($_GET['search_filename']) ? htmlspecialchars($_GET['search_filename']) : ''; ?>">
    <div class="input-group-append">
      <button class="btn btn-success" type="submit"><i class="fas fa-search"></i> Search</button>
      <button type="button" class="btn btn-info" onclick="window.location.href='index.php'"><i class="fas fa-undo"></i> Reset</button>
    </div>
  </div>
</form>

<?php
$search = "";
if (isset($_GET['search_filename']) && $_GET['search_filename'] != "") {
  $search = mysqli_real_escape_string($conn, $_GET['search_filename']);
  $filesQuery = mysqli_query($conn, "
    SELECT DISTINCT f.id AS file_id, f.filename 
    FROM tbl_fileaudittrails a
    INNER JOIN tbl_files f ON a.file_id = f.id
    WHERE f.filename LIKE '%$search%' AND a.folder_id = 1
    ORDER BY a.time_stamp DESC
  ");
} else {
  $filesQuery = mysqli_query($conn, "
    SELECT DISTINCT f.id AS file_id, f.filename 
    FROM tbl_fileaudittrails a
    INNER JOIN tbl_files f ON a.file_id = f.id
    WHERE a.folder_id = 1
    ORDER BY a.time_stamp DESC
  ");
}

if (mysqli_num_rows($filesQuery) > 0) {
  while ($file = mysqli_fetch_assoc($filesQuery)) {
    $fileId = $file['file_id'];
    $fileName = $file['filename'];

    echo '<div class="card mb-4">
            <div class="card-header bg-primary text-white">
              <h4><i class="fas fa-seedling"></i> ' . htmlspecialchars($fileName) . ' <span class="badge badge-light text-primary">Agriculture Dept.</span></h4>
            </div>
            <div class="card-body">
              <div class="activities">';

    // Fetch all actions for this file, filtered by folder_id = 1
    $activitiesQuery = mysqli_query($conn, "
      SELECT a.*, 
             u.firstname, u.lastname, 
             d.department_name, 
             ut.usertype
      FROM tbl_fileaudittrails a
      LEFT JOIN tbl_user u ON a.user_id = u.id
      LEFT JOIN tbl_departments d ON a.user_department_id = d.id
      LEFT JOIN tbl_usertype ut ON a.usertype_id = ut.id
      WHERE a.file_id = '$fileId' AND a.folder_id = 1
      ORDER BY a.time_stamp DESC
    ");

    while ($activity = mysqli_fetch_assoc($activitiesQuery)) {
      $fullname = htmlspecialchars($activity['firstname'] . ' ' . $activity['lastname']);
      $department = htmlspecialchars($activity['department_name']);
      $usertype = htmlspecialchars($activity['usertype']);
      $status = htmlspecialchars($activity['status']);
      $action = htmlspecialchars($activity['action_type']);
      $remarks = htmlspecialchars($activity['remarks']);
      $timestamp = date("M d, Y h:i A", strtotime($activity['time_stamp']));

      // Determine icon based on action type
      $icon = "fas fa-info-circle";
      $iconColor = "bg-primary";
      if (stripos($action, 'view') !== false) $icon = "fas fa-eye";
      elseif (stripos($action, 'approve') !== false) $icon = "fas fa-check";
      elseif (stripos($action, 'return') !== false) $icon = "fas fa-undo";
      elseif (stripos($action, 'forward') !== false) $icon = "fas fa-share";
      elseif (stripos($action, 'upload') !== false) $icon = "fas fa-upload";

      echo '
        <div class="activity">
          <div class="activity-icon ' . $iconColor . ' text-white shadow-primary">
            <i class="' . $icon . '"></i>
          </div>
          <div class="activity-detail">
            <div class="mb-2">
              <span class="text-job text-success">' . $timestamp . '</span>
              <span class="bullet"></span>
              <span class="text-job">' . $status . '</span>
              <div class="float-right dropdown">
                <a href="#" data-toggle="dropdown"><i class="fas fa-ellipsis-h text-dark"></i></a>
                <div class="dropdown-menu">
                  <div class="dropdown-title">Options</div>
                  <a href="#" class="dropdown-item has-icon"><i class="fas fa-eye"></i> View Details</a>
                  <div class="dropdown-divider"></div>
                  <a href="#" class="dropdown-item has-icon text-danger"><i class="fas fa-trash-alt"></i> Archive</a>
                </div>
              </div>
            </div>
            <p><strong>' . $fullname . '</strong> (' . $usertype . ', ' . $department . ') performed <strong>' . $action . '</strong>';
      if (!empty($remarks)) {
        echo ' with remarks: "<em>' . $remarks . '</em>"';
      }
      echo '.</p>
          </div>
        </div>';
    }

    echo '</div></div></div>';
  }
} else {
  echo '<div class="alert alert-info">No Agriculture Department file activities found.</div>';
}
?>
