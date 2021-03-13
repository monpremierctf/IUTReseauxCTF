<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Training: SQLi</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script defer src="js/fontawesome.all.js"></script>

    <link rel="stylesheet" href="site.css">
</head>



<body>

    <?php
    require_once('train_log.php');
    include 'train_sqli_db.php';

    trainClearLog();
    if (isset($_POST['login'])) {
        trainLogHtml("POST : login=" . $_POST['login']);
        trainLogHtml( "Check " . check_authen($_POST['login'], "123456"));
    }
    ?>
    <div class='panel'>
        <div class='panel-heading'>
            <div class='panel-title'>Login</div>
        </div>
        <div class='panel-body'>
            <form class='' action='' method="post">
                <input class='form-control' type='login' name='login' placeholder='login' required='required' />
                </br>
                <input class='form-control' type="passwd" name="passwd" placeholder='password' />
                </br>
                <input type="submit" value="Submit" />
            </form>
        </div>
        <div class='panel-div'>
        </div>

        <div class='panel-body'>
            <div class='panel-title'>Debug logs</div>
            <div>
                <?php echo trainGetLogs(); ?>
            </div>
        </div>
    </div>

</body>

</html>