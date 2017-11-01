<?php
/** 
*	Looger Controller
*	@version 1.0
*	@author Pablo E. Miguel
*
*/

/* logger json cmd

    write
    -----
	c = create
	i = insert
	u = update (not available oh PHPFiwa engine)
	d = delete


    read
    ----
*/
// no direct access
defined('WAPP_EXEC') or die('Restricted access');


function logger_controller($session)
{
     
    global $mysqli,$logger_settings;
 
   	include_once "modules/logger.php";
    $logger = new Logger($mysqli,$logger_settings);
 	
     require_once "modules/out/graph/multigraph_model.php";
    $multigraph = new Multigraph($mysqli);

    deb_log("logger - controller init",0);
 	
    

    /* multigraph send templ_var and apikey, we must build logger_log_file_id */
    if((isset($session['templ_var'])) AND ($session['cmd']!="c") AND ($session['cmd']!="i")){
        deb_log("logger - controller. templ_var exist",0);
 
        $logger_log_file_id=$multigraph->getLoggerForMultigraph($session['templ_var'],$session['apikey']);
        deb_log("logger - controller. templ_var exist got:".$logger_log_file_id,0);
 
    } else {
        deb_log("logger - controller. NO templ_var ",0);
 
        $logger_log_file_id=$session['logger_log_file_id'];
 
    }

    deb_log("logger - controller. Command:".$session['cmd'],0);
 
    // options
    $options=array();
    $options['interval']=$session['interval'];

	$session['engine']=Engine::PHPFIWA;

 	// run command
    
    // write 
    if ($session['cmd'] == "c") $result = $logger->create($logger_log_file_id,$session['type'],$session['engine'],$options,$session['log_on_file']);
    if ($session['cmd'] == "i") $result = $logger->insert_data($logger_log_file_id,$session['engine'],time(),$session['time'],$session['current_var'],$session['log_on_file']);
    if ($session['cmd'] == "d") $result = $logger->delete($logger_log_file_id,$session['engine'],$session['log_on_file']);
    if ($session['cmd'] == "l") $result = $logger->mysql_get_user_logger($session['apikey']);
 
//++  NO UPDATE PROCEDURE ++ if ($session['cmd'] == "u") $result = $logger->update_data($logger_log_file_id,time(),$session['time'],get('current_var'));
 
    // read
    if ($session['cmd'] == "value") $result = $logger->get_value($logger_log_file_id);
    if ($session['cmd'] == "timevalue") $result = $logger->get_timevalue_seconds($logger_log_file_id);
    if ($session['cmd'] == "get") $result = $logger->get_field($logger_log_file_id,$session['field']); // '/[^\w\s-]/'
    // get all field on table
    if ($session['cmd'] == "aget") $result = $logger->get($logger_log_file_id);
/*
    if ($session['cmd'] == 'histogram') $result = $logger->histogram_get_power_vs_kwh($logger_log_file_id, $session['start'], $session['end']);
    if ($session['cmd'] == 'kwhatpower') $result = $logger->histogram_get_kwhd_atpower($logger_log_file_id, $session['min'], $session['max']);
    if ($session['cmd'] == 'kwhatpowers') $result = $logger->histogram_get_kwhd_atpowers($logger_log_file_id, $session['points']);
  */  
    if ($session['cmd'] == 'data') $result = $logger->get_data($logger_log_file_id, $session['start'], $session['end'], $session['dp']);
    if ($session['cmd'] == 'average') $result = $logger->get_average($logger_log_file_id, $session['start'], $session['end'], $session['interval']);
    if ($session['cmd'] == 'history') $result = $logger->get_history($logger_log_file_id, $session['start'], $session['end'], $session['interval']);

    // multigraph cmds
   if ($session['cmd'] == 'mg_get') $result = $multigraph->get($session['name_id'],$session['apikey']);
   if ($session['cmd'] == 'mg_name') $result = $multigraph->getname($session['name_id'],$session['apikey']);


    deb_log("logger Controller - end",0);
   
    return $result;
}



 ?>