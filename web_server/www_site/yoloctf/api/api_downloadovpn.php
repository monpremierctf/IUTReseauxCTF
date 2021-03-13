<?php
    session_start ();
    if (! isset($_SESSION['login'])){
        die();
    }
    
    include ("../ctf_utils/ctf_includepath.php");
    include "ctf_challenges.php";
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
    
    function requestOVPNClientConfigToOpenvpnServer() {
        $json1="";

        $login=$_GET['login'];
        $login=$_SESSION['login'];
        $url = $servers_list[$_SESSION['accesserver']].'/getovpnclientconfig/?login='.$login;
        $url= 'http://yop-server2.home:8888/getovpnclientconfig/?login='.$login;
        $json1 = file_get_contents_curl($url);
        return ($json1);
    }
    
    function downloadOVPNFile() {
        $ovpn_client = requestOVPNClientConfigToOpenvpnServer();
        $ovpn_client = json_decode($ovpn_client);
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Cache-Control: public"); // needed for internet explorer
        header("Content-Type: application/octet-stream");
        header("Content-Transfer-Encoding: Binary");
        //header("Content-Length:".filesize($ovpn_client));
        header("Content-Disposition: attachment; filename=robert2.ovpn");
        exit($ovpn_client);        
    }
    
    
    if (isset($_GET['OVPNFile'])){
        downloadOVPNFile();
    }
    echo "yolo";
?>