<?php
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    header_remove("X-Powered-By");
    header("X-XSS-Protection: 1");
    header('X-Frame-Options: SAMEORIGIN'); 
    header("Access-Control-Allow-Origin: *");
    //header("Content-Type: application/json; charset=UTF-8");

    session_start ();
	
	include ("../ctf_utils/ctf_includepath.php");
    include (__SITEROOT__."/ctf_utils/ctf_env.php"); 
	require_once(__SITEROOT__.'/ctf_lang/ctf_locale.php');
    require_once('ctf_mail.php');
    require_once('ctf_sql.php');
    require_once('ctf_sql_pdo.php');

    //////////////////////////////////////////////////////////////////////////////
    // MUST HAVE a UID
    //
    if ( ! isset($_SESSION['uid'] )) {
        //echo "Ko, merci de vous connecter.";
        // 401 Unauthorized
        http_response_code(401);
        echo json_encode(array("message" => "Please authenticate."));
        exit();
    }

    function cleanParam($param) {
		return preg_replace("/[^0-9a-zA-Z\-\.]/", "", $param );
	}
    function cleanNames($param) {
		return preg_replace("/[^0-9a-zA-Z \-\.]/", "", $param );
	}    
    function cleanMail($param) {
		return preg_replace("/[^0-9a-zA-Z\-\.@]/", "", $param );
	}
    
    //
    // Resend validation mail
    //
    if (isset($_GET['resendValidationMail'])){
        if ($_SESSION['status']!=='waiting_email_validation') {
            echo "Mail non nécessaire.";
            exit();
        }
        if (ctf_send_validation_mail($_SESSION['uid'], $_SESSION['mail'])) {
            echo "Mail envoyé.";
        } else {
            echo "Mail non envoyé.";
        }
        exit();
    }

    //
    // Set  email address
    //
    if (isset($_GET['setEmail'])){
        $newmail = $_GET['setEmail'];
        // Check email format
        // Warning : "'`whoami`'"@example.com is ok for filter_var()
        // \n\r will be detected
        if (!filter_var($newmail, FILTER_VALIDATE_EMAIL)) {
            // 400 Bad Request
            http_response_code(400);
            echo json_encode(array("message" => "Bad email format."));
            exit();
        }
        $uid = $_SESSION['uid'];
        $pdo_request = "UPDATE users SET mail=:newmail WHERE UID=:uid;";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
        $statement->execute([
            'newmail' => $newmail,
            'uid' => $uid 
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        http_response_code(200);
        echo json_encode(array("message" => "eMail mis à jour."));
        $_SESSION['mail']=$newmail;
        exit();
    }



    function username_exist($name) {
        global $mysqli_pdo;

        $pdo_request = "SELECT * FROM users WHERE login=:name";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'name' => $name,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        $count = $statement->rowCount();
        return ($count>0);
    }


    function username_update($uid, $newname) {
        global $mysqli_pdo;

        $pdo_request = "UPDATE users SET login=:newname WHERE UID=:uid;";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'newname' => $newname,
                'uid' => $uid,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        $count = $statement->rowCount();
        return ($count>0);
    }


    function password_update($uid, $password) {
        global $mysqli_pdo;

        $pdo_request = "UPDATE users SET passwd=:password WHERE UID=:uid;";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'password' => $password,
                'uid' => $uid,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        $count = $statement->rowCount();
        return ($count>0);
    }
    //
    // Set name
    //
    if (isset($_GET['setLogin'])){
        $desiredname = $_GET['setLogin'];

        // Clean some char
        $desiredname = cleanParam($desiredname);

        // Escape string
        $newname = mysqli_real_escape_string($mysqli, $desiredname);

        
        // Check exist ?
        if (username_exist($newname)) {
            // 400 Bad Request
            http_response_code(400);
            echo json_encode(array("message" => "Existing name."));
            exit();
        }

        // Save new name
        $uid = $_SESSION['uid'];
        username_update($uid, $newname);
        http_response_code(200);
        echo json_encode(array("message" => "Login mis à jour."));
        $_SESSION['login']=$newname;
        exit();
    }


    //
    // change Passwd
    //
    if (isset($_GET['setPassword'])) { 
        $desired_passwd = $_GET['setPassword'];

        // Some containts on pwd ?
        if (strlen($desired_passwd)<3) {
            // 400 Bad Request
            http_response_code(400);
            echo json_encode(array("message" => "Too short."));
            exit();
        }

        $newpwd = md5($desired_passwd);
        $uid = $_SESSION['uid'];
        password_update($uid, $newpwd);
        http_response_code(200);
        echo json_encode(array("message" => "Password mis à jour."));
        exit();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // Account must be in state Enabled
    //
    if ( ($_SESSION['status']!=='enabled')) {
        // 401 Unauthorized
        http_response_code(401);
        echo json_encode(array("message" => "Mail non validé ou Compte bloqué."));
        exit();
    }

    function generate_UIDGROUP($len = 5){
        return strtoupper(substr(md5(microtime()),rand(0,26),$len));
    }

    //
    // Create CTF
    //

    function groups_name_exist($name) {
        global $mysqli_pdo;

        $pdo_request = "SELECT * FROM groups WHERE groupname=:name";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'name' => $name,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        $count = $statement->rowCount();
        return ($count>0);
    }
    
    function groups_is_owner($guid, $uid) {
        global $mysqli_pdo;

        $pdo_request = "SELECT * FROM groups WHERE UIDGROUP=:guid AND UIDADMIN=:uid" ;
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'guid' => $guid,
                'uid' => $uid,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        $count = $statement->rowCount();
        return ($count>0);
    }

	function filterParam($param) {
		return preg_replace("/[^0-9a-zA-Z_\-\.]/", "", $param );
	}


    function groups_create($uidgroup, $name, $uid) {
        global $mysqli_pdo;

        $pdo_request = "INSERT into groups (creation_date, UIDGROUP, groupname, UIDADMIN) VALUES (now(), :uidgroup, :name, :uid)";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'uidgroup' => $uidgroup,
                'name' => $name,
                'uid' => $uid,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        $count = $statement->rowCount();
        return ($count>0);
    }


    function groups_join($uidgroup, $uid) {
        global $mysqli_pdo;

        $pdo_request = "INSERT into group_users (UIDGROUP, UID, join_date) VALUES (:uidgroup, :uid, NOW());";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'uidgroup' => $uidgroup,
                'uid' => $uid,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        $count = $statement->rowCount();
        return ($count>0);
    }
    

    function groups_group_exist($guid) {
        global $mysqli_pdo;

        $pdo_request = "SELECT * FROM groups WHERE UIDGROUP=:guid" ;
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'guid' => $guid,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        $count = $statement->rowCount();
        return ($count>0);
    }


    function groups_users_list($guid) {
        global $mysqli_pdo;

        $pdo_request = "SELECT users.UID as UID, users.login as login FROM group_users LEFT JOIN users ON users.UID=group_users.UID WHERE group_users.UIDGROUP=:guid";
        $statement = $mysqli_pdo->prepare($pdo_request);
        $ret=[];
        try {
            $statement->execute([
                'guid' => $guid,
            ]);
            while ($frow = $statement->fetch()) {
                array_push($ret, $frow);
            }
            
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        return ($ret);
    }
    
    if (isset($_GET['createGroup'])){
        $desiredname = $_GET['createGroup'];
        if(strlen($desiredname)<2) {
            // 400 Bad Request
            http_response_code(400);
            echo json_encode(array("message" => "Too short Group name.".$name));
            exit();
        }
		$desiredname = filterParam($desiredname);
        $name_sqlsafe  = $desiredname;//mysqli_real_escape_string($mysqli, $desiredname);
        $name_htmlsafe = $desiredname;//htmlspecialchars($desiredname, ENT_QUOTES| ENT_HTML401);

        // Already exist ?
        $exist  = groups_name_exist($name_sqlsafe);
        if($exist>0) {
            // 400 Bad Request
            http_response_code(400);
            echo json_encode(array("message" => "Existing Group name.".$name_htmlsafe));
            exit();
        }
        $uid = $_SESSION['uid'];
        $creation_date = date("Y-m-d H:i:s");
        $uidgroup = generate_UIDGROUP(5);

        groups_create($uidgroup, $name_sqlsafe, $uid);
        http_response_code(200);
        echo json_encode(array("message" => "Group created."));
    }



    
    //
    // Join Group
    //
    if (isset($_GET['joinGroup'])){
        $guid = $_GET['joinGroup'];
        $guid = cleanParam($guid);

        // Group exist ?
        if(!groups_group_exist($guid)) {
            // 400 Bad Request
            http_response_code(400);
            echo json_encode(array("message" => "Unkown Group Id.".$guid));
            exit();
        }
        
        $uid = $_SESSION['uid'];
        if (groups_join($guid, $uid)) {
            http_response_code(200);
            echo json_encode(array("message" => "Joined Group."));
        } else {
            // 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Error Joined Group."));
        }
    }    
    
    //
    // Leave Group
    //
    if (isset($_GET['leaveGroup'])){
        $desiredname = $_GET['leaveGroup'];
        $desiredname = cleanParam($desiredname);
        $uid = $_SESSION['uid'];

        global $mysqli_pdo;
        $pdo_request = "DELETE from group_users WHERE UIDGROUP=:uidgroup AND UID=:uid";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'uidgroup' => $desiredname,
                'uid' => $uid,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        //$count = $statement->rowCount();
    }    
    if (isset($_GET['delGroup'])){
        $guid = $_GET['delGroup'];
        $guid = cleanParam($guid);
        $uid = $_SESSION['uid'];

        global $mysqli_pdo;
        
        // Must be group manager
        if (!groups_is_owner($guid, $uid)) {
            http_response_code(500);
            echo json_encode(array("message" => "Cant delete: Not owner"));
            die();
        } 
                
        $pdo_request = "DELETE from group_users WHERE UIDGROUP=:uidgroup";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'uidgroup' => $guid,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        
        $pdo_request = "DELETE from groups WHERE UIDGROUP=:uidgroup";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'uidgroup' => $guid,
        ]);
        } catch(PDOException $e) {
            // Send 500 Internal Server Error
            http_response_code(500);
            echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
            die();
        }
        //$count = $statement->rowCount();
    }
    
    // space is ok
    function cleanNamesWithSpaces($param) {
		return preg_replace("/[^0-9a-zA-Z \-\.]/", "", $param );
	} 
    
    if (isset($_GET['groupAddUsers'])){
        $guid = $_GET['groupAddUsers'];
        $guid = cleanParam($guid);
        $names = $_GET['names'];
        $names = cleanNamesWithSpaces($names);
        $uid = $_SESSION['uid'];

       
        // Group exist ?
        if(!groups_group_exist($guid)) {
            // 400 Bad Request
            http_response_code(400);
            echo json_encode(array("message" => "Unkown Group Id.".$name_htmlsafe));
            exit();
        }
        
        // Must be group manager
        if (!groups_is_owner($guid, $uid)) {
            http_response_code(500);
            echo json_encode(array("message" => "Not group owner"));
            die();
        } 
            
        include("db_requests.php");
        $namelist = explode(" ", $names);
        $kolistname = "";
        foreach ($namelist as $name) {
            $nameuid = db_get_uid($name);
            if ($uid == false) { 
                $kolistname = $kolistname." ".$name;
            } else {
                groups_join($guid, $nameuid); 
            }          
        }
        if ($kolistname == "") {
            echo json_encode(array("Message" => "Ok"));
        } else {
            echo json_encode(array("Message" => "Ko list= ".json_encode($kolistname)));
        }
        
    }
    if (isset($_GET['groupUsersList'])){
        $guid = $_GET['groupUsersList'];
        $guid = cleanParam($guid);        
        echo json_encode(groups_users_list($guid));        
    }
    
?>