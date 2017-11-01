<?php
/** 
*	Graph Controller
*	@version 1.0
*	@author Pablo E. Miguel
*
*/
/*
	session ()
		graph_type:
			auto
			rawdata
			bargraph
			histgraph
*/
 
// no direct access
defined('WAPP_EXEC') or die('Restricted access');

function graph_controller()
  {
    global $mysqli,$session,$logger_settings,$graphQueryPage;

    $result = false;

    require_once  "modules/logger.php";  
   	$logger = new Logger($mysqli,$logger_settings);

   require_once "modules/out/graph/multigraph_model.php";
    $multigraph = new Multigraph($mysqli);

    $visdir = "modules/out/graph/visualisations/";

    require_once "modules/out/graph/graph_object.php";
    
    $apikey=$session['apikey'];
    $logger_log_file_id=(isset($session['logger_log_file_id']))?$session['logger_log_file_id']:0;

     /*   if ($route->action == 'list' && $session['write'])
        {
            $multigraphs = $multigraph->getlist($session['userid']);
            $feedlist = $feed->get_user_feeds($session['userid']);
            $result = view("Modules/vis/vis_main_view.php", array('user' => $user->get($session['userid']), 'feedlist'=>$feedlist, 'apikey'=>$read_apikey, 'visualisations'=>$visualisations, 'multigraphs'=>$multigraphs));
        }
		*/
      
        // @pablo - here use data type from feed table. 

        // Auto - automatically selects visualisation based on datatype 
        if ($session['graph']== "auto")
        {
            
            $datatype = $logger->get_field($session['logger_log_file_id'],'type');
            if ($datatype == 0) $result = "Feed type or authentication not valid";
            if ($datatype == 1) $session['graph'] = 'rawdata';
            if ($datatype == 2) $session['graph'] = 'bargraph';
            if ($datatype == 3) $session['graph'] = 'histgraph';
        }

         
        deb_log("graphController - start visualisation ",0);
        // @pablo - get 
        while ($vis = current($visualisations))
        {
            $viskey = key($visualisations);
 
             // If the visualisation has a set property called action
            // then override the visualisation key and use the set action instead
            if (isset($vis['graph'])) $viskey = $vis['graph'];

            deb_log("graphController - viskey:".$viskey,0);

            // @pablo - print graph (passed in $route->action)
            if ($session['graph'] == $viskey)
            {
                
   				deb_log("graphController - start graph - viskey:".$viskey,0);
                $array = array();
                $array['valid'] = true;
                 if (isset($vis['options']))
                {
                    deb_log("graphController - process options",0);

                    foreach ($vis['options'] as $option)
                    {
                        $key = $option[0]; $type = $option[2];
                        if (isset($option[3])) $default = $option[3]; else $default = "";

                        if ($type==0 || $type==1 || $type==2 || $type==3)
                        {
                           
                            if ($logger_log_file_id) {
                             // $f = $feed->get($feedid);
                              $array[$key] = $logger_log_file_id;
                              $array[$key.'name'] = "graphController: name logger is not used";

                          }
                   
                        }

                        // Boolean not used at the moment
                            if ($type==4)
                                if (get($key)==true || get($key)==false)
                                    $array[$key] = get($key); else $array[$key] = $default;
                            if ($type==5)
                                $array[$key] = preg_replace('/[^\w\s£$€¥]/','',get($key))?get($key):$default;
                            if ($type==6)
                                $array[$key] = str_replace(',', '.', floatval((get($key)?get($key):$default)));
                            if ($type==7)
                                $array[$key] = intval((get($key)?get($key):$default));

                            # we need to either urlescape the colour, or just scrub out invalid chars. I'm doing the second, since
                            # we can be fairly confident that colours are eiter a hex or a simple word (e.g. "blue" or such)
                            if ($key == "colour")
                                $array[$key] = preg_replace('/[^\dA-Za-z]/','',$array[$key]);
                    }
                }

                $array['apikey'] = $apikey;
               // $array['write_apikey'] = $write_apikey;
 
  				      deb_log("graphController - call view:".$visdir.$viskey.".php",0);
  			
                $result = view($visdir.$viskey.".php", $array);
 
                echo $result;
 
             //++ no auth check   if ($array['valid'] == false) $result .= "<div style='position:absolute; top:0px; left:0px; background-color:rgba(240,240,240,0.5); width:100%; height:100%; text-align:center; padding-top:100px;'><h3>"._('Authentication not valid')."</h3></div>";

            }
            next($visualisations);
        }
   

    /*

    MULTIGRAPH ACTIONS

    */
        
        
  /*   if ( $session['graph'] == 'multigraph')
    {
        deb_log("multi graphController - call view",0);


      //  if ($route->subaction == 'new' && $session['write']) $result = $multigraph->create($session['userid']);
      //  if ($route->subaction == 'delete' && $session['write']) $result = $multigraph->delete(get('id'),$session['userid']);
      //  if ($route->subaction == 'set' && $session['write']) $result = $multigraph->set(get('id'),$session['userid'],get('feedlist'),get('name'));
      //  if ($route->subaction == 'get') $result = $multigraph->get(get('id'),$session['userid']);
       // if ($route->subaction == 'getlist') $result = $multigraph->getlist($session['userid']);
      //  if ($route->subaction == 'getname') $result = $multigraph->getname(get('id'),$session['userid']);
    } */
  
    return $result;
  }
