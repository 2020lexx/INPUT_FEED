<?php
/** 
*   index.php - conversion mod_redir froma rpi
*   @version 1.0
*   @author Pablo E. Miguel <pablo.miguel@0xsystems.it
*
*/ 
require "../functions.php";
// close the unauthorized access
//if(is_null(get('apikey'))) { echo "No apikey on _GET so you die here - posting"; die(); }

     

// go to standard process
echo get_http_page("http://".str_replace('posting/index.php','',$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'])."input.php?".$_SERVER['QUERY_STRING']);


die();

?>
  