<?php
/*
    INPUT: none
    CMD: 
        $_GET['clearFlags']
        $_GET['importParticipants']
	GLOBAL : $_SESSION

	*/

?>


	<style>
		canvas {
			-moz-user-select: none;
			-webkit-user-select: none;
			-ms-user-select: none;
		}
	</style>





<?php
    
    include 'ctf_input.php';

    function dumpUserList(){
        include "ctf_sql.php";
        $user_query = "SELECT login, UID FROM users;";
        if ($result = $mysqli->query($user_query)) {
            echo "Nb users : ".$result->num_rows."</br>";
            while ($row = $result->fetch_assoc()) {
                $uid = $row['UID'];
                $login = $row['login'];
                echo "[".htmlspecialchars($login)."]  ".$uid."
					<a href='index.php?p=Zen&deleteuid=".$uid."'>[Delete User]</a>
					<a href='index.php?p=Zen&deleteuidflags=".$uid."'>[Delete User Flags]</a>
					</br>";	
            }
            $result->close();
        }
        $mysqli->close();
    }


    function dumpUserFlags() {
        include "ctf_sql.php";
		$user_query = "SELECT login, UID FROM users;";
		if ($user_result = $mysqli->query($user_query)) {
			while ($row = $user_result->fetch_assoc()) {
				$uid = $row['UID'];
				$login = $row['login'];
                echo "</br><u>[".htmlspecialchars($login)."]  ".$uid."</u></br>";	
                $query = "SELECT UID,CHALLID, fdate, isvalid, flag FROM flags WHERE UID='$uid';";
                if ($fresult = $mysqli->query($query)) {
                   
                    while ($frow = $fresult->fetch_assoc()) {
                        $chall = getChallengeById($frow['CHALLID']);
                        if ($frow['isvalid']) {
                            printf ("%s %s-%s: ok <a href='index.php?p=Zen&deleteflag=%s&uid=%s&isvalid=true'>[Delete Flag]</a></br>", $frow['fdate'], $frow['CHALLID'], $chall['name'],$frow['CHALLID'], $uid);
                        } else {
						printf ("%s %s-%s: ko [%s] <a href='index.php?p=Zen&deleteflag=%s&uid=%s&isvalid=false'>[Delete Flag]</a></br>", $frow['fdate'], $frow['CHALLID'],$chall['name'], htmlspecialchars($frow['flag']),$frow['CHALLID'], $uid);
                        }
                    }
                    $fresult->close();	
                }		
			}
			$user_result->close();
		}
		$mysqli->close();
    }
    
    function clearFlags(){
        include "ctf_sql.php";
		$query = "DELETE FROM flags;";
		if ($result = $mysqli->query($query)) {
			
		}
		$mysqli->close();

    }

    function clearUsers(){
        include "ctf_sql.php";
        clearFlags();
		$query = "DELETE FROM users  where login!='$admin';";
		if ($result = $mysqli->query($query)) {
			
		}
		$mysqli->close();

    }


	function deleteUIDFlags($uid) {
		include "ctf_sql_pdo.php";
        $query = "DELETE FROM flags WHERE UID=:uid ; ";
        $stmt = $mysqli_pdo->prepare($query);
        $count  = 0;
        if ($stmt->execute([
            'uid' => $uid 
            ])) {
            $count  = $stmt->rowCount();
			echo "Deleted $count rows</br>";
        } else {            
            printf("Delete failed");
            exit();
        }

		return;
	}
    
    
    function upgradeflag($oldchallid, $newchallid){
        include "ctf_sql_pdo.php";

        $pdo_request = "UPDATE flags SET CHALLID=:newid WHERE CHALLID=:oldid;";
        $statement = $mysqli_pdo->prepare($pdo_request);
        try {
            $statement->execute([
                'newid' => $newchallid,
                'oldid' => $oldchallid,
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


    function upgradeflags(){
        global $challenges;
        if (!$challenges) return null;
        foreach ($challenges['results'] as $c) {
            print($c['oldid']." =&gt ".$c['id']." : ");
            print(upgradeflag($c['oldid'], $c['id']));
            print("<br />");
        }
    }

	function deleteUID($uid) {
		include "ctf_sql_pdo.php";
        $query = "DELETE FROM users WHERE UID=:uid ; ";
        $stmt = $mysqli_pdo->prepare($query);
        $count  = 0;
        if ($stmt->execute([
            'uid' => $uid 
            ])) {
            $count  = $stmt->rowCount();
        } else {            
            printf("Delete failed");
            exit();
        }
        $query = "DELETE FROM flags WHERE UID=:uid ; ";
        $stmt = $mysqli_pdo->prepare($query);
        $count  = 0;
        if ($stmt->execute([
            'uid' => $uid 
            ])) {
            $count  = $stmt->rowCount();
        } else {            
            printf("Delete failed");
            exit();
        }

		return;
	}
	
	
	function deleteFlag($challid, $uid, $isvalid) {
        include "ctf_sql_pdo.php";
        $query = "DELETE FROM flags WHERE CHALLID=:challid AND UID=:uid AND isvalid=:isvalid; ";
        $stmt = $mysqli_pdo->prepare($query);
        $count  = 0;
        if ($stmt->execute([
			'challid' => $challid,
            'uid' => $uid,
			'isvalid' => $isvalid=='true'?True:False
            ])) {
            $count  = $stmt->rowCount();
			echo "Deleted $count rows</br>";
        } else {            
            printf("Delete failed");
            exit();
        }
		return;
	}
	
	
    function dumpUserContainersList($cont){
        include "ctf_sql.php";
        $user_query = "SELECT login, UID FROM users;";
        if ($result = $mysqli->query($user_query)) {
            while ($row = $result->fetch_assoc()) {
                $uid = $row['UID'];
                $login = $row['login'];
                echo "<u>[".htmlspecialchars($login)."]  ".$uid."</u></br>";	
                if ($cont != null)	{
                    foreach ($cont as $c) {
                        if ('CTF_UID_'.$uid == $c->Uid) {
                            echo "    - ".$c->Name."</br>";
                        }
                    }
                }
            }
            $result->close();
        }
        $mysqli->close();
    }



    function register($login, $password, $mail) {
        include "ctf_sql_pdo.php";
        $uid = uniqid ("");
        $status = 'enabled';
        echo "[$login, $password, $mail] ";
        $query = "INSERT into users (login, passwd, mail, pseudo, UID, status) 
                    VALUES (:login, :passwd, :mail, :pseudo, :uid, :status)";
        $stmt = $mysqli_pdo->prepare($query);
        if ($stmt->execute([
                'login' => $login, 
                'passwd' => md5($password),
                'mail' => $mail,
                'pseudo' => $login,
                'uid' => $uid,
                'status' => $status,
            ])) {
            echo "ok</br>";
        } else {
            echo $request;
            echo("Insert failed\n");
        }	
    }


function setHacklab($login, $hacklab) {
    include "ctf_sql_pdo.php";

    $name = clean_string($name);
    $hacklab = clean_string($hacklab);
    // UPDATE users SET hacklab='hacklab02' WHERE login='hl2';
    $pdo_request = "UPDATE users SET hacklab='$hacklab' WHERE login='$login';";
    echo "[$login] set lab : $hacklab<br/>";
    $statement = $mysqli_pdo->prepare($pdo_request);
    try {
        $statement->execute();
    } catch(PDOException $e) {
        // Send 500 Internal Server Error
        http_response_code(500);
        echo json_encode(array("message" => "Erreur sql [".$e->getMessage()."] "));
        die();
    }
    $count = $statement->rowCount();
    echo "=>$count";
    return ($count>0);
}



	function cleanlogs() {
        include "ctf_sql_pdo.php";
        $query = "DELETE FROM logs WHERE fdate < DATE_SUB(NOW(), INTERVAL 1 MONTH);";
        $stmt = $mysqli_pdo->prepare($query);
        $count  = 0;
        if ($stmt->execute()) {
            $count  = $stmt->rowCount();
			echo "Deleted $count rows</br>";
        } else {            
            printf("Delete failed");
            exit();
        }
		return;
	}
    
    
function password_update($login, $password) {
    include "ctf_sql_pdo.php";

    $pdo_request = "UPDATE users SET passwd=:password WHERE login=:login;";
    $statement = $mysqli_pdo->prepare($pdo_request);
    try {
        $statement->execute([
            'password' => md5($password),
            'login' => $login,
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



function importIUT(){
	$classelist = "aiman.abdelkader-el-rifai	aciddagger44	aiman.abdelkader@gmail.com
lucas.cazottes	bluemonkey89	lucas.cazottes@live.fr
lilian.chevre	quaketrinity25	chevrelilian@gmail.com
lucas.courthieu	quakeoracle86	lucascourthieu@orange.fr
quentin.de-alphonso	trixydagger36	quentin.dealphonso@gmail.com
quentin.de-grenier	redsentinel37	quentin.de-grenier@outlook.fr
jeremy.de-leplaire	hardparticle23	jeremy.deleplaire@gmail.com
antoine.deleris	bluewebster63	delerisantoine@gmail.com
arthur.dupre	finemoonlight84	arthur.dupre000@gmail.com
mathieu.goudy	prudentsphinx88	matgoudy@live.fr
guillaume.grassaud1	facepalm42	guibenga@hotmail.com
vincent.jacquier	darklimbo94	vincentjacquier.0406@gmail.com
nicolas.jadouin	reversemonkey23	NICOLAS.JADOUIN@GMAIL.COM
pablo.juvigny	furyavatar59	pablo.jny@hotmail.com
mathieu.laboureau-bernat	silveroddity80	mathieu.laboureau@hotmail.fr
lucas.lacroix	doppelganger42	Lacroix.Lucas@outlook.com
julie.lamagnere	reversecode46	julie.lamagnere@sfr.fr
florian.lavaud	opencloud40	ck.lavaud@orange.fr
damien.le-deun	quirkavenger24	damienledeun@hotmail.fr
thomas.lejeune	moonlightshadow85	thomas.lejeunebasse@gmail.com
kevin.marut	bulletenigma26	kevin.marut@hotmail.fr
noe.nguyen-van-lan	goldarchangel79	noe.nguyen1999@gmail.com
kelvin.pacaud	robertdrake67	kelvin.pacaud319@gmail.com
luc.potigny	simpletrinity76	luc.potigny@outlook.fr
pierre.raynaud	smogbullet58	pierre061108@gmail.com
arnaud.roquebert	londonkid89	arnaud.roquebert@gmail.com
gael.rousseau	atommania35	rousseau.gael@hotmail.com
cedric.rozes	friendblazer34	cedric.rozes@gmail.com		
alain.roux	smogoddity74	alain.roux@univ-tlse2.fr 
fabrice.peyrard	splintershadow94	Fabrice.Peyrard@univ-tlse2.fr 
sebastien.josset	yellowcoconut	sebastien.josset@gmail.com
chantal.labat	sweetcloud862	chantal.labat@univ-tlse2.fr";

$group_LP = "
aiman.abdelkader-el-rifai
lucas.cazottes
lilian.chevre	
lucas.courthieu	
quentin.de-alphonso	
quentin.de-grenier	
jeremy.de-leplaire	
antoine.deleris	
arthur.dupre	
mathieu.goudy	
guillaume.grassaud1	
vincent.jacquier 
nicolas.jadouin	
pablo.juvigny	
mathieu.laboureau-bernat	
lucas.lacroix	
julie.lamagnere	
florian.lavaud	
damien.le-deun	
thomas.lejeune	
kevin.marut	
noe.nguyen-van-lan	
kelvin.pacaud	
luc.potigny	
pierre.raynaud	
arnaud.roquebert	
gael.rousseau	
cedric.rozes			
";
	foreach(preg_split("/((\r?\n)|(\r\n?))/", $classelist) as $line){
		$fields = explode("\t", $line);
    	//register($fields[0],$fields[1],$fields[2]);
		password_update($fields[0],$fields[1]);
	}
	
}
?>



<?php
    
    
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



    if (isset($_SESSION['login'] )) {
        // $admin from ctf_env.php
        if (($_SESSION['login']=== $admin )) {
            // Actions
            if (isset($_GET['clearFlags'])){
                clearFlags();
            }
            if (isset($_GET['deleteuid'])){
                deleteUID($_GET['deleteuid']);
            }
            if (isset($_GET['deleteuidflags'])){
                deleteUIDFlags($_GET['deleteuidflags']);
            }
			//deleteflag=%s&uid=%s&isvalid=false
			if (isset($_GET['deleteflag'])){
                deleteFlag($_GET['deleteflag'],$_GET['uid'],$_GET['isvalid']);
            }
			if (isset($_GET['importiut'])){
                //importIUT();
            }	
			if (isset($_GET['upgradeflags'])){
                //upgradeflags();
                exit();
            }
?>
            
            <a href="index.php?p=Zen&cleanlogs" ><pre class="ctf-menu-color">[Delete logs older than 1 month]</pre></a>

<?php
            if (isset($_GET['cleanlogs'])){
                cleanlogs();
                exit();
            }  
?>
            <script>
            function clean_string(str) {
                var str_clean = str.replace(/[^a-zA-Z0-9_\-.]+/g, '');
                return str_clean;
            }
            function clean_mail(str) {
                var str_clean = str.replace(/[^a-zA-Z0-9_\-.@]+/g, '');
                return str_clean;
            }
            function validateEmail(email) {
              var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
              return re.test(email);
            }
            function registerUser(){
                login = document.getElementById("create-login").value; 
                password = document.getElementById("create-password").value; 
                mail = document.getElementById("create-mail").value;
                
                login = clean_string(login).trim();
                password = clean_string(password).trim();
                mail = clean_mail(mail).trim();
                
                if (login.length==0 || password.length==0 || mail.length==0 ) { alert ("Champ vide"); return; }
                
                document.getElementById("create-login").value = login;
                document.getElementById("create-password").value = password;

                if (validateEmail(mail)) {
                    document.getElementById("create-mail-ok").innerHTML  = "ok";
                } else {
                    document.getElementById("create-mail-ok").innerHTML = "ko";
                    alert ("email invalide");
                    return;
                }


                var getdata = {
                    'login': login,
                    'password': password,
                    'mail': mail,
                }
                $.get("p_vm_api.php?Adduser", getdata)
                    .done(function(data) {
                        alert(data);
                    });
               
            }
            function createKaliUser(){
                login = document.getElementById("create-login").value; 
                password = document.getElementById("create-password").value; 
                
                login = clean_string(login).trim();
                password = clean_string(password).trim();
                document.getElementById("create-login").value = login;
                document.getElementById("create-password").value = password;

                var getdata = {
                    'login': login,
                    'password': password,
                }
                $.get("p_vm_api.php?Adduser", getdata)
                    .done(function(data) {
                        alert(data);
                    });
                
            }
            </script>
            <h3>Register User</h3> 
            <div 

              <label for="fname">Login</label><br> 
              <input id="create-login" type="text"><br>
              <label for="lname">Password</label><br>
              <input id="create-password" type="text"><br>
              <label for="lname">Mail</label><br>
              <input id="create-mail" type="text"><label id="create-mail-ok" for="lname"></label><br><br>
              <input type="submit" value="Register website" onClick="registerUser()">
              <input type="submit" value="Create Kali account" onClick="createKaliUser()">
            </div> 
            





            <h3>Set HackLab</h3>
            <form action="index.php?p=Zen" method="GET">
              <input hidden="true" type="text" name="p" value="Zen" >
              <label for="fname">User name</label><br>
              <input type="text" name="name" ><br>
              <label for="lname">Hacklab</label><br>
              <input type="text" name="sethacklab" value="hacklab02"><br><br>
              <input type="submit" value="Submit">
            </form> 

<?php           
            if (isset($_GET['sethacklab'])&&isset($_GET['name'])){
                setHacklab($_GET['name'],$_GET['sethacklab']);
                exit();
            }
            
            
            
            
            
            
            
            
            
            // Get containers
            $url = 'http://challenge-box-provider:8080/listChallengeBox/';
            $json = file_get_contents_curl($url);
            $cont = json_decode($json);

            echo "<h3>Php sessions</h3> ";
            echo "Nb sessions : ". get_active_users();

            echo "<h3>Users</h3>
                <div class='panel panel-primary'>
                    <div class='panel-body bg-light' style='height: 300px; overflow-y: scroll;'> ";
            dumpUserList();
            print "</div></div></br>";

            echo "<h3>Flags submited</h3> 
                <div class='panel panel-primary'>
                    <div class='panel-body bg-light' style='height: 300px; overflow-y: scroll;'> ";
            dumpUserFlags();
            print "</div></div></br>";
            
            echo "<h3>Containers</h3> ";
            echo "Nb Containers = ".count($cont)."</br>
                <div class='panel panel-primary'>
                    <div class='panel-body bg-light' style='height: 300px; overflow-y: scroll;'> ";
            dumpUserContainersList($cont);
            print "</div></div></br>";

            echo "</br>";
            
            echo "<h4>BDD</h4>";
                    ?>

                            <td><button type="submit" class="btn btn-primary" onclick="return onClearFlags()">[ClearFlags]</button></td>
                            <script>
                                function onClearFlags() {
                                    if (!confirm("Clear ALL flags ?")) {
                                        return;
                                    }
                                    var getdata = {
                                        'clearFlags': "1",
                                    }
                                    $.get("index.php?p=Zen", getdata)
                                        .done(function(data) {
                                            alert("Done");
                                        });
                                }
                            </script>

                    <?php

                            echo "<h3>Env</h3> ";
                            echo "<div class='panel panel-primary'><div class='panel-body bg-light' style='height: 300px; overflow-y: scroll;'> ";
                            foreach (getenv() as $key => $value) {
                                echo "$key=$value<br />";
                            }
                            print "</div></div></br>";

                            print "<h3>CSRF Enabled:";
                            echo json_encode($_ENV["CTF_CSRFGUARD_ENABLED"]);
                            print "</h3> ";
                            print '<a href="index.php?p=Zen&CSRFEnable" ><pre class="ctf-menu-color">[CSRFEnable]</pre></a> ';
                            print '<a href="index.php?p=Zen&CSRFDisable" ><pre class="ctf-menu-color">[CSRFDisable]</pre></a> ';


                            if (isset($_GET['FlagValidationAllowed'])) {
                                file_put_contents("isFlagValidationAllowed.cfg", "true");
                            }
                            if (isset($_GET['FlagValidationClosed'])) {
                                file_put_contents("isFlagValidationAllowed.cfg", "false");
                            }
                            print "<h3>Soumission de flag authorisée:";
                            $isFlagValidationAllowed = file_get_contents("isFlagValidationAllowed.cfg");
                            echo $isFlagValidationAllowed;
                            print "</h3> ";
                            print '<a href="index.php?p=Zen&FlagValidationAllowed" ><pre class="ctf-menu-color">[Authoriser la soumission de flag]</pre></a> ';
                            print '<a href="index.php?p=Zen&FlagValidationClosed" ><pre class="ctf-menu-color">[Interdire la soumission de flag]</pre></a> ';
                      

        } else {

        }

            

    } else {
        //echo "Merci de vous connecter.";
    }



 
?>




