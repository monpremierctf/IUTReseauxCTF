<?php 

include_once("ctf_challenges.php");

function reset_all_flags($uid){
    require "ctf_sql_pdo.php";
    $ret=0;
    $query = "DELETE from flags where (UID=:uid);";
    //echo $query;
    $stmt = $mysqli_pdo->prepare($query);
    if ($stmt->execute(['uid' => $uid])) {
        $ret  = $stmt->rowCount();
    }
    return $ret;
}

function is_flag_validated($uid, $cid)
{
    require "ctf_sql_pdo.php";
    $ret=0;
    $query = "select UID from flags where (UID=:uid and CHALLID=:cid and isvalid=TRUE);";
    //echo $query;
    $stmt = $mysqli_pdo->prepare($query);
    if ($stmt->execute(['uid' => $uid, 'cid' => $cid ])) {
        $ret  = $stmt->rowCount();
    }
    return $ret;
}


function count_flagarray_validated($uid, $cidarray)
{
    require "ctf_sql_pdo.php";
    $ret=0;
    $idlist="(";
    $firstitem=true;
    foreach ($cidarray as $cid) {
        if ($firstitem) { $firstitem =false;} else { $idlist=$idlist.", "; }
        $idlist=$idlist."'".$cid."' ";
    }
    $idlist=$idlist.")";    
    $query = "select UID from flags where (UID=:uid and CHALLID IN ".$idlist." and isvalid=TRUE);";
    //echo $query;
    $stmt = $mysqli_pdo->prepare($query);
    if ($stmt->execute(['uid' => $uid ])) {
        $ret  = $stmt->rowCount();
    }
    return $ret;
}


//SELECT * FROM products WHERE catid IN ('1', '2', '3', '4')


function getUserScore($uid)
{    
    require "ctf_sql_pdo.php";
    $ret=0;
    $query = "select max(score) as maxscore from flags where (UID=:uid);";
    //echo $query;
    $stmt = $mysqli_pdo->prepare($query);
    if ($stmt->execute(['uid' => $uid ])) {
        if ($row = $stmt->fetch()) {
            $ret= $row['maxscore'];
        }
    }
    return $ret;
}

function getUserNbFlagSolved($uid, $flagsuidlist)
{    
    $ret = count_flagarray_validated($uid, $flagsuidlist);
    return $ret;
    
    $ret=0;
    foreach($flagsuidlist as $challid) {
        if (is_flag_validated($uid, $challid)) { $ret = $ret+1;}
    }
    return $ret;
}

function getUserUIDFromLogin($login)
{    
    require "ctf_sql_pdo.php";
    $ret="XXXXXXX";
    $query = "select UID from users where (login=:login);";
    //echo $query;
    $stmt = $mysqli_pdo->prepare($query);
    if ($stmt->execute(['login' => $login ])) {
        if ($row = $stmt->fetch()) {
            $ret= $row['UID'];
        }
    }
    return $ret;
}



function dumpUsersDetailedScore($login)
{
    $uid = getUserUIDFromLogin($login);
    $ret=[];
    $ret['login']=$login;
    $ret['totalScore']= getUserScore($uid);
    $ret['categories'] = [];
    $cats = getCategories();
    foreach ($cats as $cat) {
        $entry=[];
        $entry['category'] = $cat;
        $entry['score']=0;
        $flagsuidlist = getFlagListForCategory($cat);
        $entry['nbChall']=count($flagsuidlist);
        $entry['nbChallSolved']=getUserNbFlagSolved($uid, $flagsuidlist);
        array_push($ret['categories'], $entry);
    }
    
    echo json_encode($ret);
}

function getCategoryStatusByGroup($cat, $group) {
    $ret = [];
    $challs_in_cat  = getCategory($cat);
    $isfirstchall=true;
    $challids = "(";
    foreach ($challs_in_cat as $c) {
        if ($isfirstchall==true) { $isfirstchall=false;} else {$challids = $challids.", ";}
        $challids = $challids." '".$c['id']."'";
    }
    $challids = $challids.")";
    
    /*
                    SUM(CASE WHEN isvalid = true THEN 1 ELSE 0 END) AS valid,
                    SUM(CASE WHEN isvalid = false THEN 1 ELSE 0 END) AS tries
    */
    require "ctf_sql_pdo.php";
    $query = "select flags.CHALLID, flags.UID, users.login, flags.fdate, isvalid
                FROM flags
                LEFT JOIN users ON users.UID = flags.UID
                LEFT JOIN group_users ON users.UID = group_users.UID
                LEFT JOIN groups ON group_users.UIDGROUP = groups.UIDGROUP
                where (CHALLID IN $challids)  AND groups.groupname=:group
                ORDER BY CHALLID, flags.UID, users.login, flags.fdate;";
    //echo $query;
    $stmt = $mysqli_pdo->prepare($query);
    if ($stmt->execute(
        ['group' => $group ]
    )) {   
        while ($row = $stmt->fetch()) {
            array_push($ret, $row);
        }
    }
    return $ret;
}


function save_flag_submission($uid, $cid, $flag, $isvalid)
{
	//include ('ctf_challenges.php');
    $count = is_flag_validated($uid, $cid);
    if (($isvalid)&&($count>0)) {
        return;
    }
        //echo "Valid='$valid'";
        //insert into flags (UID,CHALLID, fdate, isvalid, flag) values ('user1','chall1', NOW(), TRUE, 'flag1');
        $flag = $flag;
        $val=0;
        $score=0;
        if ($isvalid) {
            require_once("ctf_challenges.php");
            $val = getChallValue($cid);
			$val = rand(2, 12); // YOLO
            $score=getUserScore($uid)+$val;
        }
        //echo "- $cid, $val, $score";
        require "ctf_sql_pdo.php";
        $query = "insert into flags (UID,CHALLID, fdate, isvalid, flag, value, score) 
                  values (:uid, :cid, NOW(), :isvalid, :flag, :val, :score);";
        //echo $query;
        $stmt = $mysqli_pdo->prepare($query);
        if ($stmt->execute([
            'uid' => $uid,
            'cid' => $cid,
            'isvalid' => $isvalid,
            'flag' => $flag,
            'val' => $val,
            'score' => $score,                
             ])) {
            // ok
        } else {
            // ko
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }

}


?>