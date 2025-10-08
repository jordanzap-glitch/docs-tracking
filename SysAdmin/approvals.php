<?php
error_reporting(E_ALL);
include '../assets/includes/session.php';
include '../assets/includes/db/dbcon.php';

$statusMsg = "";

// Handle Approve or Return Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['file_id'])) {
    $fileId = intval($_POST['file_id']);
    $action = $_POST['action'];
    $userId = $_SESSION['userId'];
    $date_update = date('Y-m-d H:i:s');

    // Get department of the current user
    $deptQuery = "SELECT department_id FROM tbl_user WHERE id='$userId' LIMIT 1";
    $deptResult = mysqli_query($conn, $deptQuery);
    $departmentId = null;
    if ($deptResult && mysqli_num_rows($deptResult) > 0) {
        $deptRow = mysqli_fetch_assoc($deptResult);
        $departmentId = $deptRow['department_id'];
    }

    if ($action === 'approve') {
        $status = 'Approved';
        // Insert into audit trail with department
        $insertQuery = "INSERT INTO tbl_fileaudittrails (file_id, user_id, status, time_stamp, department_id) 
                        VALUES ('$fileId', '$userId', '$status', '$date_update', '$departmentId')";
        if (mysqli_query($conn, $insertQuery)) {
            $statusMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <i class='fas fa-check-circle'></i> File has been <strong>Approved</strong>.
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                              <span aria-hidden='true'>&times;</span>
                            </button>
                          </div>";
            echo "<meta http-equiv='refresh' content='2;url=" . htmlspecialchars($_SERVER['PHP_SELF']) . "'>";
        } else {
            $statusMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            <i class='fas fa-exclamation-triangle'></i> Error inserting audit trail: " . mysqli_error($conn) . "
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                              <span aria-hidden='true'>&times;</span>
                            </button>
                          </div>";
        }
    } elseif ($action === 'return') {
        $status = 'Returned';
        $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
        $insertQuery = "INSERT INTO tbl_fileaudittrails (file_id, user_id, status, time_stamp, remarks) 
                        VALUES ('$fileId', '$userId', '$status', '$date_update', '$remarks')";
        if (mysqli_query($conn, $insertQuery)) {
            $statusMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <i class='fas fa-undo'></i> File has been <strong>Returned</strong>.
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                              <span aria-hidden='true'>&times;</span>
                            </button>
                          </div>";
            echo "<meta http-equiv='refresh' content='2;url=" . htmlspecialchars($_SERVER['PHP_SELF']) . "'>";
        } else {
            $statusMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            <i class='fas fa-exclamation-triangle'></i> Error inserting audit trail: " . mysqli_error($conn) . "
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                              <span aria-hidden='true'>&times;</span>
                            </button>
                          </div>";
        }
    }
}

// Fetch Audit Trail Information with Department
$query = "
  SELECT 
    a.id AS audit_id,
    a.file_id,
    a.user_id,
    a.status,
    a.time_stamp,
    a.remarks,
    f.filename,
    f.file_path,
    u.firstname,
    u.lastname,
    d.department_name
  FROM tbl_fileaudittrails a
  LEFT JOIN tbl_files f ON a.file_id = f.id
  LEFT JOIN tbl_user u ON a.user_id = u.id
  LEFT JOIN tbl_departments d ON u.department_id = d.id
  ORDER BY a.time_stamp DESC
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>File Audit Trail &mdash; Stisla</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <?php include '../assets/includes/sysadmin/navbar.php'; ?>
      <?php include '../assets/includes/sysadmin/activate/approvalsactive.php'; ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>File Audit Trail</h1>
          </div>

          <div class="section-body">
            <?php if (!empty($statusMsg)) echo $statusMsg; ?>

            <div class="card">
              <div class="card-header">
                <h4><i class="fas fa-history text-info"></i> Audit Trail Records</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="auditTable" class="table table-striped table-hover table-bordered w-100">
                    <thead class="thead-dark">
                      <tr>
                        <th>#</th>
                        <th>Filename</th>
                        <th>File Path</th>
                        <th>Current Holder</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Timestamp</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (mysqli_num_rows($result) > 0) {
                        $count = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                          $fullname = !empty($row['firstname']) ? htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) : 'Unknown';
                          $department = !empty($row['department_name']) ? htmlspecialchars($row['department_name']) : '<span class="text-muted">—</span>';
                          $fileLink = "<a href='" . htmlspecialchars($row['file_path']) . "' target='_blank'><i class='fas fa-eye'></i> View</a>";
                          $remarks = !empty($row['remarks']) ? htmlspecialchars($row['remarks']) : '<span class="text-muted">—</span>';

                          $statusBadge = '';
                          switch(strtolower($row['status'])) {
                            case 'approved':
                              $statusBadge = '<span class="badge badge-success"><i class="fas fa-check"></i> Approved</span>';
                              break;
                            case 'returned':
                              $statusBadge = '<span class="badge badge-danger"><i class="fas fa-undo"></i> Returned</span>';
                              break;
                            case 'pending':
                            default:
                              $statusBadge = '<span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>';
                              break;
                          }

                          echo "<tr>
                                  <td>{$count}</td>
                                  <td>" . htmlspecialchars($row['filename']) . "</td>
                                  <td>{$fileLink}</td>
                                  <td>{$fullname}</td>
                                  <td>{$department}</td>
                                  <td>{$statusBadge}</td>
                                  <td>{$remarks}</td>
                                  <td>" . htmlspecialchars($row['time_stamp']) . "</td>
                                  <td>
                                    <form method='post' class='d-inline'>
                                      <input type='hidden' name='file_id' value='{$row['file_id']}'>
                                      <input type='hidden' name='action' value='approve'>
                                      <button type='submit' class='btn btn-outline-success btn-sm' title='Approve'>
                                        <i class='fas fa-check'></i>
                                      </button>
                                    </form>
                                    <a href='" . htmlspecialchars($row['file_path']) . "' download class='btn btn-outline-primary btn-sm' title='Download'>
                                      <i class='fas fa-download'></i>
                                    </a>
                                  </td>
                                </tr>";
                          $count++;
                        }
                      } else {
                        echo "<tr><td colspan='9' class='text-center text-muted'>No audit trail records found.</td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div> <!-- /.table-responsive -->
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
  <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="../assets/modules/moment.min.js"></script>
  <script src="../assets/js/stisla.js"></script>
  
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

  <!-- Template JS File -->
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>

  <script>
    $(document).ready(function () {
      $.fn.dataTable.ext.errMode = 'none';
      $('#auditTable').DataTable({
          "pageLength": 10,
          "lengthMenu": [5, 10, 25, 50, 100],
          "ordering": false,
          "searching": true,
          "scrollY": "400px",
          "scrollCollapse": true,
          "paging": true,
          "responsive": true
      });
    });
  </script>
</body>
</html>
