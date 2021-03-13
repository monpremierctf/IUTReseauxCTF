<?php

    // 
    // HTP Header
    //
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    header_remove("X-Powered-By");
    header("X-XSS-Protection: 1");
    header('X-Frame-Options: SAMEORIGIN'); 
    session_start ();
    header('Content-type: application/json');

    
    include ("ctf_utils/ctf_includepath.php");
    //
    // Get a CSRF Token for authent
    //
    include "hoserver/ho_csrfguard.php";
    if (isset($_GET['gettoken'])) {
        $token = csrfguard_generate_token('authent');    
        $json['token'] = $token;
        echo json_encode($json);
        http_response_code(200);
        exit();
    }


    //
    // Login with login/password/token
    //
    if (isset($_POST['login']) && isset($_POST['password']) && isset($_POST['token'])) {
        if (!csrfguard_validate_token('authent', $_POST['token'])) {
            $json['error'] = "Invalid token";
            echo json_encode($json);
            http_response_code(401);
            exit();
        }

        require_once "hoserver/ho_sql_fct.php";

        //error_log("Entering is_login_valid(".$_POST['login'].", md5(".$_POST['password'].")");
        $login  = $_POST['login'];
        $passwd = md5($_POST['password']);
        $row = is_login_valid($login, $passwd);
        //echo "row=[$row]";
        //var_dump($row);
        if ($row) {
            
        
            $authcookie = uniqid();
            $_SESSION['login'] = $row['login'];
            $_SESSION['uid'] = $row['UID'];
            $_SESSION['authcookie'] = $authcookie;
            $json['authcookie'] = $authcookie;
            setcookie("authcookie", $authcookie, 0, "/");

            require_once('db_log_fct.php');
            insert_log_entry($_SESSION['uid'], "Login", "User [".$_SESSION['login']."] login");

            echo json_encode($json);
            http_response_code(200);
            exit();
        } else {
            $json['error'] = "Invalid credentials";
            echo json_encode($json);
            http_response_code(401);
            exit();
        }
        
    }

    //
    // sshconfig
    //

    function file_get_contents_curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        //curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    if (isset($_SESSION['authcookie']) && isset($_COOKIE["authcookie"]) && ($_SESSION['authcookie']===$_COOKIE["authcookie"])) {
		if (isset($_GET['sshconfig'])) {
			$url = 'http://remote-challserver:8888/sshconfig/?uid='.$_SESSION['uid'];
			$json1 = file_get_contents_curl($url);
			echo $json1;
			$config = json_decode($json1, true); // true to get an array
			//if (isset($config['port'])) {
				$netid = intval($config['port'])-7001;
				$_SESSION['netid'] = $netid;
            //}
            require_once('db_log_fct.php');
            insert_log_entry($_SESSION['uid'], "GetAccessConfig", "User [".$_SESSION['login']."] get Access config");
			http_response_code(200);
			exit();
		}
		if (isset($_GET['challreset']) && isset($_GET['id'])) {
			$url = 'http://remote-challserver:8888/challreset/?uid='.$_SESSION['uid'].'&id='.$_GET['id'];
			$json1 = file_get_contents_curl($url);
            echo $json1;
            require_once('db_log_fct.php');
            insert_log_entry($_SESSION['uid'], "ChallReset", "User [".$_SESSION['login']."] Reset chall [".$_GET['id']."]");
			http_response_code(200);
			exit();
		}
		if (isset($_GET['challgetstatus']) && isset($_GET['id'])) {
            include ("db_flag_fct.php");
            // Check in db
            if (is_flag_validated($_SESSION['uid'],$_GET['id'])>0){
                echo "1";
            }  else {
                // If not Success, ask remote 
                $url = 'http://remote-challserver:8888/challgetstatus/?uid='.$_SESSION['uid'].'&id='.$_GET['id'];
                $json1 = file_get_contents_curl($url);
                echo $json1;
                
                // If Success, store in db
                $successstring = "1";
                if ($json1 == json_encode($successstring) ) {
                    //YOLO TEST: dont save
                    //save_flag_submission($_SESSION['uid'], $_GET['id'], "1", true);
                    require_once('db_log_fct.php');
                    insert_log_entry($_SESSION['uid'], "Flag OK", "User [".$_SESSION['login']."] flag [".$_GET['id']."]: Ok");
                }
            }

			http_response_code(200);
			exit();
        }
        if (isset($_GET['challgetallstatus']) ) {
            error_log("Entering challgetallstatus");
            include ("db_flag_fct.php");
            include ("hoserver/ho_flags.php");
            $ret=[];
            $out="";
            foreach ($hoChallIds as $id) {
                $validated = is_flag_validated($_SESSION['uid'],$id);
                /*
                $entry = [
                    "id" => $id,
                    "status"=> $validated                    
                ];
                rray_push($ret, $entry);
                */
                $ret[$id]=$validated;
                //echo json_encode($entry);
                $out = json_encode($ret);
            }
            echo $out;
            //$json1 == json_encode($ret);
            //echo $json1;
            http_response_code(200);
			exit();
        }
        if (isset($_GET['resetallflags']) ) {
            include ("db_flag_fct.php");
            $ret = reset_all_flags($_SESSION['uid']);
            require_once('db_log_fct.php');
            insert_log_entry($_SESSION['uid'], "Flag full reset", "User [".$_SESSION['login']."] Reset all flags");
            echo json_encode($ret);
			http_response_code(200);
			exit();
		}
		if (isset($_POST['hardwareconfig']) ) {
            http_response_code(200);
            require_once('db_log_fct.php');
            insert_log_entry($_SESSION['uid'], "Hard Config", $_POST['hardwareconfig']);
			exit();
		}
    }
   

    $json['error'] = "bad request";
    echo json_encode($json);
    http_response_code(400);
?>

