<?php
    
    //
    // Handle request
    //
    session_start ();
    include ("ctf_utils/ctf_includepath.php");
    include ("ctf_challenges.php");
	include ('db_flag_fct.php');
	
    // if, flag
    $cid =  $_GET['id'];
    $flag = trim($_GET['flag']);
    if (isset($flag)) {
        $flag = urldecode($flag);
    }

    if (isset($_SESSION['uid'] )) {
        $uid = $_SESSION['uid'];

        // Status != enabled
        if ($_SESSION['status'] !== 'enabled') {
            if (isFlagValid($cid,$flag)){
                print "ok_not_enabled";
            } else {
                echo "ko_not_enabled";
            }
            return;
        }
        
        if (isset($_GET['flag'])) {
            require_once('db_log_fct.php');
            if (isFlagValid($cid,$flag)){
                print "ok";
                save_flag_submission($_SESSION['uid'], $cid, $flag, true);
                insert_log_entry($_SESSION['uid'], "Flag OK", "User [".$_SESSION['login']."] flag Ok");
            } else {
                print "ko";
                save_flag_submission($_SESSION['uid'], $cid, $flag, false);
                insert_log_entry($_SESSION['uid'], "Flag KO", "User [".$_SESSION['login']."] flag Ko");
            }   
        } else {
            $count = is_flag_validated($uid, $cid);
            //echo $count;
            if (($count>0)) {
                echo 'ok';
            } else {
                echo 'ko';
            }
        }
    } else {
        // User not logged
        if (isFlagValid($cid,$flag)){
            echo "ok_not_logged";
        } else {
            echo "ko_not_logged";
        }
        
    }
  
?>