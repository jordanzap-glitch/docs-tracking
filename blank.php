<?php
include '../assets/includes/db/dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>File Activities &mdash; Stisla</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">
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
            <h1>File Activity Logs</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
              <div class="breadcrumb-item">Activities</div>
            </div>
          </div>

          <div class="section-body">
            <h2 class="section-title">Recent File Activities</h2>
            <p class="section-lead">
              This page shows all actions performed on files — including views, approvals, and returns — with user, department, and timestamp details.
            </p>

            <div class="row">
              <div class="col-12">
                <div class="activities">

<?php
// Query to fetch activity logs joined with related data
$query = "
SELECT 
    fa.id AS audit_id,
    fa.files_id,
    fa.user_id,
    fa.user_department_id,
    fa.usertype_id,
    fa.status,
    fa.action_type,
    fa.time_stamp,
    f.filename,
    u.firstname,
    u.lastname,
    d.department_name,
    ut.usertype_name
FROM tbl_fileaudittrails fa
LEFT JOIN tbl_files f ON fa.files_id = f.id
LEFT JOIN tbl_user u ON fa.user_id = u.id
LEFT JOIN tbl_departments d ON fa.user_department_id = d.id
LEFT JOIN tbl_usertype ut ON fa.usertype_id = ut.id
ORDER BY fa.time_stamp DESC
";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $fullname = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
    $department = htmlspecialchars($row['department_name']);
    $filename = htmlspecialchars($row['filename']);
    $action = htmlspecialchars($row['action_type']);
    $status = htmlspecialchars($row['status']);
    $timestamp = date("M d, Y h:i A", strtotime($row['time_stamp']));
    $usertype = htmlspecialchars($row['usertype_name']);

    // Choose icon based on action type
    $iconClass = "fas fa-file";
    $iconBg = "bg-primary";

    if (stripos($action, 'view') !== false) {
      $iconClass = "fas fa-eye";
      $iconBg = "bg-info";
    } elseif (stripos($action, 'approve') !== false) {
      $iconClass = "fas fa-check";
      $iconBg = "bg-success";
    } elseif (stripos($action, 'return') !== false) {
      $iconClass = "fas fa-undo";
      $iconBg = "bg-warning";
    } elseif (stripos($action, 'upload') !== false) {
      $iconClass = "fas fa-upload";
      $iconBg = "bg-primary";
    } elseif (stripos($action, 'forward') !== false) {
      $iconClass = "fas fa-share";
      $iconBg = "bg-secondary";
    }

    echo '
      <div class="activity">
        <div class="activity-icon ' . $iconBg . ' text-white shadow-' . $iconBg . '">
          <i class="' . $iconClass . '"></i>
        </div>
        <div class="activity-detail">
          <div class="mb-2">
            <span class="text-job text-primary">' . $timestamp . '</span>
            <span class="bullet"></span>
            <a class="text-job" href="#">' . ucfirst($action) . '</a>
            <div class="float-right dropdown">
              <a href="#" data-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></a>
              <div class="dropdown-menu">
                <div class="dropdown-title">Options</div>
                <a href="#" class="dropdown-item has-icon"><i class="fas fa-eye"></i> View File</a>
                <a href="#" class="dropdown-item has-icon"><i class="fas fa-user"></i> User Info</a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item has-icon text-danger" data-confirm="Archive this log?|This action cannot be undone." data-confirm-text-yes="Yes, Archive"><i class="fas fa-trash-alt"></i> Archive</a>
              </div>
            </div>
          </div>
          <p>
            <strong>' . $fullname . '</strong> (' . $usertype . ', ' . $department . ') 
            ' . strtolower($action) . ' the file 
            "<a href="#">' . $filename . '</a>"
            with status <b>' . $status . '</b>.
          </p>
        </div>
      </div>
    ';
  }
} else {
  echo '
    <div class="text-center p-4">
      <h6 class="text-muted">No activity logs found.</h6>
    </div>
  ';
}
?>

                </div>
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
