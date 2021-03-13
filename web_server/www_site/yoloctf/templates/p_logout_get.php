<?php
    header('Location: index.php');
    
    session_start();
    require_once(__SITEROOT__."/ctf_utils/db_log_fct.php");
    insert_log_entry($_SESSION['uid'], "Logout", "User [".$_SESSION['login']."] logout");

    unset($_SESSION['login']);
    unset($_SESSION['uid']);
    unset($_SESSION);
    session_destroy();

    die();
?>