<?php


// Clean GET or POST input
function clean_string($str){
    // Only char allowed:
    // a-z
    // A-Z
    // 0-9
    // - _
    $clean_str = preg_replace('/[^a-zA-Z0-9]_-\./', '', $str);
    return $clean_str;
}

?>