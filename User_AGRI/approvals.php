<?php
error_reporting(E_ALL);
include '../assets/includes/session.php';
include '../assets/includes/db/dbcon.php';

// Ensure user is logged in
if (!isset($_SESSION['userId'])) {
  header('Location: ../login.php');
  exit;
}

$statusMsg = "";

// =======================================================
// HANDLE FORWARD / RETURN ACTIONS
// =======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['file_id'])) {
  $fileId = intval($_POST['file_id']);
  $action = $_POST['action'];
  $userId = $_SESSION['userId'];

  // Get user details
  $userQuery = $conn->prepare("SELECT department_id, usertype_id FROM tbl_user WHERE id = ? LIMIT 1");
  $userQuery->bind_param('i', $userId);
  $userQuery->execute();
  $userResult = $userQuery->get_result();
  $userRow = $userResult->fetch_assoc();
  $userDepartmentId = $userRow['department_id'];
  $userType = $userRow['usertype_id'];
  $userQuery->close();

  $folderId = 1;

  // ----------------------------------
  // Forward File (Updated Logic)
  // ----------------------------------
  if ($action === 'forward') {
    $status = 'Forwarded';
    $actionType = 'Under Review';
    $toDepartmentId = 2;
    $toUsertypeId = 2;
    $remarks = null;

    $insertQuery = $conn->prepare("
      INSERT INTO tbl_fileaudittrails 
      (file_id, user_id, user_department_id, usertype_id, folder_id, status, action_type, to_department_id, to_usertype_id, remarks, time_stamp)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $insertQuery->bind_param(
      'iiiisssiis',
      $fileId,
      $userId,
      $userDepartmentId,
      $userType,
      $folderId,
      $status,
      $actionType,
      $toDepartmentId,
      $toUsertypeId,
      $remarks
    );

    if ($insertQuery->execute()) {
      $statusMsg = "<div class='alert alert-success alert-dismissible fade show'>
                      <i class='fas fa-check-circle'></i> File forwarded successfully.
                      <button type='button' class='close' data-dismiss='alert'>&times;</button>
                    </div>";
    } else {
      $statusMsg = "<div class='alert alert-danger alert-dismissible fade show'>
                      <i class='fas fa-exclamation-triangle'></i> Error forwarding file: " . htmlspecialchars($conn->error) . "
                      <button type='button' class='close' data-dismiss='alert'>&times;</button>
                    </div>";
    }
    $insertQuery->close();
  }

  // ----------------------------------
  // Return File
  // ----------------------------------
  elseif ($action === 'return') {
    if (empty(trim($_POST['remarks']))) {
      $statusMsg = "<div class='alert alert-warning alert-dismissible fade show'>
                      <i class='fas fa-info-circle'></i> Please enter remarks before returning a file.
                      <button type='button' class='close' data-dismiss='alert'>&times;</button>
                    </div>";
    } else {
      $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
      $status = 'Returned';
      $actionType = 'Returned';
      $toDepartmentId = 9;
      $toUsertypeId = 4;

      $insertQuery = $conn->prepare("
        INSERT INTO tbl_fileaudittrails 
        (file_id, user_id, user_department_id, usertype_id, folder_id, status, action_type, to_department_id, to_usertype_id, remarks, time_stamp)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
      ");
      $insertQuery->bind_param(
        'iiiisssiis',
        $fileId,
        $userId,
        $userDepartmentId,
        $userType,
        $folderId,
        $status,
        $actionType,
        $toDepartmentId,
        $toUsertypeId,
        $remarks
      );

      if ($insertQuery->execute()) {
        $statusMsg = "<div class='alert alert-success alert-dismissible fade show'>
                        <i class='fas fa-check-circle'></i> File returned successfully.
                        <button type='button' class='close' data-dismiss='alert'>&times;</button>
                      </div>";
      } else {
        $statusMsg = "<div class='alert alert-danger alert-dismissible fade show'>
                        <i class='fas fa-exclamation-triangle'></i> Error returning file: " . htmlspecialchars($conn->error) . "
                        <button type='button' class='close' data-dismiss='alert'>&times;</button>
                      </div>";
      }
      $insertQuery->close();
    }
  }
}

// =======================================================
// HANDLE "VIEWED" ACTION (AJAX)
// =======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['viewed_file_id'])) {
  $fileId = intval($_POST['viewed_file_id']);
  $userId = $_SESSION['userId'];

  $userQuery = $conn->prepare("SELECT department_id, usertype_id FROM tbl_user WHERE id = ? LIMIT 1");
  $userQuery->bind_param('i', $userId);
  $userQuery->execute();
  $userResult = $userQuery->get_result();
  $userRow = $userResult->fetch_assoc();
  $userDepartmentId = $userRow['department_id'];
  $userType = $userRow['usertype_id'];
  $userQuery->close();

  $folderId = 1;
  $status = 'Viewed';
  $actionType = 'Under Review';
  $toDepartmentId = 0;
  $toUsertypeId = 0;
  $remarks = null;

  $insertQuery = $conn->prepare("
    INSERT INTO tbl_fileaudittrails 
    (file_id, user_id, user_department_id, usertype_id, folder_id, status, action_type, to_department_id, to_usertype_id, remarks, time_stamp)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
  ");
  $insertQuery->bind_param(
    'iiiisssiis',
    $fileId,
    $userId,
    $userDepartmentId,
    $userType,
    $folderId,
    $status,
    $actionType,
    $toDepartmentId,
    $toUsertypeId,
    $remarks
  );
  $insertQuery->execute();
  $insertQuery->close();

  echo json_encode(['success' => true]);
  exit;
}

// =======================================================
// FETCH PENDING FILES — ONLY SHOW LATEST STATUS = 'Pending'
// =======================================================
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
  INNER JOIN (
      SELECT file_id, MAX(id) AS max_id
      FROM tbl_fileaudittrails
      GROUP BY file_id
  ) latest ON a.id = latest.max_id
  LEFT JOIN tbl_files f ON a.file_id = f.id
  LEFT JOIN tbl_user u ON a.user_id = u.id
  LEFT JOIN tbl_departments d ON a.user_department_id = d.id
  WHERE a.usertype_id = 4
    AND a.folder_id = 1
    AND a.status = 'Pending'
  ORDER BY a.time_stamp DESC
";
$result = mysqli_query($conn, $query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
  <title>Department of Agriculture — Pending File Approvals</title>

  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">

  <style>
    .modal-xl { max-width: 80% !important; }
    .modal-body { padding: 0 !important; background-color: #f4f6f9; }
    #pdfViewer { display: block; width: 100%; height: 90vh; border: none; }
    .pdf-controls { padding: 15px; background: #f8f9fa; border-top: 1px solid #dee2e6; }
  </style>
</head>

<body class="layout-3">
  <div id="app">
    <div class="main-wrapper container">
      <div class="navbar-bg"></div>
      <?php include '../assets/includes/user_agri/navbar1.php'; ?>
      <?php include '../assets/includes/user_agri/activate/approvalsactive.php'; ?>

      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Pending File Approvals — Department of Agriculture</h1>
          </div>

          <div class="section-body">
            <?php if (!empty($statusMsg)) echo $statusMsg; ?>

            <div class="card">
              <div class="card-header">
                <h4><i class="fas fa-clock text-warning"></i> Pending Files (User Type: 4, Folder ID: 1)</h4>
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
                    <tbody id="auditTableBody">
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
                                    <button class='btn btn-outline-info btn-sm view-btn' 
                                            data-file='" . htmlspecialchars($row['file_path']) . "' 
                                            data-id='{$row['file_id']}' 
                                            data-filename='" . htmlspecialchars($row['filename']) . "'>
                                      <i class='fas fa-eye'></i> View
                                    </button>
                                  </td>
                                </tr>";
                          $count++;
                        }
                      } else {
                        echo "<tr><td colspan='8' class='text-center text-muted'>No pending records found.</td></tr>";
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

      <?php include '../assets/includes/user_agri/footer.php'; ?>
    </div>
  </div>

  <!-- PDF Modal -->
  <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="pdfModalLabel"><i class="fas fa-file-pdf"></i> View PDF</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <iframe id="pdfViewer" src=""></iframe>
        </div>
        <div class="pdf-controls">
          <form method="post" id="actionForm">
            <input type="hidden" name="file_id" id="modalFileId">
            <div class="form-group">
              <label for="remarks">Remarks (required if returning file):</label>
              <textarea name="remarks" id="remarks" class="form-control" placeholder="Enter remarks if returning"></textarea>
            </div>
            <div class="text-right">
              <button type="submit" name="action" value="forward" class="btn btn-success">
                <i class="fas fa-share-square"></i> Forward
              </button>
              <button type="submit" name="action" value="return" class="btn btn-danger" id="returnBtn">
                <i class="fas fa-undo"></i> Return
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="../assets/modules/jquery.min.js"></script>
  <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="../assets/modules/moment.min.js"></script>
  <script src="../assets/js/stisla.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>

  <script>
    $(document).ready(function() {
      $('#auditTable').DataTable({
        pageLength: 10,
        ordering: false,
        responsive: true
      });

      // Prevent return if no remarks
      $('#returnBtn').on('click', function(e) {
        if ($('#remarks').val().trim() === '') {
          e.preventDefault();
          alert('Please enter remarks before returning the file.');
        }
      });

      // When user clicks "View"
      $('.view-btn').click(function() {
        const filePath = $(this).data('file');
        const fileId = $(this).data('id');
        const fileName = $(this).data('filename');

        // Log view action
        $.post('', { viewed_file_id: fileId }, function(response) {
          console.log('View logged:', response);
        }, 'json');

        // Show PDF modal
        $('#pdfViewer').attr('src', filePath + "#view=FitH");
        $('#pdfModalLabel').text('Viewing: ' + fileName);
        $('#modalFileId').val(fileId);
        $('#pdfModal').modal('show');
      });
    });
  </script>
  
</body>
</html>
