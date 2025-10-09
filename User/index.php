<?php
include '../assets/includes/session.php';
include '../assets/includes/db/dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Department of Agriculture - Folders</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="../assets/modules/izitoast/css/iziToast.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">

  <style>
    .folder-icon { font-size: 45px; color: #ffc107; margin-bottom: 10px; }
    .folder-body { text-align: center; padding: 10px; }
    .scroll-container-vertical { max-height: 60vh; overflow-y: auto; padding-right: 10px; scrollbar-width: thin; scrollbar-color: #ccc #f1f1f1; }
    .scroll-container-vertical::-webkit-scrollbar { width: 8px; }
    .scroll-container-vertical::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 10px; }
    .scroll-container-vertical::-webkit-scrollbar-track { background: #f1f1f1; }

    .shine-button { position: relative; padding: 0.4rem 1rem; font-size: 0.85rem; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; overflow: hidden; transition: all 0.3s ease; color: #fff; }
    .shine-button::before { content: ''; position: absolute; height: 250%; width: 40px; top: 0; left: -60px; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent); transform: rotate(45deg) translateY(-35%); animation: shine 3s ease infinite; }
    @keyframes shine { 0% { left: -80px; } 40% { left: calc(100% + 20px); } 100% { left: calc(100% + 20px); } }
    .button-emerald { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .button-emerald:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(17, 153, 142, 0.3); }

    .folder-card { margin-bottom: 15px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: transform 0.2s ease, box-shadow 0.3s ease; position: relative; }
    .folder-card.active { border: 2px solid #28a745; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3); }
    .folder-card:hover { transform: translateX(3px); }
    .folder-dot { position: absolute; right: 15px; top: 15px; height: 10px; width: 10px; border-radius: 50%; background-color: #38ef7d; visibility: hidden; }
    .folder-card.active .folder-dot { visibility: visible; }

    .upload-column { flex: 0 0 75%; max-width: 75%; padding: 20px; position: relative; }
    .upload-card { width: 100%; padding: 20px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border-radius: 12px; display: none; background-color: #fff; animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from {opacity:0; transform:translateY(-10px);} to {opacity:1; transform:translateY(0);} }

    table.file-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table.file-table th, table.file-table td { padding: 10px 12px; border-bottom: 1px solid #eaeaea; }
    table.file-table th { background-color: #f8f9fa; font-weight: 600; }
    table.file-table tr:hover { background-color: #fdfdfd; }

    .download-icon { border: none; background: linear-gradient(135deg, #38ef7d, #11998e); color: white; padding: 6px 10px; border-radius: 5px; transition: all 0.3s ease; }
    .download-icon:hover { background: linear-gradient(135deg, #11998e, #38ef7d); transform: scale(1.1); }
    .download-icon i { font-size: 14px; }

    .upload-btn { border: none; background: linear-gradient(135deg, #1abc9c, #16a085); color: #fff; padding: 10px 20px; font-weight: 600; border-radius: 50px; cursor: pointer; transition: all 0.3s ease; }
    .upload-btn:hover { transform: translateY(-2px); background: linear-gradient(135deg, #16a085, #1abc9c); }
  </style>
</head>

<body class="layout-3">
  <div id="app">
    <div class="main-wrapper container">
      <div class="navbar-bg"></div>
      <?php include '../assets/includes/user_agri/navbar1.php'; ?>
      <?php include '../assets/includes/user/navbar2.php'; ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Regular Employee</h1>
          </div>

          <div class="section-body">
            <h2 class="section-title">Folders</h2>

            <div class="row no-gutters">
              <!-- Folders Column -->
              <div class="col-lg-3 folders-column">
                <div class="scroll-container-vertical">
                  <div class="card card-primary folder-card" data-folder="accounting" data-name="Accounting Dept" data-action="upload_acct.php">
                    <div class="folder-dot"></div>
                    <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
                      <h6 class="mb-0">Accounting Dept</h6>
                      <button class="shine-button button-emerald open-folder">Open</button>
                    </div>
                    <div class="card-body folder-body"><i class="fas fa-folder folder-icon"></i></div>
                  </div>

                  <div class="card card-primary folder-card" data-folder="agriculture" data-name="Agriculture Dept" data-action="upload_agri.php">
                    <div class="folder-dot"></div>
                    <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
                      <h6 class="mb-0">Agriculture Dept</h6>
                      <button class="shine-button button-emerald open-folder">Open</button>
                    </div>
                    <div class="card-body folder-body"><i class="fas fa-folder folder-icon"></i></div>
                  </div>
                </div>
              </div>

              <!-- Upload Section -->
              <div class="col-lg-9 upload-column">
                <div class="card upload-card" id="uploadCard">
                  <div class="card-header bg-white border-bottom">
                    <h4><i class="fas fa-upload"></i> Upload to <span id="folderName">None</span></h4>
                  </div>
                  <div class="card-body">
                    <button class="upload-btn" id="uploadBtn"><i class="fas fa-file-upload"></i> Upload File</button>
                    <input type="file" id="hiddenFileInput" style="display:none;" />
                    <table class="file-table" id="fileTable">
                      <thead>
                        <tr>
                          <th>File Name</th>
                          <th>Uploaded By</th>
                          <th>Department</th>
                          <th>User Type</th>
                          <th>Date Uploaded</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody id="fileList">
                        <tr><td colspan="7" class="text-center text-muted">No folder selected.</td></tr>
                      </tbody>
                    </table>

                  </div>
                </div>
              </div>

            </div>
          </div>
        </section>
      </div>

      <?php include '../assets/includes/user_agri/footer.php'; ?>
    </div>
  </div>

  <!-- JS Scripts -->
  <script src="../assets/modules/jquery.min.js"></script>
  <script src="../assets/modules/popper.js"></script>
  <script src="../assets/modules/tooltip.js"></script>
  <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="../assets/modules/moment.min.js"></script>
  <script src="../assets/js/stisla.js"></script>
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>
  <script src="../assets/modules/izitoast/js/iziToast.min.js"></script>

  <script>
    const uploadCard = document.getElementById('uploadCard');
    const folderNameDisplay = document.getElementById('folderName');
    const folderCards = document.querySelectorAll('.folder-card');
    let activeFolder = null;
    let activeAction = null;

    const uploadBtn = document.getElementById('uploadBtn');
    const hiddenFileInput = document.getElementById('hiddenFileInput');

    // Folder selection
    folderCards.forEach(card => {
      card.querySelector('.open-folder').addEventListener('click', () => {
        folderCards.forEach(c => c.classList.remove('active'));
        card.classList.add('active');

        activeFolder = card.dataset.folder;
        activeAction = card.dataset.action;
        folderNameDisplay.textContent = card.dataset.name;

        uploadCard.style.display = 'block';
        loadFiles(activeFolder);
      });
    });

    // Open file browser on button click
    uploadBtn.addEventListener('click', () => {
      if (!activeFolder) return iziToast.warning({ title: 'Warning', message: 'Please select a folder first!', position: 'bottomRight' });
      hiddenFileInput.click();
    });

    // Auto-upload when a file is selected
    hiddenFileInput.addEventListener('change', function() {
      if (!this.files.length) return;
      const file = this.files[0];
      const formData = new FormData();
      formData.append('fileUpload', file);
      formData.append('folder_key', activeFolder);

      const loadingToast = iziToast.info({
        title: 'Uploading',
        message: file.name,
        position: 'bottomRight',
        timeout: false,
        overlay: true,
        close: false,
      });

      $.ajax({
        url: activeAction,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(resp) {
          iziToast.destroy();
          if (resp.success) {
            iziToast.success({ title: 'Success', message: resp.message, position: 'bottomRight' });
            loadFiles(activeFolder);
            hiddenFileInput.value = '';
          } else {
            iziToast.error({ title: 'Error', message: resp.message, position: 'bottomRight' });
          }
        },
        error: function() {
          iziToast.destroy();
          iziToast.error({ title: 'Error', message: 'Upload failed (network/server error).', position: 'bottomRight' });
        }
      });
    });

    // Load files dynamically
    function loadFiles(folderKey) {
      $.ajax({
        url: 'load_files.php',
        type: 'GET',
        data: { folder_key: folderKey },
        success: function(data){
          $('#fileList').html(data);
        }
      });
    }
  </script>
</body>
</html>
