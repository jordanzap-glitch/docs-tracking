<?php
include '../assets/includes/session.php';
include '../assets/includes/db/dbcon.php';

$statusMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $filename = mysqli_real_escape_string($conn, $_POST['filename']);
    $to_department = 'ACCT';
    $location = 'Municipal Admin'; #change this depends on the department 
    $status = 'Pending';
    $user_id = $_SESSION['userId'];
    $date_created = date('Y-m-d H:i:s');
    $date_submit = date('Y-m-d H:i:s');

    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['document']['tmp_name'];
        $fileName = $_FILES['document']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('pdf', 'doc', 'docx', 'txt');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = '../uploads/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            $newFileName = uniqid() . '-' . basename($fileName);
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $file_path = 'uploads/' . $newFileName;

                $query = "INSERT INTO tbl_files 
                            (filename, file_path, location, user_id, to_department, status, date_submit, date_created) 
                          VALUES 
                            ('$filename', '$file_path', '$location', '$user_id', '$to_department', '$status', '$date_submit', '$date_created')";

                if (mysqli_query($conn, $query)) {
                    $statusMsg = '<div class="alert alert-success" role="alert">
                                    <i class="fas fa-check-circle"></i> File uploaded and saved successfully! Redirecting...
                                  </div>';
                    echo "<meta http-equiv='refresh' content='2;url=" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
                } else {
                    $statusMsg = '<div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i> Database error: ' . mysqli_error($conn) . '
                                  </div>';
                }
            } else {
                $statusMsg = '<div class="alert alert-danger" role="alert">
                                <i class="fas fa-times-circle"></i> Error moving uploaded file. Please check folder permissions.
                              </div>';
            }
        } else {
            $statusMsg = '<div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-circle"></i> Invalid file type. Allowed: PDF, DOC, DOCX, TXT
                          </div>';
        }
    } else {
        $statusMsg = '<div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-circle"></i> Please select a valid file to upload.
                      </div>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Upload Documents</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">

<!-- Start GA -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-94034622-3');
</script>
<!-- /END GA -->
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <?php include '../assets/includes/sysadmin/navbar.php'; ?>
      
      <?php include '../assets/includes/sysadmin/activate/folderactive.php'; ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Upload Documents to Accounting Department</h1>
          </div>

          <div class="mb-3">
            <a href="foldersbak.php" class="btn btn-outline-danger">
              <i class="fas fa-arrow-left"></i> Back
            </a>
          </div>

          <div class="section-body">
            <div class="row">
              <div class="col-12 col-md-8 col-lg-6">
                <div class="card">
                  <div class="card-header">
                    <h4><i class="fas fa-file-upload"></i> File Upload Form</h4>
                  </div>
                  <div class="card-body">

                    <!-- Status Message -->
                    <?php if (!empty($statusMsg)) echo $statusMsg; ?>

                    <form action="" method="POST" enctype="multipart/form-data">
                      
                      <!-- File Name Field -->
                      <div class="form-group">
                        <label for="filename">
                          <i class="fas fa-tag"></i> File Name 
                          <span style="color:red;">*</span>
                        </label>
                        <input type="text" class="form-control" id="filename" name="filename" placeholder="Enter file name" required>
                      </div>

                      <!-- File Upload Field -->
                      <div class="form-group">
                        <label for="document">
                          <i class="fas fa-file-alt"></i> Choose File 
                          <span style="color:red;">*</span>
                        </label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-file"></i></span>
                          </div>
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="document" name="document" required>
                            <label class="custom-file-label" for="document">Choose file...</label>
                          </div>
                        </div>
                        <small class="form-text text-muted">Allowed formats: PDF, DOC, DOCX, TXT</small>
                      </div>

                      <div class="form-group">
                        <button type="submit" class="btn btn-outline-success">
                          <i class="fas fa-upload"></i> Upload
                        </button>
                      </div>
                    </form>

                  </div>
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

  <!-- Show filename in label -->
  <script>
    $(".custom-file-input").on("change", function() {
      var fileName = $(this).val().split("\\").pop();
      $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
  </script>
</body>
</html>
