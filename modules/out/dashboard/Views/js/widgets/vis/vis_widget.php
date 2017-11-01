<?php
    /*

    As well as loading the default visualisations
    we load here the custom multigraph visualisations.
    the object multigraphs is recognised in designer.js
    and used to create a drop down menu of available
    user multigraphs

    */

    global $mysqli, $session,$httpRootDir,$RootDir;

    require $RootDir."/modules/out/graph/multigraph_model.php";
    $multigraph = new Multigraph($mysqli);
    $multigraphs = $multigraph->getlist($session['apikey']);
?>

<script>
    var multigraphs = <?php echo json_encode($multigraphs); ?>;
</script>
<script type="text/javascript" src="<?php echo $httpRootDir; ?>modules/out/dashboard/Views/js/widgets/vis/vis_render.js"></script>
