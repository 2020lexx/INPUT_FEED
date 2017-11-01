<?php
/** 
*	Logger Model
*	@version 1.0
*	@author Pablo E. Miguel
*
*/
// no direct access
defined('WAPP_EXEC') or die('Restricted access');

class Logger
{

    private $mysqli;
   
    public function __construct($mysqli,$logger_settings)
    {
         $this->mysqli = $mysqli;
     
        // init engine
        require_once "modules/engine/PHPFiwa.php"; 
        $this->engine = array();
    //++    $this->engine[Engine::PHPFIWA] = new PHPFiwa($logger_settings['phpfiwa']); 
       $this->engine['6'] = new PHPFiwa($logger_settings['phpfiwa']); 

    }

  
     // create logger_log
    public function create($logger_log_file_id,$datatype,$engine,$options_in,$log_on_file)
    {
        
        // if not log file requiered return 
        if(!$log_on_file) return;
        
        // Histogram engine requires MYSQL
       //++ if ($datatype==DataType::HISTOGRAM && $engine!=Engine::MYSQL) $engine = Engine::MYSQL;
        
        // If feed  already exists
      //++  $logger_log_file_id = $this->get_id($logger_log_file_id);
       $options = array();
        //++   if ($engine==Engine::TIMESTORE) $options['interval'] = (int) $options_in->interval;
        //++   if ($engine==Engine::PHPTIMESTORE) $options['interval'] = (int) $options_in->interval;
        //++   if ($engine==Engine::PHPFINA) $options['interval'] = (int) $options_in->interval;
        if ($engine==Engine::PHPFIWA) $options['interval'] = (int) $options_in->interval;
            
        $engineresult = false;
        //++    if ($datatype==DataType::HISTOGRAM) {
        //++        $engineresult = $this->histogram->create($logger_log_file_id,$options);
        //++    } else {
        
        // create logger
        $engineresult = $this->engine[$engine]->create($logger_log_file_id,$options);
        //++    }
      
        if ($engineresult == false)
        {
            // Feed engine creation failed 
            err_log("Feed model: failed to create feed model logger_log_file_id=$logger_log_file_id");
        }

        
        return  $logger_log_file_id;
        
    }
/*
    Feed data public functions - @pablo - we use this -

    insert, update, get and specialist histogram public functions

    */

    public function insert_data($logger_log_file_id,$engine,$updatetime,$logger_log_time,$value,$log_on_file)
    {
        
        // save on feed only if we need it
        if($log_on_file){
            if (!$this->exist($logger_log_file_id)) { err_log("logger - logger does not exist");}

            if ($logger_log_time == null) $logger_log_time = time();
            $updatetime = intval($updatetime);
            $logger_log_time = intval($logger_log_time);
            $value = floatval($value);

            // @pablo - save - Call to engine post method
            $this->engine[$engine]->post($logger_log_file_id,$logger_log_time,$value);
            // @pablo - save - Call to engine post method
           }

        // save on table
        $this->set_timevalue($logger_log_file_id, $value, $updatetime);




        return $value;
    }

 
   
    public function delete($logger_log_file_id,$engine,$log_on_file)
    {
        if (!$this->exist($logger_log_file_id)) { err_log("logger - logger does not exist");}

        if($log_on_file){
             // Call to engine delete method
             $this->engine[$engine]->delete($logger_log_file_id);
        }
        $this->mysqli->query("DELETE FROM tbl_logger_log WHERE `logger_log_file_id` = '$logger_log_file_id'");

         
    }

    public function exist($logger_log_file_id)
    {
        $feedexist = false;
    
         $result = $this->mysqli->query("SELECT id FROM tbl_logger_log WHERE logger_log_file_id = '$logger_log_file_id'");
        if ($result->num_rows>0) $feedexist = true;
    
        return $feedexist;
    }


    // +++ read +++ 

    private function get_engine($logger_log_file_id)
    {
        $result = $this->mysqli->query("SELECT logger_id FROM tbl_logger_log WHERE `logger_log_file_id` = '$logger_log_file_id'");
        $row = $result->fetch_object();
        $id=$row->logger_id;

        $result = $this->mysqli->query("SELECT engine FROM tbl_logger WHERE `id` = '$id'");
        $row = $result->fetch_object();
   

        return $row->engine;
        
    }

