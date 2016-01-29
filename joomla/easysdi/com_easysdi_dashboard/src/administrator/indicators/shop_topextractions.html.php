<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_dashboard
 * @copyright	
 * @license		
 * @author		
 */
$indicator_name = 'shop_topextractions';
if ($this->birtenabled):
    ?>
    <a class="btn btn-small lunch-modal pull-right" data-toggle="modal" href="#chooseTimeReporting" data-id="<?php echo $indicator_name ?>">
        <i class="icon-file"></i>
    </a>
    <?php
endif;
?>
<div class="module-title nav-header"><i class="icon-cog" style="text-transform: none;"></i> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_TITLE'); ?></div> 
<div id="<?php echo('div_'.$indicator_name); ?>">
    <table class="table table-bordered table-striped table-condensed result-success">
        <thead>
            <tr>
                <th><?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_COL1'); ?></th>
                <th><?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_COL2'); ?></th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div style="display:none;" class="well waiting-for-result">
        <div class="progress progress-info progress-striped active">
            <div class="bar" style="width: 100%;"></div>
        </div>
    </div>
    <div style="display:none;" class="well no-result">
        <span class="no-data"><?php echo JText::_('COM_EASYSDI_DASHBOARD_ERROR_NO_DATA'); ?></span>
    </div>
</div>

<script>
    //Retourne les top utilisateurs
    function update_<?php echo($indicator_name); ?>(e) {
        jQuery.ajax({
            url: 'index.php',
            dataType: 'json',
            data: {option: "com_easysdi_dashboard",
                task: "getData",
                indicator: "<?php echo($indicator_name); ?>",
                organism: e.organism,
                timestart: e.timestart,
                timeend: e.timeend,
                dataformat: "json",
                format: "raw",
                limit: 5
            },
            beforeSend: function() {
                toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'waiting-for-result');
            },
            success: function(json) {
                if (json.data.length > 0) {
                    //empty table
                    jQuery("#<?php echo('div_' . $indicator_name); ?> .result-success tbody").empty();
                    //fill table
                    jQuery.each(json.data, function(key, value) {
                        jQuery("#<?php echo('div_' . $indicator_name); ?> .result-success tbody:last").append('<tr>');
                        jQuery("#<?php echo('div_' . $indicator_name); ?> .result-success tbody tr:last").append('<td>' + value[0] + '</td>');
                        jQuery("#<?php echo('div_' . $indicator_name); ?> .result-success tbody tr:last").append('<td>' + value[1] + '</td>');
                    });
                    toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'result-success');
                } else {
                    toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'no-result');
                }
            },
            error: function(error, ajaxOption, throwError) {
                toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'no-result');
            }
        });
    }
    //add event listener for update   
    jQuery(document).on("dashboardFiltersUpdated", update_<?php echo($indicator_name); ?>);
</script>
