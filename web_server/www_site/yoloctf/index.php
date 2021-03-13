<?php
// Remove all server version
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
header_remove("X-Powered-By");
header("X-XSS-Protection: 1");
header('X-Frame-Options: SAMEORIGIN');
session_start();

$p = $_GET['p'];
include ("ctf_utils/ctf_includepath.php");
if ($p === "Login") {
  include (__SITEROOT__."/templates/p_login_post.php");
} elseif ($p === "Logout") {
  include (__SITEROOT__."/templates/p_logout_get.php");
} elseif ($p === "Register") {
  include (__SITEROOT__."/templates/p_register_post.php");
} 

?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <title>Y0L0 CTF</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <!--
  <link rel="stylesheet" href="/yoloctf/js/bootstrap.min.css">
  <script src="/yoloctf/js/jquery.min.js"></script>
  <script src="/yoloctf/js/popper.min.js"></script>
  <script src="/yoloctf/js/bootstrap.min.js"></script>
  -->
  <link rel="stylesheet" href="/yoloctf/style.css">
  <script src="/yoloctf/js/ctf-utils.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.css" />
  <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/keytable/2.5.1/js/dataTables.keyTable.min.js"></script>

</head>

<body>

  <!--- Page Header  -->
  <?php
  // Global def & vars

  include (__SITEROOT__."/vendor/markdown/Parsedown.php");
  $Parsedown = new Parsedown();

  // Page HEADER
  include (__SITEROOT__."/templates/header.php");

  ?>

  <!-- Modal -->
  <?php  include ('templates/t_modal_flag_validated.php'); ?>

  <!-- Page -->
  <div class="container-fluid">
    <div class="row">
      <!--- Page TOC  -->
      <div class="col-md-auto">
        <?php include (__SITEROOT__.'/templates/toc.php') ?>
      </div>

      <!--- Page Content  -->
      <div class="col-md-9">
        <div class="row-md-auto">

          <?php

          $p = $_GET['p'];
          $intro = getIntro($p);
          if ($intro != null) {
            /*
                if ((getLangage()=='en')&&(strlen($intro['description_en'])>0)) {
                   $string = $intro['description_en'];
                } else {
                   $string = $intro['description'];
                }
                */
            $string = getLocalizedIndex($intro, 'description');
            $string = pre_process_desc_for_md($string);
            // YOLO: $_SERVER['HTTP_HOST'] Secure ?
            if (isset($_SERVER['HTTP_HOST'])) {
              $string = str_replace("{IP_SERVER}", $_SERVER['HTTP_HOST'], $string);
            }
            print $Parsedown->text($string);
            print "<p class='chall-spacer'><p>";
            if ($intro['docker'] != null) {
              ctf_div_server_status($intro['docker']);
            }
            html_dump_cat($intro['category']);
          } elseif ($p === "Login") {
            include (__SITEROOT__."/templates/p_login.php");
          } elseif ($p === "Logout") {
            include (__SITEROOT__."/templates/p_logout.php");
          } elseif ($p === "Register") {
            include (__SITEROOT__."/templates/p_register.php");
          } elseif ($p === "Dashboard") {
            include (__SITEROOT__."/templates/p_containers.php");
          } elseif ($p === "Profile") {
            include (__SITEROOT__."/templates/p_profile.php");
          } elseif ($p === "Base64") {
            include (__SITEROOT__."/templates/p_base64.php");
          } elseif ($p === "Scoreboard") {
            include (__SITEROOT__."/templates/p_scoreboard.php");
          } elseif ($p === "Xterm") {
            include (__SITEROOT__."/templates/p_myterm.php");
          } elseif ($p === "Python") {
            include (__SITEROOT__."/templates/p_mypython.php");
          } elseif ($p === "Infra") {
            include (__SITEROOT__."/templates/p_infra.php");
          } elseif ($p === "Monitor") {
            include (__SITEROOT__."/templates/p_monitor.php");
          } elseif ($p === "Feedback") {
            include (__SITEROOT__."/templates/p_feedback.php");
          } elseif ($p === "Proxy") {
            include (__SITEROOT__."/templates/p_myproxy.php");
          } elseif ($p === "ChallServers") {
            include (__SITEROOT__."/templates/p_challservers.php");
          } elseif ($p === "AdminLog") {
            include (__SITEROOT__."/templates/p_adminlog.php");
          } elseif ($p === "Zen") {
            include (__SITEROOT__."/templates/p_zen.php");
          } elseif ($p==="VM") {
            include (__SITEROOT__."/templates/p_vm.php");
          } elseif ($p==="Editor") {
            include (__SITEROOT__."/templates/p_editor.php");
          } elseif ($p==="Acces") {
            include (__SITEROOT__."/templates/p_acces.php");            
            
          } elseif ($p === "Welcome_validated") {
            $string = file_get_contents(__SITEROOT__."/content/p_welcome_validated.md");
            print $Parsedown->text($string);
          } elseif ($p === "Welcome_waiting_validation") {
            $string = file_get_contents(__SITEROOT__."/content/p_welcome_waiting_validation.md");
            print $Parsedown->text($string);
          } else {
            $string = file_get_contents(__SITEROOT__."/content/head_lab.md");
            print $Parsedown->text($string);
          }

          ?>
        </div>
      </div>
    </div>
  </div>



</body>

</html>