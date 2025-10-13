<?php
include '../assets/includes/db/dbcon.php';
?>

<!-- SEARCH BAR -->
<form method="GET" class="mb-4" onsubmit="return false;">
  <div class="input-group">
    <input type="text" id="searchInput" name="search_filename" class="form-control" placeholder="Search by Filename...">
    <div class="input-group-append">
      <button type="button" class="btn btn-info" id="resetBtn"><i class="fas fa-undo"></i> Reset</button>
    </div>
  </div>
</form>

<!-- RESULTS CONTAINER -->
<div id="resultsContainer">
<?php
$search = "";
if (isset($_GET['search_filename']) && $_GET['search_filename'] != "") {
  $search = mysqli_real_escape_string($conn, $_GET['search_filename']);
  $filesQuery = mysqli_query($conn, "
    SELECT DISTINCT f.id AS file_id, f.filename 
    FROM tbl_fileaudittrails a
    INNER JOIN tbl_files f ON a.file_id = f.id
    WHERE f.filename LIKE '%$search%'
    ORDER BY a.time_stamp DESC
  ");
} else {
  $filesQuery = mysqli_query($conn, "
    SELECT DISTINCT f.id AS file_id, f.filename 
    FROM tbl_fileaudittrails a
    INNER JOIN tbl_files f ON a.file_id = f.id
    ORDER BY a.time_stamp DESC
  ");
}

if (mysqli_num_rows($filesQuery) > 0) {
  while ($file = mysqli_fetch_assoc($filesQuery)) {
    $fileId = $file['file_id'];
    $fileName = $file['filename'];

    echo '<div class="card mb-4">
            <div class="card-header bg-primary text-white">
              <h4><i class="fas fa-file-alt"></i> ' . htmlspecialchars($fileName) . '</h4>
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

<!-- REALTIME SEARCH SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const resultsContainer = document.getElementById('resultsContainer');
  const resetBtn = document.getElementById('resetBtn');

  // Function to load results dynamically
  function loadResults(query = '') {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'alltab.php?search_filename=' + encodeURIComponent(query), true);
    xhr.onload = function() {
      if (xhr.status === 200) {
        const parser = new DOMParser();
        const htmlDoc = parser.parseFromString(xhr.responseText, 'text/html');
        const newResults = htmlDoc.querySelector('#resultsContainer').innerHTML;
        resultsContainer.innerHTML = newResults;
      }
    };
    xhr.send();
  }

  // Realtime search on input
  searchInput.addEventListener('keyup', function() {
    const query = this.value.trim();
    loadResults(query);
  });

  // Reset button reloads full data
  resetBtn.addEventListener('click', function() {
    searchInput.value = '';
    loadResults('');
  });
});
</script>
