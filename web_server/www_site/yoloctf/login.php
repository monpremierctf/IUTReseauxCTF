<?php
    /*
    INPUT
      $_POST['login']
      $_POST['password']
    */
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    header_remove("X-Powered-By");
    header("X-XSS-Protection: 1");
    header('X-Frame-Options: SAMEORIGIN'); 
    session_start ();
    include "ctf_env.php";
    require_once('ctf_locale.php');
    if (isset($CSRFGuardEnabled)&&($CSRFGuardEnabled === "true")) {
      include "csrfguard.php";
    }

    if (isset($_POST['login']) && isset($_POST['password'])) {

        require_once "ctf_sql_pdo.php";

        $login = $_POST['login'];
        $passwd = md5($_POST['password']);
        
        $query = "SELECT * FROM users WHERE login=:login and passwd = :passwd";
        //echo $request;
        $stmt = $mysqli_pdo->prepare($query);
        $count  = 0;
        if ($stmt->execute(['login' => $login, 'passwd' => $passwd ])) {
          $count  = $stmt->rowCount();
        }
        if($count>0) {
            // dans ce cas, tout est ok, on peut démarrer notre session
            $row = $stmt->fetch();
            // on enregistre les paramètres de notre visiteur comme variables de session ($login et $pwd) (notez bien que l'on utilise pas le $ pour enregistrer ces variables)
            $_SESSION['login'] = $login;
            $_SESSION['uid'] = $row['UID'];
            $_SESSION['status'] = $row['status'];
            $_SESSION['mail']= $row['mail'];
            setcookie("UID", $row['UID'], 0, "/");
            setcookie("CTFUID", $row['UID'], 0, "/");

            require_once('db_log_fct.php');
            insert_log_entry($_SESSION['uid'], "Login", "User [".$_SESSION['login']."] login");
            // on redirige notre visiteur vers une page de notre section membre
            header ('location: index.php');
        }
        else {
            // Le visiteur n'a pas été reconnu comme étant membre de notre site. On utilise alors un petit javascript lui signalant ce fait
            echo '<body onLoad="alert(\''.getLocalizedLabel("login_invalid_credentials").'\')">';
            // puis on le redirige vers la page de login
            echo '<meta http-equiv="refresh" content="0;URL=login.php">';
        }
    }
    
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Y0L0 CTF</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/yoloctf/js/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
  <script src="/yoloctf/js/jquery.min.js"></script>
</head>
<body>

<?php
    include "ctf_challenges.php";
    include "Parsedown.php";
    $Parsedown = new Parsedown();
?>

<div class="jumbotron ctf-title text-center">
<h1 class="ctf-title-size">Y0L0 CTF</h1>
<p ><pre class="ctf-subtitle-size">Mon premier CTF</pre></p> 
</div>


<div class="row-sm-3">
<div class="col-sm-3">
  <p>
<?php
//echo "Participants connectés : ";
//echo  get_active_users();
?>
</p>
</div>
</div>

<div class="row">
    <div class="col-sm-1">
      
    </div>
    <div class="col-sm-4 text-center">
      <h3><?php print getLocalizedLabel("login_with_account") ?></h3>
      <p><img src="img/player_02_200.png" alt="Participant"></p>
      <form action="login.php"  method="post">
		<div class="form-group text-left">
		  <label for="usr">Login</label>
		  <input type="text" class="form-control" id="login" name="login">
        </div>
        <div class="form-group text-left">
		  <label for="usr">Password</label>
		  <input type="password" class="form-control" id="password" name="password">
  
      <?php  if (isset($ReCaptchaEnabled)&&($ReCaptchaEnabled === 'true')) { ?>
      <script src="https://www.google.com/recaptcha/api.js" async defer></script>
      <div class="field">
          <label for="usr">Verification</label>
					<div class="g-recaptcha" data-sitekey="6LdAw9EUAAAAAGLowra3GcVcI-gfCk7B1465Q0z3"></div>
        </div>
      <?php } ?>
       
    </div>
		<button type="submit" class="btn btn-primary">Login</button>
	  </form>
    </div>
    <div class="col-sm-4 text-center">
      <h3><?php print getLocalizedLabel("login_without_account") ?></h3>        
      <p><img src="img/admin_02_200.png" alt="Anonymous"></p>
      <form action="register.php"  method="post">
		<div class="form-group text-center">
    <?php print getLocalizedLabel("login_create_account") ?>
		</div>
		<button type="submit" class="btn btn-primary">Register</button>
	  </form>
    </div>
  </div>
</div>
  


  
</body>
</html>



