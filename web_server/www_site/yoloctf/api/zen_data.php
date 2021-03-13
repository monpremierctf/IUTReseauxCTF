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
            


?>