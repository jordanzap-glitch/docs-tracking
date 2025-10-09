<?php
error_reporting(E_ALL);
include '../assets/includes/session.php';
include '../assets/includes/db/dbcon.php';

// 1. Ensure the user is authenticated
if (!isset($_SESSION['userId'])) {
    header('Location: ../login.php');
    exit;
}

$statusMsg = "";

// 2. Handle Approve or Return Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['file_id'])) {
    $fileId = intval($_POST['file_id']);
    $action = $_POST['action'];
    $userId = $_SESSION['userId'];
    $date_update = date('Y-m-d H:i:s');

    // Get user department and user type
    $userQuery = $conn->prepare("SELECT department_id, user_type FROM tbl_user WHERE id = ? LIMIT 1");
    $userQuery->bind_param('i', $userId);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    $userRow = $userResult->fetch_assoc();
    $userDepartmentId = $userRow['department_id'];
    $userType = $userRow['user_type'];
    $userQuery->close();

    if ($action === 'approve') {
        $status = 'Approved';
        $insertQuery = $conn->prepare("
            INSERT INTO tbl_fileaudittrails (file_id, user_id, user_department_id, usertype_id, folder_id, status, time_stamp)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $folderId = 2;
        $insertQuery->bind_param('iiiisss', $fileId, $userId, $userDepartmentId, $userType, $folderId, $status, $date_update);
    } elseif ($action === 'return') {
        $status = 'Returned';
        $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
        $folderId = 2;
        $insertQuery = $conn->prepare("
            INSERT INTO tbl_fileaudittrails (file_id, user_id, user_department_id, usertype_id, folder_id, status, remarks, time_stamp)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insertQuery->bind_param('iiiissss', $fileId, $userId, $userDepartmentId, $userType, $folderId, $status, $remarks, $date_update);
    }

    if ($insertQuery->execute()) {
        $statusMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='fas fa-check-circle'></i> File has been <strong>{$status}</strong>.
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                          <span aria-hidden='true'>&times;</span>
                        </button>
                      </div>";
        echo "<meta http-equiv='refresh' content='2;url=" . htmlspecialchars($_SERVER['PHP_SELF']) . "'>";
    } else {
        $statusMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <i class='fas fa-exclamation-triangle'></i> Error: " . htmlspecialchars($conn->error) . "
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                          <span aria-hidden='true'>&times;</span>
                        </button>
                      </div>";
    }

    $insertQuery->close();
}

// 3. Fetch Pending Files (no department filter, user_type = 3, folder_id = 2, status = Pending)
$query = "
  SELECT 
    a.id AS audit_id,
    a.file_id,
    a.user_id,
    a.status,
    a.time_stamp,
    a.remarks,
    a.user_department_id,
    a.usertype_id,
    a.folder_id,
    f.filename,
    f.file_path,
    u.firstname,
    u.lastname,
    d.department_name
  FROM tbl_fileaudittrails a
  LEFT JOIN tbl_files f ON a.file_id = f.id
  LEFT JOIN tbl_user u ON a.user_id = u.id
  LEFT JOIN tbl_departments d ON a.user_department_id = d.id
  WHERE a.usertype_id = 3
    AND a.folder_id = 2
    AND a.status = 'Pending'
  ORDER BY a.time_stamp DESC
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Pending File Approvals — System</title>

  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <?php include '../assets/includes/subadmin_acct/navbar.php'; ?>
      <?php include '../assets/includes/subadmin_acct/activate/approvalsactive.php'; ?>

      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Pending File Approvals</h1>
          </div>

          <div class="section-body">
            <?php if (!empty($statusMsg)) echo $statusMsg; ?>

            <div class="card">
              <div class="card-header">
                <h4><i class="fas fa-clock text-warning"></i> Pending Files (User Type: 3, Folder ID: 2)</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="auditTable" class="table table-striped table-hover table-bordered w-100">
                    <thead class="thead-dark">
                      <tr>
                        <th>#</th>
                        <th>Filename</th>
                        <th>Uploaded By</th>
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
                          $remarks = !empty($row['remarks']) ? htmlspecialchars($row['remarks']) : '<span class="text-muted">—</span>';
                          $statusBadge = '<span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>';

                          echo "<tr>
                                  <td>{$count}</td>
                                  <td>" . htmlspecialchars($row['filename']) . "</td>
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
                        echo "<tr><td colspan='8' class='text-center text-muted'>No pending records found for User Type 3 (Folder ID: 2).</td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <?php include '../assets/includes/subadmin_agri/footer.php'; ?>
    </div>
  </div>

  <script src="../assets/modules/jquery.min.js"></script>
  <script src="../assets/modules/popper.js"></script>
  <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="../assets/modules/moment.min.js"></script>
  <script src="../assets/js/stisla.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
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
