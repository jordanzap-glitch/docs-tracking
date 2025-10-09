<?php
error_reporting(E_ALL);
include '../assets/includes/session.php';
include '../assets/includes/db/dbcon.php';

// 1. Ensure the user is authenticated
if (!isset($_SESSION['userId'])) {
    header('Location: ../login.php');
    exit;
}

// 2. Handle Viewed logging via AJAX
if (isset($_POST['view_file_id'])) {
    $viewFileId = intval($_POST['view_file_id']);
    $userId = $_SESSION['userId'];
    $date_update = date('Y-m-d H:i:s');

    // Get user department and type
    $userQuery = $conn->prepare("SELECT department_id, usertype_id FROM tbl_user WHERE id=? LIMIT 1");
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

    $stmt = $conn->prepare("
        INSERT INTO tbl_fileaudittrails 
        (file_id, user_id, user_department_id, usertype_id, folder_id, status, action_type, to_department_id, to_usertype_id, remarks, time_stamp) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('iiiisssiiss', $viewFileId, $userId, $userDepartmentId, $userType, $folderId, $status, $actionType, $toDepartmentId, $toUsertypeId, $remarks, $date_update);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success'=>true]);
    exit;
}

$statusMsg = "";

// 3. Handle Forward or Return Actions from modal (Updated to match previous forward/return logic)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modal_action'], $_POST['modal_file_id'])) {
    $fileId = intval($_POST['modal_file_id']);
    $action = $_POST['modal_action'];
    $userId = $_SESSION['userId'];
    $date_update = date('Y-m-d H:i:s');

    // Get user department and type
    $userQuery = $conn->prepare("SELECT department_id, usertype_id FROM tbl_user WHERE id = ? LIMIT 1");
    $userQuery->bind_param('i', $userId);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    $userRow = $userResult->fetch_assoc();
    $userDepartmentId = $userRow['department_id'];
    $userType = $userRow['usertype_id'];
    $userQuery->close();

    $folderId = 1;

    if ($action === 'approve') {
        // Forward file logic
        $status = 'Approved';
        $actionType = 'Under Review';
        $toDepartmentId = 1;
        $toUsertypeId = 1;
        $remarks = null;

        $insertQuery = $conn->prepare("
            INSERT INTO tbl_fileaudittrails 
            (file_id, user_id, user_department_id, usertype_id, folder_id, status, action_type, to_department_id, to_usertype_id, remarks, time_stamp)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insertQuery->bind_param('iiiisssiiss', $fileId, $userId, $userDepartmentId, $userType, $folderId, $status, $actionType, $toDepartmentId, $toUsertypeId, $remarks, $date_update);

    } elseif ($action === 'return') {
        // Return file logic
        if (empty(trim($_POST['modal_remarks']))) {
            $statusMsg = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                            <i class='fas fa-info-circle'></i> Please enter remarks before returning the file.
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                              <span aria-hidden='true'>&times;</span>
                            </button>
                          </div>";
        } else {
            $remarks = mysqli_real_escape_string($conn, $_POST['modal_remarks']);
            $status = 'Returned';
            $actionType = 'Returned';
            $toDepartmentId = 9;
            $toUsertypeId = 4;

            $insertQuery = $conn->prepare("
                INSERT INTO tbl_fileaudittrails 
                (file_id, user_id, user_department_id, usertype_id, folder_id, status, action_type, to_department_id, to_usertype_id, remarks, time_stamp)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insertQuery->bind_param('iiiisssiiss', $fileId, $userId, $userDepartmentId, $userType, $folderId, $status, $actionType, $toDepartmentId, $toUsertypeId, $remarks, $date_update);
        }
    }

    if (isset($insertQuery) && $insertQuery->execute()) {
        $statusMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='fas fa-check-circle'></i> File has been <strong>{$status}</strong>.
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                          <span aria-hidden='true'>&times;</span>
                        </button>
                      </div>";
        echo "<meta http-equiv='refresh' content='2;url=" . htmlspecialchars($_SERVER['PHP_SELF']) . "'>";
    } elseif (isset($insertQuery)) {
        $statusMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <i class='fas fa-exclamation-triangle'></i> Error: " . htmlspecialchars($conn->error) . "
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                          <span aria-hidden='true'>&times;</span>
                        </button>
                      </div>";
    }

    if (isset($insertQuery)) $insertQuery->close();
}

// 4. Fetch Forwarded or Pending Files (user_type = 3, folder_id = 1)
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
    AND a.folder_id = 1
    AND a.status IN ('Forwarded', 'Pending')
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

<style>
.modal-xl { max-width: 90% !important; }
.modal-body { background-color: #f0f2f5; padding: 0; }
#pdfViewer { width: 100%; height: 85vh; border: none; }
.pdf-controls { padding: 12px 20px; background: #ffffff; border-top: 1px solid #dee2e6; text-align: right; }
.btn-view, .btn-approve, .btn-return { border-radius: 0.35rem; font-weight: 500; }
.btn-view { background: #007bff; color: #fff; }
.btn-view:hover { background: #0056b3; color: #fff; }
.btn-approve { background: #28a745; color: #fff; }
.btn-approve:hover { background: #218838; }
.btn-return { background: #dc3545; color: #fff; }
.btn-return:hover { background: #c82333; }
</style>
</head>
<body>
<div id="app">
  <div class="main-wrapper main-wrapper-1">
    <div class="navbar-bg"></div>
    <?php include '../assets/includes/subadmin_agri/navbar.php'; ?>
    <?php include '../assets/includes/subadmin_agri/activate/approvalsactive.php'; ?>

    <div class="main-content">
      <section class="section">
        <div class="section-header">
          <h1>Pending File Approvals</h1>
        </div>

        <div class="section-body">
          <?php if (!empty($statusMsg)) echo $statusMsg; ?>

          <div class="card">
            <div class="card-header">
              <h4><i class="fas fa-clock text-warning"></i> Forwarded & Pending Files (User Type: 3, Folder ID: 1)</h4>
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
                          $statusBadge = ($row['status'] === 'Forwarded') 
                            ? '<span class="badge badge-success"><i class="fas fa-share"></i> Forwarded</span>' 
                            : '<span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>';

                          echo "<tr>
                                  <td>{$count}</td>
                                  <td>" . htmlspecialchars($row['filename']) . "</td>
                                  <td>{$fullname}</td>
                                  <td>{$department}</td>
                                  <td>{$statusBadge}</td>
                                  <td>{$remarks}</td>
                                  <td>" . htmlspecialchars($row['time_stamp']) . "</td>
                                  <td>
                                    <button type='button' 
                                            class='btn btn-outline-info btn-sm view-btn' 
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
                      echo "<tr><td colspan='8' class='text-center text-muted'>No forwarded or pending records found (User Type 3, Folder ID: 1).</td></tr>";
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
          <input type="hidden" name="modal_file_id" id="modalFileId">
          <div class="form-group">
            <label for="modal_remarks">Remarks (required if returning file):</label>
            <textarea name="modal_remarks" id="modal_remarks" class="form-control" placeholder="Enter remarks if returning"></textarea>
          </div>
          <div class="text-right">
            <button type="submit" name="modal_action" value="approve" class="btn btn-success btn-approve">
              <i class="fas fa-check"></i> Approve
            </button>
            <button type="submit" name="modal_action" value="return" class="btn btn-danger btn-return">
              <i class="fas fa-undo"></i> Return
            </button>
          </div>
        </form>
      </div>
    </div>
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
    $('#auditTable').DataTable({
        pageLength: 10,
        ordering: false,
        responsive: true
    });

    // View button click
    $('.view-btn').click(function() {
        const filePath = $(this).data('file');
        const fileId = $(this).data('id');
        const fileName = $(this).data('filename');

        // Log viewed action via AJAX
        $.post('', { view_file_id: fileId }, function(response) {
            console.log('Viewed logged', response);
        }, 'json');

        $('#pdfViewer').attr('src', filePath + "#view=FitH");
        $('#pdfModalLabel').text('Viewing: ' + fileName);
        $('#modalFileId').val(fileId);
        $('#pdfModal').modal('show');
    });

    // Prevent return if remarks empty
    $('.btn-return').click(function(e){
        if ($('#modal_remarks').val().trim() === '') {
            e.preventDefault();
            alert('Please enter remarks before returning the file.');
        }
    });
});
</script>
</body>
</html>
