<?php
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    header_remove("X-Powered-By");
    header("X-XSS-Protection: 1");
    header('X-Frame-Options: SAMEORIGIN'); 
    
    session_start ();

    include ("ctf_utils/ctf_includepath.php");
    include 'ctf_hacklab_conf.php';
    
    
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
    

    
	if (isset($_GET['Servers'])){
        if (isset($_SESSION['login'] )) {
            include ("ctf_utils/ctf_includepath.php");
            include "ctf_env.php";  // $admin from ctf_env.php
            if (($_SESSION['login']=== $admin )) {
                $servers=[ "Server1", "Server2", "Server3"];
            } else {
                $servers=[ $_SESSION['accesserver'] ];
            }
            header('Content-Type: application/json');
            $json1 = json_encode($servers);
            echo ($json1);
            exit;
        }
	}
    
	if (isset($_GET['Metrics'])){
        if (isset($_GET['server'])) {}
		header('Content-Type: application/json');
		$url = $servers_list[$_SESSION['accesserver']].'/metrics/';
        $json1 = file_get_contents_curl($url);
		echo ($json1);
		exit;
	}
    
	if (isset($_GET['State'])){
		header('Content-Type: application/json');
		$url = $servers_list[$_SESSION['accesserver']].'/status/';
        $json1 = file_get_contents_curl($url);
		echo ($json1);
		exit;
	}


	if (isset($_GET['Start'])){
		header('Content-Type: application/json');
		$url = $servers_list[$_SESSION['accesserver']].'/vmstart/?id='.$_GET['Start'];
        $json1 = file_get_contents_curl($url);
		echo ($json1);
		exit;
	}
	
	
	if (isset($_GET['Stop'])){
		header('Content-Type: application/json');
		$url = $servers_list[$_SESSION['accesserver']].'/vmstop/?id='.$_GET['Stop'];
        $json1 = file_get_contents_curl($url);
		echo ($json1);
		exit;
	}

	if (isset($_GET['Reboot'])){
		header('Content-Type: application/json');
		$url = $servers_list[$_SESSION['accesserver']].'/vmreboot/?id='.$_GET['Reboot'];
        $json1 = file_get_contents_curl($url);
		echo ($json1);
		exit;
	}
    
    if (isset($_GET['Adduser'])){
		header('Content-Type: application/json');
		$url = $servers_list[$_SESSION['accesserver']].'/adduser/?login='.$_GET['login'].'&passwd='.$_GET['password'];
        $json1 = file_get_contents_curl($url);
		echo ($json1);
		exit;
	}
   
    
?>