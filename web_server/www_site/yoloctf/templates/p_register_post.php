<?php
/*
    INPUT: 
        $_GET['validate']
        $_POST['login']
        $_POST['password']
        $_POST['code']
        $_POST['mail']
        $_POST['pseudo']
    CMD: 
        none
	GLOBAL : $_SESSION

    */
    

    include "ctf_env.php";
    if (isset($CSRFGuardEnabled)&&($CSRFGuardEnabled === "true")) {
      include "csrfguard.php";
    }




    include "ctf_sql_pdo.php";
    $uid = $_SESSION['uid'];

    //
    // Validate account
    // Url received by mail
    // https://localhost/yoloctf/register.php?validate=5d60eb2b3bbd4
    // status : waiting_email_validation -> enabled
    //
    if (isset($_GET['validate'])) {
          $arg_uid = $_GET['validate'];

        // Validate the loggued profile ?
        if ($_GET['validate'] === $uid){
            $_SESSION['status'] = 'enabled';
        }
        
        $query = "UPDATE users SET status = 'enabled' WHERE UID=:arg_uid AND status='waiting_email_validation'; ";
        $stmt = $mysqli_pdo->prepare($query);
        $count  = 0;
        if ($stmt->execute(['arg_uid' => $arg_uid ])) {
            $count  = $stmt->rowCount();
            //echo $count;
            header ('location: index.php?p=Welcome_validated');
            exit();
        } else {
            
            printf("Update failed");
            exit();
        }
    }

    //
    // Register a new account
    //
    if (isset($_POST['login']) && isset($_POST['password']) ) {

        // Login, passwd too short ??
        if ((strlen($_POST['login'])<2) or (strlen($_POST['password'])<2)) {
            echo '<body onLoad="alert(\'Login ou mot de passe un peu court...\')">';
            echo '<meta http-equiv="refresh" content="0;URL=pages.php?p=Register">';
            exit();
        }

        // Invitation Code ??
        if (isset($ctf_register_code)&&($ctf_register_code!='')) {
            if(strtoupper($_POST['code'])!==strtoupper($ctf_register_code)) {
                echo '<body onLoad="alert(\'Code Invitation invalide\')">';
                echo '<meta http-equiv="refresh" content="0;URL=pages.php?p=Register">';
                exit();
            }
        }

         // CTF Code ??
         if (isset($_POST['code'])&&($_POST['code']!='')) {
            
        }
        
        include "ctf_mail.php";

        $login = $_POST['login'];
        $passwd = md5($_POST['password']);
        $mail = $_POST['mail'];
        $pseudo =  $_POST['pseudo'];


        // Login already exist ?
        include ("db_requests.php");
        if(db_login_exists($login)) {
            echo '<body onLoad="alert(\'Ce login est déjà existant\')">';
            echo '<meta http-equiv="refresh" content="0;URL=register.php">';
        }
        else {
            // On sauve tout ça dans la base
            $uid = uniqid ("");
            $status = 'enabled';
            // Send mail ?
            if ($ctf_require_email_validation =='true'){
                $status = 'waiting_email_validation';
                $to = $_POST['mail'];
                ctf_send_validation_mail($uid, $to);  
                
            }
            $_SESSION['status'] = $status;
            setcookie("UID", $uid, 0, "/");
            setcookie("CTFUID", $uid, 0, "/");


            $query = "INSERT into users (login, passwd, mail, pseudo, UID, status) 
                VALUES (:login, :passwd, :mail, :pseudo, :uid, :status)";
            //$result = $mysqli->query($request);
            //$count  = $result->affected_rows;
            $stmt = $mysqli_pdo->prepare($query);
            if ($stmt->execute([
                    'login' => $login, 
                    'passwd' => $passwd,
                    'mail' => $mail,
                    'pseudo' => $pseudo,
                    'uid' => $uid,
                    'status' => $status,
                ])) {
                // on enregistre les paramètres de notre visiteur comme variables de session 
                $_SESSION['login'] = $login;
                $_SESSION['uid'] = $uid;
                // on redirige notre visiteur vers une page de bienvenue
                if ($ctf_require_email_validation =='true'){
                    header ('location: pages.php?p=Welcome_waiting_validation');
                } else {
                    header ('location: pages.php?p=Welcome_validated');
                }
            } else {
                echo $request;
                printf("Insert failed\n");
                exit();
            }
            // create user Network
            //$dummy = file_get_poke('http://challenge-box-provider:8080/createChallengeBox/?uid='.$_SESSION['uid'].'&cid=1');
        }
    }
?>