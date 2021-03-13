<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Training: MySQL/MariaDB</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script defer src="js/fontawesome.all.js"></script>
    <link rel="stylesheet" href="site.css">
</head>


<body>

    <?php

    require_once('train_log.php');
    include 'train_sqli_db.php';

    if (isset($_GET['sqluser'])) { $sqluser=$_GET['sqluser']; } else { $sqluser='root'; }
    if (isset($_GET['waflevel'])) { $waflevel=$_GET['waflevel']; } else { $waflevel='none'; } 
    if (isset($_GET['injecttype'])) { $injecttype=$_GET['injecttype']; } else { $injecttype='cmd'; } 
    if (isset($_GET['outputtype'])) { $outputtype=$_GET['outputtype']; } else { $outputtype='aff'; } 
    if (isset($_GET['errmsg'])) { $errmsg=$_GET['errmsg']; } else { $errmsg='on'; } 
    if (isset($_GET['debugtraces'])) { $debugtraces=$_GET['debugtraces']; } else { $debugtraces='on'; } 
        if (isset($_GET['cmd'])) { $cmd=$_GET['cmd']; } else { $cmd=null; } 
    
    trainClearLog();
    if (isset($cmd)) {
        exec_cmd_mysql($cmd, $sqluser, $waflevel, $injecttype, $outputtype, $errmsg, $debugtraces);
    }

    ?>


    <div class='panel'>
        <div class='panel-heading'>
            <div class='panel-title'>SQL Training</div>
        </div>   

        <div class='panel-body'>
        
        <div class='cmd-title'>MySQL commands</div>
        <pre><code>
    ==== Info system
    SELECT @@version
    SELECT user();
    SELECT system_user();
    SELECT user FROM mysql.user; — priv
    SELECT host, user, password FROM mysql.user; — priv

    === Info DB
    SHOW Databases
    SELECT schema_name FROM information_schema.schemata;
    SELECT table_schema,table_name FROM information_schema.tables WHERE table_schema != 'mysql' AND table_schema != 'information_schema'

    === Exploit 
    SELECT LOAD_FILE('/etc/passwd')
    SELECT * FROM users INTO dumpfile '/var/www/html/bob.php'

        </code></pre>

        <div class='panel-div'></div>
        <?php 
            function isradioselected($label, $sqluser) {
                if ($label===$sqluser) {
                    echo 'checked="checked"';
                }
            }
        ?>
        <div class='panel-body'>
            
            <form class='' action='' method="get">
                <div class='panel-title'>Command</div>
                <input class='form-control sql-cmd-input' type='text' name='cmd' placeholder='' 
                    value='<?php  if (isset($cmd)) {  echo htmlspecialchars($cmd, ENT_QUOTES); } else {  echo "SELECT * from users";  } ?>' required='required' />
                </br>
                <input type="submit" value="Execute" />
                <div class='panel-div'></div>

                <div class='panel-body'>
                    <div class='panel-title'>Output</div>
                    <div><?php echo trainGetLogs(); ?></div>
                </div>
                <div class='panel-div'></div>





                <div class='panel-title'>Context</div>
                <p>SQL user</p>
                <input type="radio" name="sqluser" value="root" <?php isradioselected("root", $sqluser);?> ><label>root</label><br>
                <input type="radio" name="sqluser" value="ctfuser" <?php isradioselected("ctfuser", $sqluser);?> ><label>ctfuser</label><br>
                <br>  
                <p>WAF Level</p>
                <input type="radio" name="waflevel" value="none" <?php isradioselected("none", $waflevel);?> ><label>none</label><br>
                <?php foreach ($blacklist as $key => $value) {?>
                <input type="radio" name="waflevel" value="<?php echo $key; ?>" <?php isradioselected($key, $waflevel);?> ><label>remove blacklist: <?php  echo dumpBlackLists($key)?> </label><br>
                <?php }?>
                <br>  
                <p>Injection type</p>
                <?php foreach ($injections as $key => $value) {?>
                <input type="radio" name="injecttype" value="<?php echo $key; ?>" <?php isradioselected($key, $injecttype);?> ><label><?php echo $value; ?></label><br>
                <?php }?>
                <br>  
                <p>Ouput</p>
                <input type="radio" name="outputtype" value="aff" <?php isradioselected("aff", $outputtype);?> ><label>Aff all results</label><br>
                <input type="radio" name="outputtype" value="checkispositive" <?php isradioselected("checkispositive", $outputtype);?> ><label>Check results>0</label><br>
                <input type="radio" name="outputtype" value="checkisone" <?php isradioselected("checkisone", $outputtype);?> ><label>Check results=1</label><br>
                <br>  
                <p>Error messages</p>
                <input type="radio" name="errmsg" value="on" <?php isradioselected("on", $errmsg);?> ><label>on</label><br>
                <input type="radio" name="errmsg" value="off" <?php isradioselected("off", $errmsg);?> ><label>off</label><br>
                <br> 
                <p>Debug traces</p>
                <input type="radio" name="debugtraces" value="on" <?php isradioselected("on", $debugtraces);?> ><label>on</label><br>
                <input type="radio" name="debugtraces" value="off" <?php isradioselected("off", $debugtraces);?> ><label>off</label><br>
                <br>                 
            </form>
        </div>

    </div>

</body>

</html>