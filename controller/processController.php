<?php
/** 
*	Process Controller
*	@version 1.0
*	@author Pablo E. Miguel
*
*/
// no direct access
defined('WAPP_EXEC') or die('Restricted access');
 

function  process_controller($get)
{
     
    global $mysqli, $session;
 
   	include_once "modules/process.php";
    $process = new Process($mysqli);
 
    $session['current_var']=$process->pre_operation($get['current_var'],$get['templ_var']);
   

 	 
    return $session;

 }
 ?>