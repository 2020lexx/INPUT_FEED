<?php

   /*

   @pablo - main functions 

    */


// no direct access
//++defined('EMONCMS_EXEC') or die('Restricted access');

class Main
{

    private $mysqli;
   
    public function __construct($mysqli)
    {
     
        $this->mysqli = $mysqli;
    }


    // get apikey, user_id, input_sys_id, template number (if applicabile) from systems
     public function apikey_session($apikey_in,$input_sys_id)
    {
        $apikey_in = $this->mysqli->real_escape_string($apikey_in);
        $session = array();

        // use input_dev_id if exists
        $sql_add_cmd=($input_dev_id>0)?"AND 'input_sys_id' = '$input_sys_id'":"";
        
        // if I don't have an system id, the record must be unique 
     
        $result = $this->mysqli->query("SELECT 'id,api_wr,user_id,input_sys_id,custom_template' FROM tbl_systems WHERE `api_wr` = '$apikey_in'");
		
        if ($result->num_rows == 1)
	    	{
            // ok - only one record
			$row = $result->fetch_array(); 
		
            }
        elseif ($result->num_rows >= 1)
           {
            // I found more than one record but I have no system id
           $this->error_log("main_functions - systems - There are more than 1 record with these IDs");
            return $session;
            
           }
       else
            {
            $this->error_log("main_functions - api key - No record with these IDs"); 
            return $session;
        }

        // if we use template get it 
        $custom_template=$row['custom_template'];
        if($custom_template!=0)
        {
        $result = $this->mysqli->query("SELECT 'id,api_wr,user_id,input_sys_id' FROM tbl_systems WHERE `main_template` = '$custom_template'");
            if ($result->num_rows >= 1)
            {
            $row = $result->fetch_array(); 
            }
            else
            {
            $this->error_log("main_functions - System - Template don't exists");
            return $session;
            }
        }

        $session = array(
               
	            'system_id' > $row['id'],
                'apikey' =>$apikey_in,
	            'user_id' => $row['user_id'],
                'input_sys_id'=>$row['input_sys_id'],
                );
                
     
         
         return $session;
 
	}
 
    // get device_id and
    public function get_device($session,$system_id,$input_dev_id)
    {
        // use input_dev_id if exists
        $sql_add_cmd=($input_dev_id>0)?"AND 'input_dev_id' = $input_dev_id'":"";

        $result = $this->mysqli->query("SELECT 'id,custom_template' FROM tbl_device WHERE `id_system` = '$system_id' ".$sql_add_cmd);
        if ($result->num_rows == 1)
            {
            $row = $result->fetch_array(); 
           // if we use template get it 
                $custom_template=$row['custom_template'];
                if($custom_template!=0)
                {
                $result = $this->mysqli->query("SELECT 'id' FROM tbl_device WHERE `main_template` = '$custom_template'");
                    if ($result->num_rows >= 1)
                    {
                    $row = $result->fetch_array(); 
                    }
                    else
                    {
                    $this->error_log("main_functions - Device - Template don't exists");
                    return $session;
                    }
                }
            $session['device_id']=$row['id'];
            }

    return $session;
    }

    // get feed_vars
    public function get_feed($session,$device_id,$input_var_id)
    {
    // use input_var_id if exists
        $sql_add_cmd=($input_var_id>0)?"AND 'input_var_id' = $input_var_id'":"";

        $result = $this->mysqli->query("SELECT 'id,engine,min_var,max_var,type,interval,custom_template' FROM tbl_feed WHERE `id_device` = '$device_id' ".$sql_add_cmd);
        if ($result->num_rows == 1)
            {
            $row = $result->fetch_array(); 
           // if we use template get it 
                $custom_template=$row['custom_template'];
                if($custom_template!=0)
                {
                $result = $this->mysqli->query("SELECT 'id,engine,min_var,max_var,type,interval,custom_template' FROM tbl_feed WHERE `main_template` = '$custom_template'");
                    if ($result->num_rows >= 1)
                    {
                    $row = $result->fetch_array(); 
                    }
                    else
                    {
                    $this->error_log("main_functions - Feed - Template don't exists");
                    return $session;
                    }
                }
            $session['main_feed_id']=$session['apikey'].$session['system_id'].$session['device_id'].$row['id'];
            $session['feed_id']=$row['id'];
            $session['engine']=$row['engine'];
            $session['type']=$row['type'];
            $session['max_value']=$row['max_value'];
            $session['min_value']=$row['min_value'];
            $session['interval']=$row['interval'];
            }

    return $session;
    }


    
    // error log
    public function error_log($message)
    {
        echo "message:".$message." - ".time();
        die();
    }


}?>