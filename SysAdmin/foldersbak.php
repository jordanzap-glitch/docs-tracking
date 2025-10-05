<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Folders Page &mdash; Stisla</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="../assets/modules/chocolat/dist/css/chocolat.css">
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
<style>
  .folder-icon {
    font-size: 70px; /* big folder icon */
    color: #ffc107;
    margin-bottom: 15px;
  }
  .folder-body {
    text-align: center;
  }

  /* ==== Emerald Shine Button Styles ==== */
  .button-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .shine-button {
    position: relative;
    padding: 0.6rem 1.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.3s ease;
    letter-spacing: 0.5px;
    color: #fff;
  }

  .shine-button::before {
    content: '';
    position: absolute;
    height: 250%;
    width: 40px;
    top: 0;
    left: -60px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transform: rotate(45deg) translateY(-35%);
    animation: shine 3s ease infinite;
  }

  @keyframes shine {
    0% {
      left: -80px;
    }
    40% {
      left: calc(100% + 20px);
    }
    100% {
      left: calc(100% + 20px);
    }
  }

  .button-emerald {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    box-shadow: 
      0 6px 20px rgba(17, 153, 142, 0.4),
      inset 0 1px 0 rgba(255, 255, 255, 0.2);
  }

  .button-emerald:hover {
    transform: translateY(-3px);
    box-shadow: 
      0 10px 30px rgba(17, 153, 142, 0.6),
      inset 0 1px 0 rgba(255, 255, 255, 0.3);
  }

  .shine-button:active {
    transform: translateY(-1px);
    transition: transform 0.1s ease;
  }
</style>
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
            <h1>Folders</h1>
          </div>

          <div class="section-body">
            <div class="row">
              <!-- Folder 1 -->
              <div class="col-md-4 col-lg-4 mb-4">
                <div class="card card-primary">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Folder 1</h4>
                    <div class="button-wrapper">
                      <button class="shine-button button-emerald">Open</button>
                    </div>
                  </div>
                  <div class="card-body folder-body">
                    <i class="fas fa-folder folder-icon"></i>
                    <p>Folder 1 description or details here.</p>
                  </div>
                </div>
              </div>
              <!-- Folder 2 -->
              <div class="col-md-4 col-lg-4 mb-4">
                <div class="card card-primary">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Folder 2</h4>
                    <div class="button-wrapper">
                      <button class="shine-button button-emerald">Open</button>
                    </div>
                  </div>
                  <div class="card-body folder-body">
                    <i class="fas fa-folder folder-icon"></i>
                    <p>Folder 2 description or details here.</p>
                  </div>
                </div>
              </div>
              <!-- Folder 3 -->
              <div class="col-md-4 col-lg-4 mb-4">
                <div class="card card-primary">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Folder 3</h4>
                    <div class="button-wrapper">
                      <button class="shine-button button-emerald">Open</button>
                    </div>
                  </div>
                  <div class="card-body folder-body">
                    <i class="fas fa-folder folder-icon"></i>
                    <p>Folder 3 description or details here.</p>
                  </div>
                </div>
              </div>
              <!-- Folder 4 -->
              <div class="col-md-4 col-lg-4 mb-4">
                <div class="card card-primary">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Folder 4</h4>
                    <div class="button-wrapper">
                      <button class="shine-button button-emerald">Open</button>
                    </div>
                  </div>
                  <div class="card-body folder-body">
                    <i class="fas fa-folder folder-icon"></i>
                    <p>Folder 4 description or details here.</p>
                  </div>
                </div>
              </div>
              <!-- Folder 5 -->
              <div class="col-md-4 col-lg-4 mb-4">
                <div class="card card-primary">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Folder 5</h4>
                    <div class="button-wrapper">
                      <button class="shine-button button-emerald">Open</button>
                    </div>
                  </div>
                  <div class="card-body folder-body">
                    <i class="fas fa-folder folder-icon"></i>
                    <p>Folder 5 description or details here.</p>
                  </div>
                </div>
              </div>
              <!-- Folder 6 -->
              <div class="col-md-4 col-lg-4 mb-4">
                <div class="card card-primary">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Folder 6</h4>
                    <div class="button-wrapper">
                      <button class="shine-button button-emerald">Open</button>
                    </div>
                  </div>
                  <div class="card-body folder-body">
                    <i class="fas fa-folder folder-icon"></i>
                    <p>Folder 6 description or details here.</p>
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
  
  <!-- JS Libraies -->

  <!-- Page Specific JS File -->
  
  <!-- Template JS File -->
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>
</body>
</html>
