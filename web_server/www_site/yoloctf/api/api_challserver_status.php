<?php
 
//
// $_GET['start']  : 0    : offset
// $_GET['length'] : 10   : entry par page
// $_GET['draw']   : 1    : la page (peut avoir 5, 1µ0,n 25, 50 entry par page)


header('Content-type: application/json');


//
// Session start
session_start();
include ("../ctf_utils/ctf_includepath.php");
include 'ctf_env.php'; 

//
// ADMIN check
if (!isset($_SESSION['login'] )) {
  echo '{"draw":"1","recordsTotal":1,"recordsFiltered":1,"data":[["","","","",""]]}';
  die();
}

// $admin from ctf_env.php
if (!($_SESSION['login']=== $admin )) {
  echo '{"draw":"1","recordsTotal":1,"recordsFiltered":1,"data":[["","","","",""]]}';
  die();
}

//
// ADMIN only
   
    
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


if (isset($_GET['serverlist'])){
    
    $start = isset($_GET['start'])?$_GET['start']:0;
    $length = isset($_GET['length'])?$_GET['length']:10;
    $draw = isset($_GET['draw'])?$_GET['draw']:1;

    $url = 'http://remote-challserver:8888/serverlist/';
    $json1 = file_get_contents_curl($url);

    $data = json_decode($json1, true);
    $nb = count($data);
    $t = [];
    foreach ($data as $entry) {
        $e=[];
        $e[]= $entry['name'];
        $e[]= $entry['mem_total'];
        $e[]= $entry['mem_available'];
        $e[]= $entry['nbclientenv'];
        $e[]= $entry['nbclientenv_available'];
        $t[]=$e;
    }
    $ret = [
        'draw' => $draw,
        'recordsTotal' => $nb,
        'recordsFiltered'=> $nb,
        'data'=> $t
    ];
    echo json_encode($ret);

}


?>