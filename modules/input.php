<?php
/** 
*	Input Model
*	@version 1.0
*	@author Pablo E. Miguel
*
*/

// no direct access
defined('WAPP_EXEC') or die('Restricted access');

class Input
{

    private $mysqli;
   
    public function __construct($mysqli)
    {
     
        $this->mysqli = $mysqli;
    }


    // get apikey, user_id, input_sys_id, template number (if applicabile) from systems
     public function get_system($apikey_in,$input_sys_id)
    {
        $apikey_in = $this->mysqli->real_escape_string($apikey_in);
        $session = array();

 
        // use input_dev_id if exists
        $sql_add_cmd=($input_dev_id>0)?"AND 'input_sys_id' = '$input_sys_id'":"";
        
        // if I don't have an system id, the record must be unique 'id,api_wr,user_id,input_sys_id,custom_template'
     
        $result = $this->mysqli->query("SELECT id,api_wr,user_id,input_sys_id   FROM tbl_systems WHERE `api_wr` = '$apikey_in'");
		if ($result->num_rows != 1)  { err_sql($this->mysqli->error,$_SERVER['PHP_SELF'],'No system with this apikey:'.$apikey_in); }
        // ok - only one record
		$row = $result->fetch_array(); 
		
        //+++ 2015 +++
        // no custom_template have an apikey so allways is custom_template=0 
        /***************************************
        // if we use template get it 
        $custom_template=$row['custom_template'];
        if($custom_template!=0)
        {
        $result = $this->mysqli->query("SELECT id,api_wr,user_id,input_sys_id  FROM tbl_systems WHERE `main_template` = '$custom_template'");
            if ($result->num_rows >= 1)
            {
            $row = $result->fetch_array(); 
            }
            else
            {
            err_log("main_functions - System - Template don't exists");
            return $session;
            }
        }
        ****************************************/
        //+++ 2015 +++
        $session = array(
               
	            'system_id' => $row['id'],
                'apikey' =>$apikey_in,
	            'user_id' => $row['user_id'],
                'input_sys_id'=>$row['input_sys_id'],
                
                );
                
     
         
         return $session;
 
	}
    //+++ 2015 +++
    // the data from rpi is trasmited and identificate by the all systems so device id is discontinued
    /*********************************************************
    // get device_id and
    public function get_device($session,$input_dev_id)
    {
       
        // check if we use a template
        if($session['main_template']>0){
            $result = $this->mysqli->query("SELECT id FROM tbl_devices WHERE `main_template` = '".$session['main_template']."'");
            if ($result->num_rows != 1)  { err_sql($this->mysqli->error,$_SERVER['PHP_SELF'],'template device no exists:'.$session['main_template']); }
           $row = $result->fetch_array(); 
          
        }
        else
        {
            // use input_dev_id if exists
            $system_id=$session['system_id'];
            $sql_add_cmd=($input_dev_id>0)?"AND 'input_dev_id' = $input_dev_id'":"";
            
            $result = $this->mysqli->query("SELECT id FROM tbl_devices WHERE `system_id` = '$system_id' ".$sql_add_cmd);
            if ($result->num_rows != 1)  { err_sql($this->mysqli->error,$_SERVER['PHP_SELF'],'no device with these ID:'.$system_id.$sql_add_cmd); }
            $row = $result->fetch_array(); 
 
        }

        $session['device_id']=$row['id'];
        $session['input_dev_id']=$input_dev_id;
        return $session;
    }
    *********************************************************/
    //+++ 2015 +++

