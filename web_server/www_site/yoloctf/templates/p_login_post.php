<?php
/*
    INPUT
      $_POST['login']
      $_POST['password']
    */


include(__SITEROOT__ . "/ctf_utils/ctf_env.php");
require_once(__SITEROOT__ . "/ctf_lang/ctf_locale.php");

if (isset($CSRFGuardEnabled) && ($CSRFGuardEnabled === "true")) {
  include(__SITEROOT__ . "/vendor/csrfguard/csrfguard.php");
}

if (isset($_POST['login']) && isset($_POST['password'])) {

  require_once(__SITEROOT__ . "/ctf_utils/ctf_sql_pdo.php");

  $login = $_POST['login'];
  $passwd = md5($_POST['password']);

  $query = "SELECT * FROM users WHERE login=:login and passwd = :passwd";
  //echo $request;
  $stmt = $mysqli_pdo->prepare($query);
  $count  = 0;
  if ($stmt->execute(['login' => $login, 'passwd' => $passwd])) {
    $count  = $stmt->rowCount();
  }
  if ($count > 0) {
    // dans ce cas, tout est ok, on peut démarrer notre session
    $row = $stmt->fetch();
    // on enregistre les paramètres de notre visiteur comme variables de session ($login et $pwd) (notez bien que l'on utilise pas le $ pour enregistrer ces variables)
    $_SESSION['login'] = $login;
    $_SESSION['uid'] = $row['UID'];
    $_SESSION['status'] = $row['status'];
    $_SESSION['hacklab'] = $row['hacklab'];
    $_SESSION['accesserver'] = "Server2";
    $_SESSION['mail'] = $row['mail'];
    setcookie("UID", $row['UID'], 0, "/");
    setcookie("CTFUID", $row['UID'], 0, "/");

    require_once('db_log_fct.php');
    insert_log_entry($_SESSION['uid'], "Login", "User [" . $_SESSION['login'] . "] login");
    // on redirige notre visiteur vers une page de notre section membre
    header('location: index.php');
  } else {
    // Le visiteur n'a pas été reconnu comme étant membre de notre site. On utilise alors un petit javascript lui signalant ce fait
    echo '<body onLoad="alert(\'' . getLocalizedLabel("login_invalid_credentials") . '\')">';
    // puis on le redirige vers la page de login
    echo '<meta http-equiv="refresh" content="0;URL=index.php">';
  }
}

?>
