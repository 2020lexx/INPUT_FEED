<?php
 
/** 
*   dashboardController.php
*   @version 1.0
*   @author Pablo E. Miguel
*
*/ 


// dashboard/new						New dashboard
// dashboard/delete 				POST: id=			Delete dashboard
// dashboard/clone					POST: id=			Clone dashboard
// dashboard/thumb 					List dashboards
// dashboard/list         	List mode
// dashboard/view?id=1			View and run dashboard (id)
// dashboard/edit?id=1			Edit dashboard (id) with the draw editor
// dashboard/ckeditor?id=1	Edit dashboard (id) with the CKEditor
// dashboard/set POST				Set dashboard
// dashboard/setconf POST 	Set dashboard configuration

defined('WAPP_EXEC') or die('Restricted access');

function dashboard_controller()
{
    global $mysqli, $path, $session, $route, $user;
 
    require "modules/out/dashboard/dashboard_model.php";
    $dashboard = new Dashboard($mysqli);

   
    $result = false; $submenu = '';

    //$session['ds_name']="ds2";
   
        if ($session['cmd'] == "ds_view")
        {
           
          deb_log("dashboardController - call view",0);
          
           if (isset($session['ds_name'])) { 
              $dash = $dashboard->get(trim($session['ds_name']));
               }
               
            //   echo $dash['content'];
        if(isset($dash)){
              $result = view("modules/out/dashboard/Views/dashboard_view.php",array('dashboard'=>$dash));
          } else {
            err_log("dashboardController - no dashboard data with this ds_name");

          }
        }

   /*     if ($route->action == "edit" && $session['write'])
        {
            if ($route->subaction) $dash = $dashboard->get_from_alias($session['userid'],$route->subaction,false,false);
            elseif (isset($_GET['id'])) $dash = $dashboard->get($session['userid'],get('id'),false,false);

            $result = view("Modules/dashboard/Views/dashboard_edit_view.php",array('dashboard'=>$dash));
            $result .= view("Modules/dashboard/Views/dashboard_config.php", array('dashboard'=>$dash));

            $menu = $dashboard->build_menu($session['userid'],"edit");
            $submenu = view("Modules/dashboard/Views/dashboard_menu.php", array('id'=>$dash['id'], 'menu'=>$menu, 'type'=>"edit"));
        }
    }

   

    if ($route->format == 'json')
    {
        if ($session['cmd']=='ds_list' ) $result = $dashboard->get_list($session['apikey']);

        if ($session['cmd']=='ds_set' ) $result = $dashboard->set($session['apikey'],get('ds_name'),get('fields'));
        if ($session['cmd']=='ds_setcontent' ) $result = $dashboard->set_content($session['apikey'],post('ds_name'),post('content'),post('height'));
        if ($session['cmd']=='ds_delete' ) $result = $dashboard->delete($session['apikey'],get('ds_name'));

        if ($session['cmd']=='ds_create' ) $result = $dashboard->create($session['apikey'],post('ds_name'));
    //+      if ($route->action=='clone' && $session['write']) $result = $dashboard->dashclone($session['userid'], get('id'));
    }
*/
    // $result = $dashboard->get_main($session['userid'])
 
    return $result;
}