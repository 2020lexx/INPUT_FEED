<?php
/** 
*	Process Model
*	@version 1.0
*	@author Pablo E. Miguel
*
*/
// no direct access
defined('WAPP_EXEC') or die('Restricted access');

class Process
{

    private $mysqli;
   
    public function __construct($mysqli)
    {
     
        $this->mysqli = $mysqli;
    }

// pre operations 
 public function pre_operation($current_var_value,$current_var)
     {

    $result =  $this->mysqli->query("SELECT pre_operation  FROM tbl_logger WHERE `templ_var` = '".$current_var."'");
    if ($result->num_rows != 1)  { err_sql( $this->mysqli->error,$_SERVER['PHP_SELF'],'No templ_var on table:'.$current_var); }
    
    $row = $result->fetch_array(); 
     if(!is_null($row['pre_operation'])){ 
     
         $current_var_value = $current_var_value / $row['pre_operation'];}
         deb_log("process.php - pre operation: /".$row['pre_operation']."->".$current_var."=".$current_var_value,0);
      return $current_var_value;
     }
   

   

 }
 ?>