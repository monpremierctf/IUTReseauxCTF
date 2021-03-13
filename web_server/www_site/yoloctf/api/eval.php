<?php
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    header_remove("X-Powered-By");
    header("X-XSS-Protection: 1");
    header('X-Frame-Options: SAMEORIGIN'); 
    
    session_start ();

    //
    // Global def & vars
    include ("../ctf_utils/ctf_includepath.php");
    include (__SITEROOT__."/ctf_utils/ctf_env.php"); 
    require_once(__SITEROOT__."/ctf_utils/db_requests.php");

    //
    // Some functions
    function dumpUserCount(){
        include (__SITEROOT__."/ctf_utils/ctf_sql.php");
        $user_query = "SELECT count(*) as total from users;";
        if ($result = $mysqli->query($user_query)) {
            if ($row = $result->fetch_assoc()) {
                
                echo $row['total'];	
            }
            $result->close();
        }
        $mysqli->close();
    }

    function dumpUserList(){
        header('Content-Type: application/text');
        echo getNbUsers();
    }


	function filterParam($param) {
		return preg_replace("/[^0-9a-zA-Z_\-\.]/", "", $param );
	}
    //
    // Handle requests

    if (isset($_GET['UsersList'])){
        dumpUserListJSON();
        exit;
    }
    if (isset($_GET['IUTList'])){
        dumpIUTListJSON();
        exit;
    }
    // Datas
    if (isset($_GET['UsersCount'])){
        dumpUserCount();
        exit;
    }
    // Datas
    if (isset($_GET['ChallengeCategoriesList'])){
        include("ctf_challenges.php");
        $cats = getCategories();
        echo json_encode($cats);
        exit;
    }
    if (isset($_GET['ChallengeCategoryIntros'])){
        include("ctf_challenges.php");
        $cats = getCategories();
        $ret=[];
        foreach ($cats as $cat) {
            $intro = getIntro($cat);
            $ret[] = $intro;
        }
        header('Content-Type: application/json');
        echo json_encode($ret);
        exit;
    }
    if (isset($_GET['ChallengeCategoryIntro'])){
        include("ctf_challenges.php");
        $cats = getIntro($_GET['ChallengeCategoryIntro']);
        header('Content-Type: application/json');
        echo json_encode($cats);
        exit;
    }
    if (isset($_GET['ChallengeCategory'])){
        include("ctf_challenges.php");
        $cats = getCategory($_GET['ChallengeCategory']);
        header('Content-Type: application/json');
        echo json_encode($cats);
        exit;
    }
    if (isset($_GET['CategoryStatusByGroup'])){
        include("ctf_challenges.php");
        require_once("ctf_input.php");
        require_once("db_flag_fct.php");
        $cat = clean_string($_GET['CategoryStatusByGroup']);
        $group = clean_string($_GET['group']);
        $ret = getCategoryStatusByGroup($cat, $group);
        header('Content-Type: application/json');
        echo json_encode($ret);
        exit;
    }
    // Datas
    if (isset($_GET['UsersFlags'])){

        require_once(__SITEROOT__."/ctf_utils/ctf_challenges.php");
		if (isset($_GET['Category'])){
			dumpUserFlagDataSet($_GET['UsersFlags'], $_GET['Category']);
		} else {
			dumpUserFlagDataSet($_GET['UsersFlags']);
		}
        exit;
    }
    if (isset($_GET['UsersDetailedScore'])){
        require_once(__SITEROOT__."/ctf_utils/ctf_input.php");
        require_once(__SITEROOT__."/ctf_utils/db_flag_fct.php");
        $login = clean_string($_GET['UsersDetailedScore']);
        dumpUsersDetailedScore($login);
        exit;
    }    
    if (isset($_GET['Top20'])){
        $nb= 0;
        $enb = intval($_GET['Top20']);
        if ( $enb >= 10) { $nb = $enb; }; 
        $iut="";
        $lycee="";
        $group="";
        if (isset($_GET['iut'])) { $iut= $_GET['iut']; }
        if (isset($_GET['lycee'])) { $lycee= $_GET['lycee']; }
        if (isset($_GET['group'])) { 
            $group= $_GET['group'];
            $group= filterParam($group);
            dumpTop20Group($nb, $group);
        } else {
            dumpTop20($nb, $iut, $lycee); 
        }
        exit;
    }
    if (isset($_GET['LoginExist'])){
        echo json_encode(db_login_exists($_GET['LoginExist']));
        exit;
    }


    function dumpFlagsSubmittedByGroup($group){
        require "ctf_sql_pdo.php";
        
        $count=0;
        $params['group']= $group ;
        $query = "
        SELECT f.UID, max(score) as max_score, login  
        FROM flags f 
            LEFT JOIN users u	     on f.UID = u.UID 
            LEFT JOIN group_users gu on f.UID = gu.UID
            LEFT JOIN groups g	     on gu.UIDGROUP = g.UIDGROUP 
        WHERE g.groupname=:group
        GROUP BY UID ORDER BY max(score) DESC ";
        

        $query = "SELECT UIDGROUP, groupname from groups";
        // UIDGROUP = 72E35
        // groupname= LPRIMS_2020_2021

        $query = "
        SELECT u.UID, u.login  
        FROM users u	     
            LEFT JOIN group_users gu on u.UID = gu.UID
            LEFT JOIN groups g	     on gu.UIDGROUP = g.UIDGROUP 
        WHERE g.groupname='LPRIMS_2020_2021' ";


        /*
        CREATE TABLE `flags` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `UID` varchar(45) DEFAULT NULL,
        `CHALLID` varchar(45) DEFAULT NULL,
        `fdate` datetime DEFAULT NULL,
        `isvalid` tinyint(1) DEFAULT NULL,
        `flag` varchar(45) DEFAULT NULL,
        `value` int(11) DEFAULT NULL,
        `score` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`)
        */
        $query = "
        SELECT u.login, f.id, f.CHALLID, f.fdate, f.isvalid, f.flag, f.value, f.score
        FROM flags f 
            LEFT JOIN users u	     on f.UID = u.UID 
            LEFT JOIN group_users gu on f.UID = gu.UID
            LEFT JOIN groups g	     on gu.UIDGROUP = g.UIDGROUP 
        WHERE g.groupname='LPRIMS_2020_2021'
        ORDER BY u.login, f.fdate  ";

        $query = $query." ;";
        //echo $query."<br/>";
        $stmt = $mysqli_pdo->prepare($query);
        if ($stmt->execute()) {
            echo '[';
            $firstrow=true;
            $uname="";
            while ($frow = $stmt->fetch()) {
                //var_dump($frow); echo "<br/>";
                if ($frow['login']!=$uname) {
                    $uname = $frow['login'];
                    echo "<br/><b>$uname</b><br/>";
                }
                echo $frow['fdate']." ".$frow['CHALLID']." ".$frow['flag']." ".$frow['isvalid']." ".$frow['score']."<br/>";                
            }
            
            echo ']';
        } else {
            echo "nop";
        }
    }



    function dumpGroupScore($group){
        require "ctf_sql_pdo.php";
        
        $count=0;
        $params['group']= $group ;
        $query = "
        SELECT f.UID, max(score) as max_score, login  
        FROM flags f 
            LEFT JOIN users u	     on f.UID = u.UID 
            LEFT JOIN group_users gu on f.UID = gu.UID
            LEFT JOIN groups g	     on gu.UIDGROUP = g.UIDGROUP 
        WHERE g.groupname=:group
        GROUP BY UID ORDER BY max(score) DESC ";
        

        $query = "SELECT UIDGROUP, groupname from groups";
        // UIDGROUP = 72E35
        // groupname= LPRIMS_2020_2021

        $query = "
        SELECT u.UID, u.login  
        FROM users u	     
            LEFT JOIN group_users gu on u.UID = gu.UID
            LEFT JOIN groups g	     on gu.UIDGROUP = g.UIDGROUP 
        WHERE g.groupname='LPRIMS_2020_2021' ";


        /*
        CREATE TABLE `flags` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `UID` varchar(45) DEFAULT NULL,
        `CHALLID` varchar(45) DEFAULT NULL,
        `fdate` datetime DEFAULT NULL,
        `isvalid` tinyint(1) DEFAULT NULL,
        `flag` varchar(45) DEFAULT NULL,
        `value` int(11) DEFAULT NULL,
        `score` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`)
        */
        $query = "
        SELECT u.login, f.id, f.CHALLID, f.fdate, f.isvalid, f.flag, f.value, f.score
        FROM flags f 
            LEFT JOIN users u	     on f.UID = u.UID 
            LEFT JOIN group_users gu on f.UID = gu.UID
            LEFT JOIN groups g	     on gu.UIDGROUP = g.UIDGROUP 
        WHERE g.groupname='LPRIMS_2020_2021'
        ORDER BY u.login, f.CHALLID, f.flag,  f.isvalid, f.value  ";

        $query = $query." ;";
        //echo $query."<br/>";
        $stmt = $mysqli_pdo->prepare($query);
        if ($stmt->execute()) {
            echo '[';
            $firstrow=true;
            $uname="";
            $lastvalidatedflag="";
            $nbflags=0;
            while ($frow = $stmt->fetch()) {
                //var_dump($frow); echo "<br/>";
                if ($frow['login']!=$uname) {
                    $uname = $frow['login'];
                    $score[$uname]=0;
                    $nbflags=0;
                    echo "<br/><b>$uname</b><br/>";
                }
                $currentflag=$frow['CHALLID']."-".$frow['flag'];
                if (($frow['isvalid'])&&($currentflag!=$lastvalidatedflag)) {
                    $lastvalidatedflag = $currentflag;
                    $nbflags=$nbflags+1;
                    $score[$uname]=$score[$uname]+1;
                    echo $frow['fdate']." ".$frow['CHALLID']." ".$frow['flag']." ".$nbflags."<br/>";; //." ".$frow['isvalid']." ".$frow['score']."<br/>";                
                    //echo $score[$uname]." ";
                }
            }
            echo "<br/>";
            arsort($score);
            foreach ($score as $key => $value) {
                echo $key.": ".$value."<br/>";
            }
            
            echo ']';
        } else {
            echo "nop";
        }
    }


    
    $nb=30;     
    $group="LP_RIMS_2020_2021";
    /*
    echo "===== Flags saisis ====<br/>";
    echo dumpFlagsSubmittedByGroup($group);
    */
    
    echo "===== Flags crédités ====<br/>";
    echo dumpGroupScore($group);

?>