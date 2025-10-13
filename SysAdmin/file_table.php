<?php
include '../assets/includes/session.php';
include '../assets/includes/db/dbcon.php';

$statusMsg = "";

// Fetch Approved Files
$queryApproved = "
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
  WHERE f.status = 'Approved'
  ORDER BY f.date_submit DESC
";
$resultApproved = mysqli_query($conn, $queryApproved);

// Fetch Returned Files
$queryReturned = "
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
  WHERE f.status = 'Returned'
  ORDER BY f.date_submit DESC
";
$resultReturned = mysqli_query($conn, $queryReturned);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Approved and Returned Files</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">

  <style>
    .card-header {
      background-color: #fff !important;
      color: #343a40 !important;
      border-bottom: 2px solid #dee2e6;
    }
    .card-header i.text-success {
      color: #28a745 !important;
    }
    .card-header i.text-danger {
      color: #dc3545 !important;
    }
  </style>
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <?php include '../assets/includes/sysadmin/navbar.php'; ?>
      <?php include '../assets/includes/sysadmin/activate/tableactive.php'; ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Approved and Returned Documents</h1>
          </div>

          <div class="section-body">
            <?php if (!empty($statusMsg)) echo $statusMsg; ?>

            <!-- APPROVED FILES TABLE -->
            <div class="card mb-4">
              <div class="card-header">
                <h4><i class="fas fa-check-circle text-success"></i> List of Approved Files</h4>
              </div>
              <div class="card-body table-responsive">
                <table id="approvedTable" class="table table-striped table-hover">
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
                      <th>Apporved Date</th>
                      <th>Download</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (mysqli_num_rows($resultApproved) > 0) {
                      $count = 1;
                      while ($row = mysqli_fetch_assoc($resultApproved)) {
                        $fullname = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
                        $checkedBy = !empty($row['check_firstname']) ? htmlspecialchars($row['check_firstname'] . ' ' . $row['check_lastname']) : '<span class="text-muted">Not yet checked</span>';
                        $remarks = !empty($row['remarks']) ? htmlspecialchars($row['remarks']) : '<span class="text-muted">—</span>';
                        $statusBadge = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Approved</span>';
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
                                <td>" . htmlspecialchars($row['date_update']) . "</td>
                                <td>
                                  <a href='" . htmlspecialchars($row['file_path']) . "' download class='btn btn-outline-primary btn-sm' title='Download'>
                                    <i class='fas fa-download'></i>
                                  </a>
                                </td>
                              </tr>";
                        $count++;
                      }
                    } else {
                      echo "<tr><td colspan='11' class='text-center text-muted'>No approved files found.</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- RETURNED FILES TABLE -->
            <div class="card">
              <div class="card-header">
                <h4><i class="fas fa-undo text-danger"></i> List of Returned Files</h4>
              </div>
              <div class="card-body table-responsive">
                <table id="returnedTable" class="table table-striped table-hover">
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
                      <th>Returned Date</th>
                      <th>Download</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (mysqli_num_rows($resultReturned) > 0) {
                      $count = 1;
                      while ($row = mysqli_fetch_assoc($resultReturned)) {
                        $fullname = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
                        $checkedBy = !empty($row['check_firstname']) ? htmlspecialchars($row['check_firstname'] . ' ' . $row['check_lastname']) : '<span class="text-muted">Not yet checked</span>';
                        $remarks = !empty($row['remarks']) ? htmlspecialchars($row['remarks']) : '<span class="text-muted">—</span>';
                        $statusBadge = '<span class="badge badge-danger"><i class="fas fa-undo"></i> Returned</span>';
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
                                <td>" . htmlspecialchars($row['date_update']) . "</td>
                                <td>
                                  <a href='" . htmlspecialchars($row['file_path']) . "' download class='btn btn-outline-primary btn-sm' title='Download'>
                                    <i class='fas fa-download'></i>
                                  </a>
                                </td>
                              </tr>";
                        $count++;
                      }
                    } else {
                      echo "<tr><td colspan='11' class='text-center text-muted'>No returned files found.</td></tr>";
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

  <!-- JS Scripts -->
  <script src="../assets/modules/jquery.min.js"></script>
  <script src="../assets/modules/popper.js"></script>
  <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="../assets/modules/moment.min.js"></script>
  <script src="../assets/js/stisla.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>

  <script>
  $(document).ready(function () {
      // Disable DataTables pop-up error notifications
      $.fn.dataTable.ext.errMode = 'none';

      // Optional: log warnings quietly in the console instead of showing alerts
      $(document).on('error.dt', function(e, settings, techNote, message) {
          console.warn('DataTables warning:', message);
      });

      // Initialize both DataTables
      $('#approvedTable, #returnedTable').DataTable({
          "pageLength": 10,
          "lengthMenu": [5, 10, 25, 50, 100],
          "ordering": false,
          "searching": true,
          "scrollY": "400px",
          "scrollCollapse": true,
          "paging": true
      });
  });
</script>

</body>
</html>
