<?php
error_reporting(E_ALL);
include '../assets/includes/session.php';
include '../assets/includes/db/dbcon.php';

// Ensure user is logged in
if (!isset($_SESSION['userId'])) {
  header('Location: ../login.php');
  exit;
}

// =============================
// AJAX HANDLER FOR REUPLOAD
// =============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reupload_ajax') {
  header('Content-Type: application/json');
  $response = ['status' => 'error', 'message' => 'Unknown error occurred.'];

  $userId = $_SESSION['userId'];
  $date_uploaded = date('Y-m-d H:i:s');
  $fileId_old = intval($_POST['file_id']);

  // Get user info
  $userQuery = $conn->prepare("SELECT department_id, usertype_id FROM tbl_user WHERE id = ? LIMIT 1");
  $userQuery->bind_param('i', $userId);
  $userQuery->execute();
  $userResult = $userQuery->get_result();
  $userRow = $userResult->fetch_assoc();
  $userDepartmentId = $userRow['department_id'];
  $userType = $userRow['usertype_id'];
  $userQuery->close();

  if (isset($_FILES['reupload_file']) && $_FILES['reupload_file']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $originalFileName = basename($_FILES['reupload_file']['name']);
    $fileExt = pathinfo($originalFileName, PATHINFO_EXTENSION);
    $newFileName = uniqid('file_', true) . '.' . $fileExt;
    $filePath = $uploadDir . $newFileName;

    if (move_uploaded_file($_FILES['reupload_file']['tmp_name'], $filePath)) {
      // Get old filename
      $oldFilename = '';
      $fileQuery = $conn->prepare("SELECT filename FROM tbl_files WHERE id = ? LIMIT 1");
      $fileQuery->bind_param('i', $fileId_old);
      $fileQuery->execute();
      $fileResult = $fileQuery->get_result();
      if ($fileResult && $fileRow = $fileResult->fetch_assoc()) {
        $oldFilename = $fileRow['filename'];
      }
      $fileQuery->close();

      // Insert new file record
      $insertFile = $conn->prepare("INSERT INTO tbl_files (filename, file_path, user_id, date_uploaded) VALUES (?, ?, ?, ?)");
      $insertFile->bind_param('ssis', $originalFileName, $filePath, $userId, $date_uploaded);
      if ($insertFile->execute()) {
        $fileId_new = $insertFile->insert_id;

        // Mark old audit trail as reuploaded
        $updateOld = $conn->prepare("UPDATE tbl_fileaudittrails SET status = 'Reuploaded' WHERE file_id = ? AND status = 'Returned'");
        $updateOld->bind_param('i', $fileId_old);
        $updateOld->execute();
        $updateOld->close();

        // Add new audit entry
        $status = 'Pending';
        $actionType = 'Uploaded';
        $folderId = 1;
        $toDepartmentId = 2;
        $toUsertypeId = 3;
        $remarks = 'File reuploaded (replacing: ' . htmlspecialchars($oldFilename) . ')';

        $insertTrail = $conn->prepare("
          INSERT INTO tbl_fileaudittrails 
          (file_id, user_id, user_department_id, usertype_id, folder_id, status, action_type, to_department_id, to_usertype_id, remarks, time_stamp)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insertTrail->bind_param(
          'iiiisssiiss',
          $fileId_new,
          $userId,
          $userDepartmentId,
          $userType,
          $folderId,
          $status,
          $actionType,
          $toDepartmentId,
          $toUsertypeId,
          $remarks,
          $date_uploaded
        );
        $insertTrail->execute();
        $insertTrail->close();

        $response = ['status' => 'success', 'message' => 'File successfully reuploaded.'];
      } else {
        $response = ['status' => 'error', 'message' => 'Failed to insert new file record.'];
      }
      $insertFile->close();
    } else {
      $response = ['status' => 'error', 'message' => 'File upload failed.'];
    }
  } else {
    $response = ['status' => 'error', 'message' => 'No valid file selected.'];
  }

  echo json_encode($response);
  exit;
}

// =============================
// FETCH TABLE DATA (AJAX)
// =============================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch']) && $_GET['fetch'] === 'returned') {
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
    LEFT JOIN tbl_departments d ON a.user_department_id = d.id
    WHERE a.status = 'Returned'
    ORDER BY a.time_stamp DESC
  ";

  $result = mysqli_query($conn, $query);
  $rows = [];
  $count = 1;

  while ($row = mysqli_fetch_assoc($result)) {
    $fullname = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
    $department = htmlspecialchars($row['department_name']);
    $remarks = htmlspecialchars($row['remarks']);
    $rows[] = [
      $count,
      htmlspecialchars($row['filename']),
      $fullname,
      $department,
      '<span class="badge badge-danger"><i class="fas fa-undo"></i> Returned</span>',
      $remarks,
      htmlspecialchars($row['time_stamp']),
      "<button class='btn btn-outline-primary btn-sm reupload-btn' data-id='{$row['file_id']}' data-filename='" . htmlspecialchars($row['filename']) . "'><i class='fas fa-upload'></i> Reupload</button>"
    ];
    $count++;
  }

  echo json_encode(['data' => $rows]);
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Returned Files</title>
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">
</head>
<body class="layout-3">
<div id="app">
  <div class="main-wrapper container">
    <div class="navbar-bg"></div>
    <?php include '../assets/includes/user_agri/navbar1.php'; ?>
    <?php include '../assets/includes/user/activate/tableactive.php'; ?>

    <div class="main-content">
      <section class="section">
        <div class="section-header"><h1>Returned Files</h1></div>
        <div class="section-body">
          <div id="alert-area"></div>

          <div class="card">
            <div class="card-header"><h4><i class="fas fa-undo text-danger"></i> Returned Files (Pending Reupload)</h4></div>
            <div class="card-body">
              <div class="table-responsive">
                <table id="returnedTable" class="table table-striped table-bordered w-100">
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
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
    <br><br><br><br><br><br><br><br><br><br><br><br>
    <?php include '../assets/includes/user_agri/footer.php'; ?>
  </div>
</div>

<!-- Reupload Modal -->
<div class="modal fade" id="reuploadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-upload"></i> Reupload File</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <form id="reuploadForm" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="action" value="reupload_ajax">
          <input type="hidden" name="file_id" id="modalFileId">
          <div class="form-group">
            <label>Select New File:</label>
            <input type="file" name="reupload_file" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Upload</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="../assets/modules/jquery.min.js"></script>
<script src="../assets/modules/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(function() {
  const table = $('#returnedTable').DataTable({
    ajax: '?fetch=returned',
    columns: [
      { data: 0 }, { data: 1 }, { data: 2 }, { data: 3 },
      { data: 4 }, { data: 5 }, { data: 6 }, { data: 7 }
    ],
    responsive: true,
    ordering: false,
    pageLength: 10
  });

  $(document).on('click', '.reupload-btn', function() {
    $('#modalFileId').val($(this).data('id'));
    $('#reuploadModal .modal-title').text('Reupload File: ' + $(this).data('filename'));
    $('#reuploadModal').modal('show');
  });

  $('#reuploadForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    $.ajax({
      url: '',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success(res) {
        $('#reuploadModal').modal('hide');
        showAlert(res.status, res.message);
        if (res.status === 'success') table.ajax.reload(null, false);
      },
      error() { showAlert('error', 'Upload failed, please try again.'); }
    });
  });

  function showAlert(type, message) {
    const cls = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    $('#alert-area').html(`
      <div class="alert ${cls} alert-dismissible fade show mt-2">
        <i class="fas ${icon}"></i> ${message}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    `);
  }
});
</script>
</body>
</html>
