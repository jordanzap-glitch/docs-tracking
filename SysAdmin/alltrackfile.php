<div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                <?php
                // Fetch unique files with activities (for ALL departments)
                $fileQuery = "
                  SELECT DISTINCT f.id, f.filename
                  FROM tbl_fileaudittrails fa
                  LEFT JOIN tbl_files f ON fa.file_id = f.id
                  ORDER BY f.filename ASC
                ";
                $fileResult = mysqli_query($conn, $fileQuery);

                if (mysqli_num_rows($fileResult) > 0) {
                  while ($file = mysqli_fetch_assoc($fileResult)) {
                    $fileId = $file['id'];
                    $fileName = htmlspecialchars($file['filename']);

                    echo '<div class="card mb-4 shadow-sm">
                            <div class="card-header bg-primary text-white">
                              <h6 class="mb-0"><i class="fas fa-file-alt"></i> ' . $fileName . '</h6>
                            </div>
                            <div class="card-body p-3">
                              <div class="activities">';
                    
                    // Fetch activities per file
                    $activityQuery = "
                      SELECT 
                          fa.id AS audit_id,
                          fa.file_id,
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
                          ut.usertype
                      FROM tbl_fileaudittrails fa
                      LEFT JOIN tbl_files f ON fa.file_id = f.id
                      LEFT JOIN tbl_user u ON fa.user_id = u.id
                      LEFT JOIN tbl_departments d ON fa.user_department_id = d.id
                      LEFT JOIN tbl_usertype ut ON fa.usertype_id = ut.id
                      WHERE fa.file_id = '$fileId'
                      ORDER BY fa.time_stamp DESC
                    ";

                    $activityResult = mysqli_query($conn, $activityQuery);

                    if (mysqli_num_rows($activityResult) > 0) {
                      while ($row = mysqli_fetch_assoc($activityResult)) {
                        $fullname = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
                        $department = htmlspecialchars($row['department_name']);
                        $action = htmlspecialchars($row['action_type']);
                        $status = htmlspecialchars($row['status']);
                        $timestamp = date("M d, Y h:i A", strtotime($row['time_stamp']));
                        $usertype = htmlspecialchars($row['usertype']);

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
                            <div class="activity-icon ' . $iconBg . ' text-white">
                              <i class="' . $iconClass . '"></i>
                            </div>
                            <div class="activity-detail">
                              <div class="mb-2">
                                <span class="text-job text-primary">' . $timestamp . '</span>
                                <span class="bullet"></span>
                                <a class="text-job" href="#">' . ucfirst($action) . '</a>
                              </div>
                              <p>
                                <strong>' . $fullname . '</strong> (' . $usertype . ', ' . $department . ') 
                                ' . strtolower($action) . ' this file
                                with status <b>' . $status . '</b>.
                              </p>
                            </div>
                          </div>
                        ';
                      }
                    } else {
                      echo '<p class="text-muted text-center">No activities found for this file.</p>';
                    }

                    echo '</div></div></div>';
                  }
                } else {
                  echo '<div class="text-center p-4"><h6 class="text-muted">No files with activity logs found.</h6></div>';
                }
                ?>
              </div>