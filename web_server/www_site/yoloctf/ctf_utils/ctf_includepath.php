<?php
    /* Site path */
    define('__SITEROOT__', dirname(__FILE__,2));
    $wwwdir = dirname(__FILE__,2) ;

    /* PHP include path */
    $path = 
        "$wwwdir/api:".
        "$wwwdir/conf:".
        "$wwwdir/ctf_lang:".
        "$wwwdir/ctf_utils:".
        "$wwwdir/templates:".
        "$wwwdir/vendor/csrfguard:".
        "$wwwdir/vendor/markdown:".
        "$wwwdir/vendor/lib_mail:";
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    //echo get_include_path();
?>
