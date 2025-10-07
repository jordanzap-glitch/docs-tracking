<?php
include '../assets/includes/session.php';
include '../assets/includes/db/dbcon.php';

$statusMsg = "";

// Handle Approve or Return Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['file_id'])) {
    $fileId = intval($_POST['file_id']);
    $action = $_POST['action'];
    $userId = $_SESSION['userId'];
    $status = '';
    $remarks = '';
    $date_update = date('Y-m-d H:i:s');

    if ($action === 'approve') {
        $status = 'Approved';
    } elseif ($action === 'return') {
        $status = 'Returned';
        $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
    }

    if (!empty($status)) {
        $updateQuery = "
            UPDATE tbl_files 
            SET 
                status = '$status', 
                check_by_id = '$userId',
                date_update = '$date_update',
                remarks = '$remarks'
            WHERE id = $fileId
        ";

        if (mysqli_query($conn, $updateQuery)) {
            $statusMsg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <i class='fas fa-check-circle'></i> File has been successfully <strong>$status</strong>.
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                              <span aria-hidden='true'>&times;</span>
                            </button>
                          </div>";
            echo "<meta http-equiv='refresh' content='2;url=" . htmlspecialchars($_SERVER['PHP_SELF']) . "'>";
        } else {
            $statusMsg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            <i class='fas fa-exclamation-triangle'></i> Error updating file status: " . mysqli_error($conn) . "
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                              <span aria-hidden='true'>&times;</span>
                            </button>
                          </div>";
            echo "<meta http-equiv='refresh' content='2;url=" . htmlspecialchars($_SERVER['PHP_SELF']) . "'>";
        }
    }
}

// Fetch only PENDING files
$query = "
  SELECT 
    f.id,
    f.filename,
    f.file_path,
    f.location,
    f.user_id,
    f.status,
    f.date_submit,
    f.date_update,
    f.check_by_id,
    f.remarks,
    u.firstname,
    u.lastname,
    u2.firstname AS check_firstname,
    u2.lastname AS check_lastname
  FROM tbl_files f
  LEFT JOIN tbl_user u ON f.user_id = u.id
  LEFT JOIN tbl_user u2 ON f.check_by_id = u2.id
  WHERE f.status = 'Pending'
  ORDER BY f.date_submit DESC
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Pending Files &mdash; Stisla</title>

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
            <h1>Pending Documents for Review</h1>
          </div>

          <div class="section-body">
            <?php if (!empty($statusMsg)) echo $statusMsg; ?>

            <div class="card">
              <div class="card-header">
                <h4><i class="fas fa-clock text-warning"></i> List of Pending Files</h4>
              </div>
              <div class="card-body table-responsive">
                <table id="fileTable" class="table table-striped table-hover">
                  <thead class="thead-dark">
                    <tr>
                      <th>#</th>
                      <th>Filename</th>
                      <th>File Path</th>
                      <th>Location</th>
                      <th>Uploaded By</th>
                      <th>Checked By</th>
                      <th>Status</th>
                      <th>Remarks</th>
                      <th>Date Submitted</th>
                      <th>Last Updated</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                      $count = 1;
                      while ($row = mysqli_fetch_assoc($result)) {
                        $statusBadge = '<span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>';

                        $fullname = !empty($row['firstname']) ? htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) : 'Unknown User';
                        $checkedBy = !empty($row['check_firstname']) ? htmlspecialchars($row['check_firstname'] . ' ' . $row['check_lastname']) : '<span class="text-muted">Not yet checked</span>';
                        $remarks = !empty($row['remarks']) ? htmlspecialchars($row['remarks']) : '<span class="text-muted">—</span>';
                        $viewLink = "<a href='" . htmlspecialchars($row['file_path']) . "' target='_blank'><i class='fas fa-eye'></i> View</a>";

                        echo "<tr>
                                <td>{$count}</td>
                                <td>" . htmlspecialchars($row['filename']) . "</td>
                                <td>{$viewLink}</td>
                                <td>" . htmlspecialchars($row['location']) . "</td>
                                <td>{$fullname}</td>
                                <td>{$checkedBy}</td>
                                <td>{$statusBadge}</td>
                                <td>{$remarks}</td>
                                <td>" . htmlspecialchars($row['date_submit']) . "</td>
                                <td>" . (!empty($row['date_update']) ? htmlspecialchars($row['date_update']) : '<span class=\"text-muted\">-</span>') . "</td>
                                <td>
                                  <a href='" . htmlspecialchars($row['file_path']) . "' download class='btn btn-outline-primary btn-sm' title='Download'>
                                    <i class='fas fa-download'></i>
                                  </a>
                                  <form method='post' class='d-inline'>
                                    <input type='hidden' name='file_id' value='{$row['id']}'>
                                    <input type='hidden' name='action' value='approve'>
                                    <button type='submit' class='btn btn-outline-success btn-sm' title='Approve'>
                                      <i class='fas fa-check'></i>
                                    </button>
                                  </form>
                                  <button type='button' class='btn btn-outline-danger btn-sm btn-return' 
                                    data-id='{$row['id']}' title='Return'>
                                    <i class='fas fa-times'></i>
                                  </button>
                                </td>
                              </tr>";
                        $count++;
                      }
                    } else {
                      echo "<tr><td colspan='11' class='text-center text-muted'>No pending files found.</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </section>
      </div>

      <?php include '../assets/includes/sysadmin/footer.php'; ?>
    </div>
  </div>

  <!-- Return Modal -->
  <div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="post" id="returnForm">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="returnModalLabel"><i class="fas fa-undo"></i> Return File with Remarks</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="file_id" id="returnFileId">
            <input type="hidden" name="action" value="return">
            <div class="form-group">
              <label for="remarks">Remarks</label>
              <textarea name="remarks" id="remarks" class="form-control" rows="4" placeholder="Enter your reason for returning..." required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-outline-danger"><i class="fas fa-undo"></i> Return</button>
          </div>
        </div>
      </form>
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
      // Disable DataTables error alerts
      $.fn.dataTable.ext.errMode = 'none';

      // Optional: silently log errors to console instead of showing alerts
      $('#fileTable').on('error.dt', function(e, settings, techNote, message) {
          console.warn('DataTables warning:', message);
      });

      // Initialize DataTable
      $('#bookTable').DataTable({
          "pageLength": 10,
          "lengthMenu": [5, 10, 25, 50, 100],
          "ordering": false,
          "searching": true,
          "scrollY": "400px",
          "scrollCollapse": true,
          "paging": true
      });

      // Handle Return button
      $(document).on('click', '.btn-return', function() {
        const fileId = $(this).data('id');
        $('#returnFileId').val(fileId);
        $('#remarks').val('');
        $('#returnModal').modal('show');
      });
  });
</script>

</body>
</html>
