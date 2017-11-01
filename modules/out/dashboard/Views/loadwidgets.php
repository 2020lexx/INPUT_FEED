<?php
    /*

    All Emoncms code is released under the GNU Affero General Public License.
    See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

    */
  global $RootDir,$httpRootDir;
    define("MODULE_PATH", $RootDir."/modules/out");
    define("MODULE_PATH_EXT",MODULE_PATH."/");
    
    define("WIDGETS_PATH",MODULE_PATH_EXT."dashboard/Views/js/widgets");
     define("WIDGETS_PATH_EXT",WIDGETS_PATH."/");

    define("WIDGETS_PATH_WEB",$httpRootDir."modules/out/dashboard/Views/js/widgets/");
   define("MODULES_PATH_WEB",$httpRootDir."modules/out/dashboard/Views/js/widgets/");
  


    $widgets = array();
    $dir = scandir(WIDGETS_PATH);
    for ($i=2; $i<count($dir); $i++)
    {
        if (filetype(WIDGETS_PATH_EXT.$dir[$i])=='dir')
        {
            if (is_file(WIDGETS_PATH_EXT.$dir[$i]."/".$dir[$i]."_widget.php"))
            {
                require_once WIDGETS_PATH_EXT.$dir[$i]."/".$dir[$i]."_widget.php";
                $widgets[] = $dir[$i];
            }
            else if (is_file(WIDGETS_PATH_EXT.$dir[$i]."/".$dir[$i]."_render.js"))
            {
                echo "<script type='text/javascript' src='".WIDGETS_PATH_WEB.$dir[$i]."/".$dir[$i]."_render.js'></script>";
                $widgets[] = $dir[$i];
            }
        }
    }

    // Load module specific widgets

    $dir = scandir(MODULE_PATH);
    for ($i=2; $i<count($dir); $i++)
    {
        if (filetype(MODULE_PATH_EXT.$dir[$i])=='dir')
        {
            if (is_file(MODULE_PATH_EXT.$dir[$i]."/widget/".$dir[$i]."_widget.php"))
            {
                require_once MODULE_PATH_EXT.$dir[$i]."/widget/".$dir[$i]."_widget.php";
                $widgets[] = $dir[$i];
            }
            else if (is_file(MODULE_PATH_EXT.$dir[$i]."/widget/".$dir[$i]."_render.js"))
            {
                echo "<script type='text/javascript' src='".MODULES_PATH_WEB.$dir[$i]."/widget/".$dir[$i]."_render.js'></script>";
                $widgets[] = $dir[$i];
            }
        }
    }
