<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Training: SQLite</title>
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
    if (isset($_POST['cmd'])) {
        exec_cmd("SQLite", $_POST['cmd']);
    }

    ?>


    <div class='panel'>
        <div class='panel-heading'>
            <div class='panel-title'>SQL Training</div>
        </div>   

        <div class='panel-body'>
        
<div class='cmd-title'>SQLite version</div>
<div>
select sqlite_version();
</div>   
<div class='cmd-title'>Database filename</div>
<div>
select file from pragma_database_list where name='main';
</div>  

<div class='cmd-title'>List tables</div>
<div>
SELECT name FROM sqlite_master WHERE type ='table' AND name NOT LIKE 'sqlite_%';
</div>
<div class='cmd-title'>Get Columns</div>
<div>
SELECT sql FROM sqlite_master WHERE name='users';
<br/>
PRAGMA table_info(users);

            </div>
        </div>
        <div class='panel-div'></div>

        <div class='panel-body'>
            <div class='panel-title'>Command</div>
            <form class='' action='' method="post">
                <input class='form-control sql-cmd-input' type='text' name='cmd' placeholder='' value='SELECT * from users' required='required' />
                </br>
                <input type="submit" value="Execute" />
            </form>
        </div>
        <div class='panel-div'></div>

        <div class='panel-body'>
            <div class='panel-title'>Debug logs</div>
            <div>
                <?php echo trainGetLogs(); ?>
            </div>
        </div>
    </div>

</body>

</html>