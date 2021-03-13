<!DOCTYPE html>
<html lang="fr">

<head>
  <title>Ping 'o matic</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script defer src="js/fontawesome.all.js"></script>

  <link rel="stylesheet" href="login.css">



</head>

<body>
    <form class="login-form" action="" method="get">
        <h1>Ping</h1>
        <div class="form-field">
            <i class="fas fa-desktop"></i>
            <input type="text" name="ip" id="ip" class="form-field" value="127.0.0.1" required>
            <label for="ip">IP</label>
        </div>
        <div class="">
        <label id="output"></label>

<?php

    if (isset($_GET['ip'])) {
        $output = shell_exec('ping -c 3 '.$_GET['ip']);
        echo nl2br($output);

    }
?>         
        </div>
    <button type="submit" value="Login" class="btn">Ping</button>
    </form>
</body>
</html>