    public function get_data($logger_log_file_id,$start,$end,$dp)
    {
         if ($end == 0) $end = time()*1000;
                
      
        $engine = $this->get_engine($logger_log_file_id);
        

          // Call to engine get_data method
        $range = ($end - $start) * 0.001;
        if ($dp>$this->max_npoints_returned) $dp = $this->max_npoints_returned;
        if ($dp<1) $dp = 1;
        $outinterval = round($range / $dp); 
        
        return $this->engine[$engine]->get_data($logger_log_file_id,$start,$end,$outinterval);

    }

    public function get_average($logger_log_file_id,$start,$end,$outinterval)
    {
         if ($end == 0) $end = time()*1000;
        
   
        $engine = $this->get_engine($logger_log_file_id);

        // Call to engine get_average method
        if ($outinterval<1) $outinterval = 1;
        $range = ($end - $start) * 0.001;
        $npoints = ($range / $outinterval);
        if ($npoints>$this->max_npoints_returned) $outinterval = round($range / $this->max_npoints_returned);
        return $this->engine[$engine]->get_data($logger_log_file_id,$start,$end,$outinterval);
    }
    
    public function get_history($logger_log_file_id,$start,$end,$outinterval)
    {
         if ($end == 0) $end = time()*1000;
        
        if (!$this->exist($logger_log_file_id)){ err_log("logger - logger does not exist");}

        $engine = $this->get_engine($logger_log_file_id);
        
        if ($engine==Engine::PHPFINA || $engine==Engine::PHPFIWA) {
            // Call to engine get_average method
            if ($outinterval<1) $outinterval = 1;
            $range = ($end - $start) * 0.001;
            $npoints = ($range / $outinterval);
            if ($npoints>$this->max_npoints_returned) $outinterval = round($range / $this->max_npoints_returned);
            return $this->engine[$engine]->get_data_exact($logger_log_file_id,$start,$end,$outinterval);
        }
        return false;
    }

    public function get($logger_log_file_id)
    {
        $result = $this->mysqli->query("SELECT * FROM tbl_logger_log WHERE `logger_log_file_id` = '$logger_log_file_id'");
        $row = (array) $result->fetch_object();
        $row['time'] = strtotime($row['time']);
      
        return $row;
    }

    public function get_field($id,$field)
    {
        $id = (int) $id;
        if (!$this->exist($id)) { err_log("logger - logger does not exist");}

        if ($field!=NULL) // if the feed exists
        {
            $field = preg_replace('/[^\w\s-]/','',$field);
            
            if ($this->redis) {
                $val = $this->redis->hget("feed:$id",$field);
            } else {
                $result = $this->mysqli->query("SELECT `$field` FROM feeds WHERE `id` = '$id'");
                $row = $result->fetch_array();
                $val = $row[0];
            }
            
            if ($val) return $val; else return 0;
        }
        else { err_log("logger - Missing field parameter");}
    }

    public function get_timevalue($logger_log_file_id)
    {
      
        $result = $this->mysqli->query("SELECT time,value FROM tbl_logger_log WHERE `logger_log_file_id` = '$logger_log_file_id'");
        $row = $result->fetch_array();
        $lastvalue = array('time'=>$row['time'], 'value'=>$row['value']);
        

        return $lastvalue;
    }

    public function get_timevalue_seconds($logger_log_file_id)
    {
        $lastvalue = $this->get_timevalue($logger_log_file_id);
        $lastvalue['time'] = strtotime($lastvalue['time']);
        return $lastvalue;
    }

    public function get_value($logger_log_file_id)
    {
        $lastvalue = $this->get_timevalue($logger_log_file_id);
        return $lastvalue['value'];
    }

    public function get_timevalue_from_data($feedid)
    {
        $feedid = (int) $feedid;
        if (!$this->exist($feedid)) { err_log("logger - logger does not exist");}

        $engine = $this->get_engine($feedid);
        
        // Call to engine lastvalue method
        return $this->engine[$engine]->lastvalue($feedid);
    }


