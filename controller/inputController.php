<?php
/** 
*	Input Controller
*	@version 1.0
*	@author Pablo E. Miguel
*/
// no direct access
defined('WAPP_EXEC') or die('Restricted access');
 

function input_controller($get)
{
     
    global $mysqli, $session;
 
   	include_once "modules/input.php";
    $input = new Input($mysqli);
 
  
    // check if logger_log is setted
     $session=$input->check_logger_log_exist($get);

     if(is_null($session))
    {
    	deb_log("inputController - start create new feed log",0);
 
    	// build and set looger_log id
      $logger_input_var=key($get['current_var']);
      $logger_input_var_value=current($get['current_var']);
   
       
     	$session=$input->get_system($get['apikey'],$get['system_input_id']);
      //+++ 2015 +++
      // the data from rpi is trasmited and identificate by the all systems so device id is discontinued
      //  		$session=$input->get_device($session,$get['device_input_id']);
     	//+++ 2015 +++
     $session=$input->get_logger($session,$logger_input_var,$logger_input_var_value);
       
    }
 // main cmd
    $session['cmd']=$get['cmd'];
    $session['current_var']=current($get['current_var']);

  	deb_log("inputController - exit",0);
   
    return $session;

    /*  
		Session Main var

    	$session->apikey
    			->logger_log_id
    			->logger_file_id
    			->type
    			->engine
    			->min_value
				->max_value
				->interval
				->cmd


	*/
 }


?>