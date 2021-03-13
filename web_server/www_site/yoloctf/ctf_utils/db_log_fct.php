
<?php

//CREATE TABLE logs (id INT NOT NULL AUTO_INCREMENT, fdate datetime, UID VARCHAR(45) NULL, type INT NULL, txt VARCHAR(2000) NULL, PRIMARY KEY (id));


function insert_log_entry($uid, $type, $txt) {

    require "ctf_sql_pdo.php";
    $query = "INSERT into logs (fdate, UID, type, txt) VALUES (NOW(), :UID, :type, :txt)";
    $stmt = $mysqli_pdo->prepare($query);
    if ($stmt->execute([
            'UID' => $uid, 
            'type' => $type,
            'txt' => $txt,
        ])) {
    } else {
        echo $request;
        printf("Log failes\n");
        exit();
    }
}

function test_log_entry() {
    echo "Add test log entry";
    insert_log_entry("UUIIDD", "Login", "User XXX login");
    insert_log_entry("UUIIDD", "Config", "User XXX config");
    insert_log_entry("UUIIDD", "Change", "User XXX change password");
    insert_log_entry("UUIIDD", "Flag ko", "User XXX flag ko: FFFFFFFF");
    insert_log_entry("UUIIDD", "Flag ok", "User XXX flag ok: KKKKKKFFFF");
    insert_log_entry("UUIIDD", "Chall start", "User XXX chall start 22");
    insert_log_entry("UUIIDD", "Keep alive", "User XXX keep alive");
    insert_log_entry("UUIIDD", "Keep alive timeout", "User XXX keep alive timeout");
    insert_log_entry("UUIIDD", "Logout", "User XXX logout");
}

function get_log_table_count()
{
    include "ctf_sql_pdo.php";
    $query = "SELECT count(*) as count FROM logs";
    $stmt = $mysqli_pdo->prepare($query);
    if ($stmt->execute()) {
        if ($frow = $stmt->fetch()) {
            return $frow['count'];
        }
    }
    $mysqli_pdo=null;
}


function get_log_table($offset_value=0, $number_rows=20)
{
    include "ctf_sql.php";
    $ret= [];
    $user_query = "SELECT * FROM logs l
            LEFT JOIN users u
            on l.uid = u.uid
            ORDER BY l.date;";
    $user_query = "SELECT * FROM logs LIMIT $number_rows OFFSET $offset_value;";
    if ($result = $mysqli->query($user_query)) {
        if ($array = $result->fetch_all(MYSQLI_ASSOC)) {
            $ret = $array;
        }
        $result->close();
    } else {
        echo "pb";
    }
    $mysqli->close();
    return $ret;
}


?>