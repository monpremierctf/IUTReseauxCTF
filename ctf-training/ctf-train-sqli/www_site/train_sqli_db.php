<?php 

    session_start();

/*
    if (! isset($_SESSION['count'])) { $_SESSION['count']=0;}
    echo 'Count : '.$_SESSION['count'];
    $_SESSION['count'] = $_SESSION['count'] +1;
*/

    // Create db 
    if (!file_exists('./db_train_sqli.sqlite')) {
        $db = new SQLite3('./db_train_sqli.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $result = $db->query('CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                login VARCHAR,
                password VARCHAR
            );');
        $resultf = $db->query('INSERT INTO users (login,password) VALUES ("admin","123456");');
        $resultf = $db->query('INSERT INTO users (login,password) VALUES ("max","princess");');
        $resultf = $db->query('INSERT INTO users (login,password) VALUES ("ori","forest");');

        $resultf = $db->query('CREATE TABLE IF NOT EXISTS flags (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            flag VARCHAR
        );');
        $resultf = $db->query('INSERT INTO flags (flag) VALUES ("flag_trop_bien_ranger");');
    }


    function check_authen($login, $pass) {
        trainLogHtml("Checking login=[$login], password=[$pass]");
        $db = new SQLite3('./db_train_sqli.sqlite', SQLITE3_OPEN_READONLY);
        if ($db) {
            trainLogHtml( "building SQL Query");
            $query= "SELECT login FROM  users WHERE login='$login' AND password='$pass';";
            trainLogHtml( "SQL Query = [$query]");
            $res = $db->query($query);
            if (! $res) {
                trainLogHtml( "Error: ".$db->lastErrorMsg());
            } else {
                $count=0;
                $login = ""; 
                while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
                    trainLogHtml( $row['login']); 
                    $count++;
                    if ($login=="") { $login = $row['login']; }
                } 
                trainLogHtml( "Count=$count");
                if ($count>0) { 
                    return $login; 
                }
            }
        }
        return False;
    }

    function init_mysql($mysqli, $dbname)
    {
        $sql = 'CREATE DATABASE '.$dbname;
        if ($mysqli->query($sql)) {
            //echo "Database my_db created successfully\n";
        } else {
            echo 'Error creating database: ' .$mysqli->error . "\n";
        }
        $db_selected = $mysqli->select_db($dbname);

        //$res = $mysqli->query('DROP table users;');
        $res = $mysqli->query('CREATE TABLE IF NOT EXISTS users (
            id INT NOT NULL AUTO_INCREMENT, 
            login VARCHAR(45) NULL, 
            password VARCHAR(45) NULL,
            PRIMARY KEY (id)
        );');
        //if (! $res )  {  echo( "Error: ".$mysqli->error);    }
        $resultf = $mysqli->query('INSERT INTO users (login,password) VALUES ("admin","123456");');
        $resultf = $mysqli->query('INSERT INTO users (login,password) VALUES ("max","princess");');
        $resultf = $mysqli->query('INSERT INTO users (login,password) VALUES ("ori","forest");');

        $res = $mysqli->query('CREATE TABLE IF NOT EXISTS flags (
            id INT NOT NULL AUTO_INCREMENT,
            flag VARCHAR(45) NULL,
            PRIMARY KEY (id)
        );');
        //if (! $res )  {  echo( "Error: ".$mysqli->error);    }
        $resultf = $mysqli->query('INSERT INTO flags (flag) VALUES ("flag_trop_bien_ranger");');

        $resultf = $mysqli->query("CREATE USER 'ctfuser'@'%' IDENTIFIED BY '123456';");
        $resultf = $mysqli->query("GRANT SELECT, INSERT, UPDATE, DELETE ON $dbname.* TO 'ctfuser'@'%';");
    }
    function reset_mysql()
    {

    }

    //
    // WAF
    //
    $blacklist = [ 
        'removeblacklistspace' => [" "],
        'removeblacklistquote' => ["'"],
        'removeblacklist2' => ["SELECT", "FROM", "INSERT", "WHERE"],
        'removeblacklist3' => [" ", ";", "/", "*", "SELECT", "FROM", "INSERT", "WHERE"],
    ];
    function dumpBlackLists($index) {
        global $blacklist;
        return ("[".implode("][", $blacklist[$index])."]");
    }
    
    function apply_waf($cmd, $waflevel, $debugtraces){
        global $blacklist;
        //trainLogHtml("Waf: $waflevel");


        if ($waflevel=='none') {
            if ($debugtraces=='on') trainLogHtml("Waf: Desactivated");
            return $cmd;
        }
        if (array_key_exists($waflevel, $blacklist)) {
            if ($debugtraces=='on') trainLogHtml("Waf: $waflevel [".implode("][", $blacklist[$waflevel])."]");
            $cmd = str_replace($blacklist[$waflevel], "", $cmd, $count);
            if ($count>0) {
                if ($debugtraces=='on') trainLogHtml("Waf: removed $count elements");
            } else {
                if ($debugtraces=='on') trainLogHtml("Waf: Ok, nothing found");
            }
            return $cmd;
        }
        return $cmd;
    }


    //
    // Injection
    //
    $injections = [ 
        'cmd' => "SQLI",
        'selectint' => "SELECT * from users WHERE id=SQLI",
        'selectstring' => "SELECT * from users WHERE login='SQLI'",
        'selectstring2' => "SELECT * from users WHERE login='SQLI' and password='123456'",
    ];
    
    function apply_injection($cmd, $injectiontype){
        global $injections;
        //trainLogHtml ("apply_injection: $cmd, $injectiontype");

        if (array_key_exists($injectiontype, $injections)) {
            $sql = $injections[$injectiontype];
            $cmd = str_replace("SQLI", $cmd, $sql);
        }
        return $cmd;
    }


    function exec_cmd_mysql($cmd, $sqluser, $waflevel, $injecttype, $outputtype, $errmsg, $debugtraces) {
        
        // Connect DB
        $host = 'localhost';
        if ($sqluser==='ctfuser') {
            $user = 'ctfuser';
            $pass = '123456';
        } else {
            $user = 'root';
            $pass = 'root';
        }
        $dbname="ctfdb";
        $mysqli = new mysqli($host, $user, $pass);
        if ($mysqli->connect_error) {
            trainLogHtml("Connection failed: " . $conn->connect_error);
        } else {
            trainLogHtml( "Connected as user:  [$user]");
        }

        // DB: ctfdb
        $db_selected = $mysqli->select_db($dbname);
        if (!$db_selected) {
            init_mysql($mysqli, $dbname);            
        }

        // WAF
        $cmd = apply_waf($cmd, $waflevel,$debugtraces);

        // Injection
        $cmd = apply_injection($cmd, $injecttype);

        // Query
        $query= $cmd;
        if ($debugtraces=='on') {
            trainLogHtml( "SQL Query: [$query]");
        }

        $res = $mysqli->query($cmd);
        if (! $res )  {
            if ($errmsg !== 'off') {
                trainLogHtml( "Error: ".$mysqli->error);
            }
        } else {
            $count = 0;
            if ( gettype($res)==='object') {
                $count = $res->num_rows;
                if ($outputtype=='aff') {
                    trainLogHtml("--- dump rows : start ---");
                    while($row = $res->fetch_array(MYSQLI_ASSOC)) {                    
                        trainLogHtml( implode(",", $row)); 
                    }
                    trainLogHtml("--- dump rows : end ---");
                } 
                $res->free();
            }
            trainLogHtml( "Count=$count");
            if ($outputtype=='checkispositive') {
                if ($count>0) {
                    trainLogHtml("Check nb row>0 : Ok"); 
                } else {
                    trainLogHtml("Check nb row>0 : Ko"); 
                }
            }
            if ($outputtype=='checkisone') {
                if ($count==1) {
                    trainLogHtml("Check nb row=1 : Ok"); 
                } else {
                    trainLogHtml("Check nb row=1 : Ko"); 
                }
            }
          
        }
        $mysqli->close();
    }

    function exec_cmd($cmd) {
        $db = new SQLite3('./db_train_sqli.sqlite', SQLITE3_OPEN_READONLY);
        if ($db) {
            $query= $cmd;
            trainLogHtml( "SQL Query: [$query]");
            $res = $db->query($query);
            if (! $res) {
                trainLogHtml( "Error: ".$db->lastErrorMsg());
            } else {
                $count=0;
                while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
                    trainLogHtml( implode(",", $row)); 
                    $count++;
                } 
                trainLogHtml( "Count=$count");
            }
        }
    }

    function exec_intinject($CMDINJ_SQL, $CMDINJ_SQL2){
        $db = new SQLite3('./db_train_sqli.sqlite', SQLITE3_OPEN_READONLY);
        if ($db) {
            trainLogHtml( "building SQL Query");
            $query= $CMDINJ_SQL.$CMDINJ_SQL2;
            trainLogHtml( "SQL Query = [$query]");
            $res = $db->query($query);
            if (! $res) {
                trainLogHtml( "Error: ".$db->lastErrorMsg());
            } else {
                $count=0;
                $login = ""; 
                while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
                    trainLogHtml(implode(",", $row)); 
                    $count++;
                } 
                trainLogHtml( "Count=$count");
            }
        }
    }
