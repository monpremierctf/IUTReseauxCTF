<?php
    session_start ();
    
    
    function file_get_contents_curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, $url);
        //Disable CURLOPT_SSL_VERIFYHOST and CURLOPT_SSL_VERIFYPEER by
        //setting them to false.
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        //curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    } 
    
    function get_chall_provider_url($uid){
        $chall_provider_url = 'http://challenge-box-provider:8080';
        //$_SESSION['hacklab']
        if ($_SESSION['login'] == "hl2") {
            $chall_provider_url = 'http://hacklab2.home:9080';
            $chall_provider_url = 'http://192.168.1.38:9080';
        }
        return $chall_provider_url;
    }
    
    if (isset($_SESSION['login'] )) {
        if (isset($_GET['create'])){
            $url = get_chall_provider_url($_SESSION['uid']).'/createChallengeBox/?uid='.$_SESSION['uid'].'&cid='.$_GET['create'];
            //echo "==".$url."";
            $json1 = file_get_contents_curl($url);
            echo $json1;
        }
        if (isset($_GET['status'])){
            $url =  get_chall_provider_url($_SESSION['uid']).'/statusChallengeBox/?uid='.$_SESSION['uid'].'&cid='.$_GET['status'];
            //echo "==".$url."";
            $json1 = file_get_contents_curl($url);
            echo $json1;
        }
        if (isset($_GET['terminate'])){
            $url =  get_chall_provider_url($_SESSION['uid']).'/stopChallengeBox/?uid='.$_SESSION['uid'].'&cid='.$_GET['terminate'];
            //echo "==".$url."";
            $json1 = file_get_contents_curl($url);
            echo $json1;
        }
        
        if (($_SESSION['login']=== $admin )) {
            
        }
            
            
    } else {
        echo "Merci de vous connecter.";
    }

?>
