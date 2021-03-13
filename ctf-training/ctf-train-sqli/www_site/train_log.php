<?php

    $train_logs="";

    function trainClearLog(){
        global $train_logs;
        $train_logs = "";
    }

    function trainGetLogs(){
        global $train_logs;
        return $train_logs;

    }

    function trainLog($str){
        global $train_logs;
        $train_logs = $train_logs.$str;
    }

    function trainLogHtml($str){
        global $train_logs;
        $train_logs = $train_logs.$str."<br />";
    }
?>