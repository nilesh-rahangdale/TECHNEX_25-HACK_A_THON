<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('admin/db_connect.php');
ob_start();
    $query = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
    foreach ($query as $key => $value) {
      if(!is_numeric($key))
        $_SESSION['system'][$key] = $value;
    }
ob_end_flush();
include('header.php');
?>

<style>
  header.masthead {
    background: url(admin/assets/uploads/<?php echo $_SESSION['system']['cover_img'] ?>);
    background-repeat: no-repeat;
    background-size: cover;
  }

  /* Additional styling */
  #viewer_modal .btn-close {
    position: absolute;
    z-index: 999999;
    background: unset;
    color: white;
    border: unset;
    font-size: 27px;
    top: 0;
  }
  #viewer_modal .modal-dialog {
    width: 80%;
    height: calc(90%);
  }
  #viewer_modal .modal-content {
    background: black;
    border: unset;
    height: calc(100%);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  #viewer_modal img,#viewer_modal video {
    max-height: calc(100%);
    max-width: calc(100%);
  }
  body, footer {
    background: #000000e6 !important;
  }
</style>

<body id="page-top">
  <!-- Navigation-->
  <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body text-white"></div>
  </div>

  <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
    <div class="container">
      <a class="navbar-brand js-scroll-trigger" href="./"><?php echo $_SESSION['system']['name'] ?></a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto my-2 my-lg-0">
          <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=home">Home</a></li>
          <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=alumni_list">Alumni</a></li>
          <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=student_list">Student</a></li>
          <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=gallery">Gallery</a></li>
          <?php if(isset($_SESSION['login_id'])): ?>
          <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=careers">Jobs</a></li>
          <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=forum">Forums</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=about">About</a></li>
          <?php if(!isset($_SESSION['login_id'])): ?>
          <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#" id="login">Login</a></li>
          <?php else: ?>
          <li class="nav-item">
            <div class="dropdown mr-4">
              <a href="#" class="nav-link js-scroll-trigger" id="account_settings" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php echo $_SESSION['login_name'] ?> <i class="fa fa-angle-down"></i>
              </a>
              <div class="dropdown-menu" aria-labelledby="account_settings" style="left: -2.5em;">
                <a class="dropdown-item" href="index.php?page=my_account" id="manage_my_account"><i class="fa fa-cog"></i> Manage Account</a>
                <a class="dropdown-item" href="admin/ajax.php?action=logout2"><i class="fa fa-power-off"></i> Logout</a>
              </div>
            </div>
          </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Dynamic page content -->
  <?php 
  $page = isset($_GET['page']) ? $_GET['page'] : "home";
  include $page.'.php';
  ?>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      </div>
    </div>
  </div>

<div class="modal fade" id="login_type_modal" role='dialog'>
  <div class="modal-dialog modal-md" role="document" style="max-width: 500px;"> <!-- Increased width -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Login As</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
      </div>
      <div class="modal-body text-center">
        <!-- Added padding and spacing -->
        <button type="button" class="btn btn-primary btn-lg btn-block mb-4" id="alumni_login" style="padding: 15px 0; font-size: 1.2em;">Alumni Login</button>
        <button type="button" class="btn btn-success btn-lg btn-block" id="student_login" style="padding: 15px 0; font-size: 1.2em;">Student Login</button>
      </div>
    </div>
  </div>
</div>

  <!-- Modal for Login Form -->
  <div class="modal fade" id="login_form_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="login_form_title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <!-- Content of login form will be loaded here dynamically -->
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  
  <footer class="py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h2 class="mt-0 text-white">Contact us</h2>
          <hr class="divider my-4" />
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-4 ml-auto text-center mb-5 mb-lg-0">
          <i class="fas fa-phone fa-3x mb-3 text-muted"></i>
          <div class="text-white"><?php echo $_SESSION['system']['contact'] ?></div>
        </div>
        <div class="col-lg-4 mr-auto text-center">
          <i class="fas fa-envelope fa-3x mb-3 text-muted"></i>
          <a class="d-block" href="mailto:<?php echo $_SESSION['system']['email'] ?>"><?php echo $_SESSION['system']['email'] ?></a>
        </div>
      </div>
    </div>
    <br>
    <div class="container"><div class="small text-center text-muted">Copyright © 2024 - <?php echo $_SESSION['system']['name'] ?></div></div>
  </footer>

  <?php include('footer.php') ?>
  
  <!-- JavaScript -->
  <script type="text/javascript">
    // Handle login button click
    $('#login').click(function(){
      $('#login_type_modal').modal('show');
    });

    // Handle Student Login button click
    $('#student_login').click(function(){
      $('#login_type_modal').modal('hide');
      $('#login_form_title').text("Student Login");
      $('#login_form_modal .modal-body').load('student_login.php'); // Load the student login form
      $('#login_form_modal').modal('show');
    });

    // Handle Alumni Login button click
    $('#alumni_login').click(function(){
      $('#login_type_modal').modal('hide');
      $('#login_form_title').text("Alumni Login");
      $('#login_form_modal .modal-body').load('alumni_login.php'); // Load the alumni login form
      $('#login_form_modal').modal('show');
    });
  </script>
  
  <?php $conn->close() ?>
</body>
</html>