    // get logger_vars
    public function get_logger($session_c,$input_var_id,$input_var_value)
    {
            //+++ 2015 +++
            // no template on these functions or device_id
            //
            // check if we use a template
            //++ if($session['main_template']>0){
            //++     $result = $this->mysqli->query("SELECT id,engine,min_var,max_var,type,interval,custom_template,templ_var FROM tbl_logger WHERE `main_template` = '".$session['main_template']."'");
            //++     if ($result->num_rows != 1)  { err_sql($this->mysqli->error,$_SERVER['PHP_SELF'],'no template logger_log with these ID:'.$session['main_template']); }
             
            //++ }
            //++ else
            //++ {

             //++   // use input_var_id if exists
            //++    $device_id=$session_c['device_id'];
           
              $result = $this->mysqli->query("SELECT * FROM tbl_logger WHERE  `input_var_id` = '$input_var_id'");
              if ($result->num_rows != 1)  { err_sql($this->mysqli->error,$_SERVER['PHP_SELF'],'MAIN CONFIG ERROR: No record on tbl_logger with input_var_id:'.$input_var_id); }
               
              $row = $result->fetch_array(); 
                
            //++ }
             //+++ 2015 +++

                deb_log("input - create new logger_log record",0);
                // create tbl_logger_log record
                $session=array();    
                $session['logger_log_file_id']=$session_c['apikey'].$session_c['system_id'].$row['id'];
                $session['main_logger_input_id']=$session_c['apikey'].$session_c['input_sys_id'].$input_var_id;
               //++  $session['logger_log_file_id']=$session_c['apikey'].$session_c['system_id'].$session_c['device_id'].$row['id'];
               //++ $session['main_logger_input_id']=$session_c['apikey'].$session_c['input_sys_id'].$session_c['input_dev_id'].$input_var_id;
           
                $session['engine']=$row['engine'];
                $session['type']=$row['type'];
                $session['max_value']=$row['max_var'];
                $session['min_value']=$row['min_var'];
                $session['interval']=$row['interval'];
                $session['templ_var']=$row['templ_var'];
                $session['pre_operation']=$row['pre_operation'];
                $result = $this->mysqli->query("INSERT INTO tbl_logger_log (value,api_wr,systems_id,logger_id,templ_var,main_logger_input_id,logger_log_file_id) 
    	        								                  VALUES ('".$input_var_value."',
                                                                            '".$session_c['apikey']."',
                                                                           '".$session_c['system_id']."', 
                                                                           '".$row['id']."',
                                                                           '".$row['templ_var']."',
                                                                           '".$session['main_logger_input_id']."',
                                                                           '".$session['logger_log_file_id']."')");
    	        
                $logger_log_id = $this->mysqli->insert_id;
    	        $session['logger_log_id']=$logger_log_id;
    	        deb_log("input - created new logger_log record:".$logger_log_id,0);
              
                // set to create logger_log  if we need
                $session['create_logger_file']=true;
                // set to create file or only record on table
                $session['log_on_file']=$row['log_on_file'];

    	        return $session;
             
               
                
 
    }


    // check if logger is setted
    public function check_logger_log_exist($get)
    { 
    	// build query
        $logger_input_id=key($get['current_var']);
    	$main_logger_input_id=$get['apikey'].$get['system_input_id'].$logger_input_id;
 
        $result = $this->mysqli->query("SELECT id,api_wr,systems_id,logger_id FROM tbl_logger_log WHERE `main_logger_input_id` = '$main_logger_input_id'");
     
        if ($result->num_rows == 1)
        {
        	$row = $result->fetch_array(); 
            deb_log("input - logger_log exists",0);
            // get_logger_log and log records
            $session['main_logger_input_id']=$main_logger_input_id;
            $session['logger_log_file_id']=$row['api_wr'].$row['systems_id'].$row['logger_id'];
            $session['logger_log_id']=$row['id'];

            $result = $this->mysqli->query("SELECT *  FROM tbl_logger WHERE `id` = '".$row['logger_id']."'");
        
            if ($result->num_rows == 1)
            {
                $row = $result->fetch_array(); 
                $session['engine']=$row['engine'];
                $session['type']=$row['type'];
                $session['max_value']=$row['max_var'];
                $session['min_value']=$row['min_var'];
                $session['interval']=$row['interval'];
                $session['templ_var']=$row['templ_var'];
                $session['pre_operation']=$row['pre_operation'];
            	$session['create_logger_file']=false;
                $session['log_on_file']=$row['log_on_file'];
                return $session;
            }
            else
            {
               error_log("input - no logger with the ID from logger_input_var");
            }
        }
        elseif ($result->num_rows == 0)
        {
             return null;
        } 
        else
        {
        	error_log("functions - more than one Logger_log with same ID ?");
       		return;
        }

    }



     


}?>