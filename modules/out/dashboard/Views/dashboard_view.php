<?php

/*
All Emoncms code is released under the GNU Affero General Public License.
See COPYRIGHT.txt and LICENSE.txt.

---------------------------------------------------------------------
Emoncms - open source energy visualisation
Part of the OpenEnergyMonitor project:
http://openenergymonitor.org
*/

    global $session, $viewQueryPage,$httpRootDir,$RootDir,$graphQueryPage; ?>
 
    <link href="<?php echo $httpRootDir; ?>modules/out/dashboard/Views/js/widget.css" rel="stylesheet">

    <script type="text/javascript" src="<?php echo $httpRootDir; ?>Lib/flot/jquery.flot.min.js"></script>
    <script type="text/javascript" src="<?php echo $httpRootDir; ?>modules/out/dashboard/Views/js/widgetlist.js"></script>
    <script type="text/javascript" src="<?php echo $httpRootDir; ?>modules/out/dashboard/Views/js/render.js"></script>

    <script type="text/javascript" src="<?php echo $httpRootDir; ?>modules/out/dashboard/logger.js"></script>
 
    <?php  require_once $RootDir."/modules/out/dashboard/Views/loadwidgets.php"; ?>
 
    <div id="page-container" style="height:<?php echo $dashboard['height']; ?>px; position:relative;">
        <div id="page"><?php echo $dashboard['content']; ?></div>
    </div>

<script type="application/javascript">
    var dashid = <?php echo $dashboard['id']; ?>;
    var path = "<?php echo $graphQueryPage; ?>";
    var path_embed = "<?php echo $viewQueryPage; ?>";
    var widget = <?php echo json_encode($widgets); ?>;
    var apikey = "<?php echo $session['apikey']; ?>";
  
    for (z in widget)
    {
        var fname = widget[z]+"_widgetlist";
       
 

        var fn = window[fname];
        $.extend(widgets,fn());
    }

    var redraw = 1;
    var reloadiframe = 0;

    show_dashboard();
    setInterval(function() { update(); }, 10000);
    setInterval(function() { fast_update(); }, 30);

</script>
