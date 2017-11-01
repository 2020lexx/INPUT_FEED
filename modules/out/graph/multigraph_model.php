<?php
/*
 All Emoncms code is released under the GNU Affero General Public License.
 See COPYRIGHT.txt and LICENSE.txt.

 ---------------------------------------------------------------------
 Emoncms - open source energy visualisation
 Part of the OpenEnergyMonitor project:
 http://openenergymonitor.org
 */

// no direct access
defined('WAPP_EXEC') or die('Restricted access');

class Multigraph
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function create($apikey)
    { 
        $this->mysqli->query("INSERT INTO tbl_logger_multigr (`apikey`,`loggerlist`) VALUES ('$apikey','')");
        return $this->mysqli->insert_id;  
    }

    public function delete($id,$apikey)
    { 
        $this->mysqli->query("DELETE FROM tbl_logger_multigr WHERE `id` = '$id' AND `apikey` = '$apikey'");
    }

    public function set($id, $apikey, $loggerlist, $name)
    { 
        $loggerlist = preg_replace('/[^\w\s-.",:{}\[\]]/','',$loggerlist);
        $name = preg_replace('/[^\w\s-.]/','',$name);
        $this->mysqli->query("UPDATE tbl_logger_multigr SET `name` = '$name', `loggerlist` = '$loggerlist' WHERE `id`='$id' AND `apikey`='$apikey'");
    }

    /*

    multigraph model get.. apikey is not used cos' this must be a template, we use only mg name_id

    */
    public function get($name_id, $apikey)
    { 
     
        //$result = $this->mysqli->query("SELECT loggerlist FROM tbl_logger_multigr WHERE `name_id`='$name_id' AND `apikey`='$apikey'");
        $result = $this->mysqli->query("SELECT loggerlist FROM tbl_logger_multigr WHERE `name_id`='$name_id' ");
          if ($result->num_rows != 1)  { err_sql($this->mysqli->error,$_SERVER['PHP_SELF'],'No multigraph with this name_id:'.$name_id); }
     
        $result = $result->fetch_array();
        
        $loggerlist = json_decode($result['loggerlist']);
        return $loggerlist;
    }
    /*

    multigraph model get name.. apikey is not used cos' this must be a template, we use only mg name_id

    */
     public function getname($name_id, $apikey)
    { 
         
     //   $result = $this->mysqli->query("SELECT name FROM tbl_logger_multigr WHERE `name_id`='$name_id' AND `apikey`='$apikey'");
        $result = $this->mysqli->query("SELECT name FROM tbl_logger_multigr WHERE `name_id`='$name_id' ");
        if ($result->num_rows != 1)  { err_sql($_SERVER['PHP_SELF'],$this->mysqli->error,'No multigraph name with this name_id:'.$name_id); }
     
       $result = $result->fetch_array();
        return $result['name'];
    }
    /*

    multigraph logger_log_file_id converter -  multigraph send templ_var and apikey, we must build logger_log_file_id

     */
    public function getLoggerForMultigraph($templ_var,$apikey)
    {

        $result = $this->mysqli->query("SELECT logger_log_file_id FROM tbl_logger_log WHERE `templ_var`='$templ_var' AND `api_wr`='$apikey' ");
        if ($result->num_rows != 1)  { err_sql($_SERVER['PHP_SELF'],$this->mysqli->error,'No logger_log_file_id with this apikey:'.$apikey.' and templ_var:'.$templ_var); }
          
        $result = $result->fetch_array();
        return $result['logger_log_file_id'];
    
    }
    public function getlist($apikey)
    { 
        $result = $this->mysqli->query("SELECT id,name,loggerlist FROM tbl_logger_multigr WHERE `apikey`='$apikey'");

        $multigraphs = array();
        while ($row = $result->fetch_object())
        {
            $multigraphs[] = array('id'=>$row->id,'name'=>$row->name,'loggerlist'=>$row->loggerlist);
        }
        return $multigraphs;
    }
    
    
    
}
