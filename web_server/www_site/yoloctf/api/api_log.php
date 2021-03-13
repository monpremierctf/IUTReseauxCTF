<?php
 
//
// $_GET['start']  : 0    : offset
// $_GET['length'] : 10   : entry par page
// $_GET['draw']   : 1    : la page (peut avoir 5, 1µ0,n 25, 50 entry par page)


header('Content-type: application/json');

/*

Request : 
-----------------------
draw=1
&
columns[0][data]=0&
columns[0][name]=&
columns[0][searchable]=true&
columns[0][orderable]=true&
columns[0][search][value]=&
columns[0][search][regex]=false&

columns[1][data]=1&
columns[1][name]=&
columns[1][searchable]=true&
columns[1][orderable]=true&
columns[1][search][value]=&
columns[1][search][regex]=false&

columns[2][data]=2&
columns[2][name]=&
columns[2][searchable]=true&
columns[2][orderable]=true&
columns[2][search][value]=&
columns[2][search][regex]=false&

columns[3][data]=3&
columns[3][name]=&
columns[3][searchable]=true&
columns[3][orderable]=true&
columns[3][search][value]=&
columns[3][search][regex]=false&

columns[4][data]=4&
columns[4][name]=&
columns[4][searchable]=true&
columns[4][orderable]=true&
columns[4][search][value]=&
columns[4][search][regex]=false&

order[0][column]=0&
order[0][dir]=asc&
start=0&
length=10&
search[value]=&
search[regex]=false&
_=1584019287357

Response:
-------------------------
$ret = '{
    "draw": 1,
    "recordsTotal": 57,
    "recordsFiltered": 57,
    "data": [
      [
        "Airi",
        "Satou",
        "Accountant",
        "Tokyo",
        "28th Nov 08",
        "$162,700"
      ],
      [
        "Bruno",
        "Nash",
        "Software Engineer",
        "London",
        "3rd May 11",
        "$163,500"
      ],
      [
        "Cedric",
        "Kelly",
        "Senior Javascript Developer",
        "Edinburgh",
        "29th Mar 12",
        "$433,060"
      ]
    ]
  }';
*/

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

include ('db_log_fct.php');

function jquery_datatable_format_log_table()
{
    $nb = get_log_table_count();
    $start = isset($_GET['start'])?$_GET['start']:0;
    $length = isset($_GET['length'])?$_GET['length']:10;
    $draw = isset($_GET['draw'])?$_GET['draw']:1;

    $table = get_log_table($start, $length, $draw);
    $t = [];
    foreach ($table as $entry) {
        $e=[];
        $e[]= $entry['id'];
        $e[]= $entry['fdate'];
        $e[]= $entry['UID'];
        $e[]= $entry['type'];
        $e[]= $entry['txt'];
        $t[]=$e;
    }
    $ret = [
        'draw' => $draw,
        'recordsTotal' => $nb,
        'recordsFiltered'=> $nb,
        'data'=> $t
    ];
    return json_encode($ret);
}


function jquery_datatable_format_log_table_empty()
{
    $nb = 0;
    $draw = 1;

    $t = [];
    {
        $e=[];
        $e[]= "";
        $e[]= "";
        $e[]= "";
        $e[]= "";
        $e[]= "";
        $t[]=$e;
    }
    $ret = [
        'draw' => $draw,
        'recordsTotal' => $nb,
        'recordsFiltered'=> $nb,
        'data'=> $t
    ];
    return json_encode($ret);
}


if (isset($_SESSION['login'])&&($_SESSION['login'] === $admin)) {	
	echo jquery_datatable_format_log_table();
} else {
	echo jquery_datatable_format_log_table_empty();
}
?>