      // check if exist on  - table
    public function get_id($logger_log_file_id)
    {
         $result = $this->mysqli->query("SELECT id FROM tbl_logger_log WHERE `logger_log_file_id` = '$logger_log_file_id'");
        if ($result->num_rows>0) {   return $logger_log_file_id; } else return false;
    }
    

    // ++ set ++
     //   update table feed timestamp
    public function set_timevalue($logger_log_file_id, $value)
    {
     
      $this->mysqli->query("UPDATE tbl_logger_log SET `value` = '$value'  WHERE `logger_log_file_id`= '$logger_log_file_id'");
     
    }
  
    public function mysql_get_user_logger($apikey)
        { 
        $logger = array();
        $result = $this->mysqli->query("SELECT * FROM tbl_logger_log WHERE `api_wr` = '$apikey'");
       
        while ($row = (array)$result->fetch_object())
        {
             $row['time'] = strtotime($row['timestamp']);
            $logger[] = $row;
        }
        
        return $logger;
    }


     //---------------------------------------------------------------------------------------
    // Power to kwh
    //---------------------------------------------------------------------------------------
    public function power_to_kwh($logger_log_file_id, $time_now, $value)
    {
        $new_kwh = 0;

        // Get last value
        $last = $this->get_timevalue($logger_log_file_id);

        $last['time'] = strtotime($last['time']);
        if (!isset($last['value'])) $last['value'] = 0;
        $last_kwh = $last['value']*1;
        $last_time = $last['time']*1;

        // only update if last datapoint was less than 2 hour old
        // this is to reduce the effect of monitor down time on creating
        // often large kwh readings.
        if ($last_time && (time()-$last_time)<7200)
        {
            // kWh calculation
            $time_elapsed = ($time_now - $last_time);
            $kwh_inc = ($time_elapsed * $value) / 3600000.0;
            $new_kwh = $last_kwh + $kwh_inc;
        } else {
            // in the event that redis is flushed the last time will
            // likely be > 7200s ago and so kwh inc is not calculated
            // rather than enter 0 we enter the last value
            $new_kwh = $last_kwh;
        }

        $padding_mode = "join";
      ////++++++++++++  $this->feed->insert_data_padding_mode($feedid, $time_now, $time_now, $new_kwh, $padding_mode);
        
        return $value;
    }

    public function power_to_kwhd($logger_log_file_id, $time_now, $value)
    {
        $new_kwh = 0;

        // Get last value
        $last = $this->get_timevalue($logger_log_file_id);

        $last['time'] = strtotime($last['time']);
        if (!isset($last['value'])) $last['value'] = 0;
        $last_kwh = $last['value']*1;
        $last_time = $last['time']*1;
        
        //$current_slot = floor($time_now / 86400) * 86400;
        //$last_slot = floor($last_time / 86400) * 86400;
        $current_slot = $this->getstartday($time_now);
        $last_slot = $this->getstartday($last_time);    

        if ($last_time && ((time()-$last_time)<7200)) {
            // kWh calculation
            $time_elapsed = ($time_now - $last_time);
            $kwh_inc = ($time_elapsed * $value) / 3600000.0;
        } else {
            // in the event that redis is flushed the last time will
            // likely be > 7200s ago and so kwh inc is not calculated
            // rather than enter 0 we dont increase it
            $kwh_inc = 0;
        }
        
        if($last_slot == $current_slot) {
            $new_kwh = $last_kwh + $kwh_inc;
        } else {
            # We are working in a new slot (new day) so don't increment it with the data from yesterday
            $new_kwh = $kwh_inc;
        }
        
       //+++++++++++ $this->update_data($feedid, $time_now, $current_slot, $new_kwh);

        return $value;
    }
 
  
     // Get the start of the day
    private function getstartday($time_now)
    {
        $now = DateTime::createFromFormat("U", $time_now);
        $now->setTimezone(new DateTimeZone($this->timezone));
        $now->setTime(0,0);    // Today at 00:00
        return $now->format("U");
    }

}
?>