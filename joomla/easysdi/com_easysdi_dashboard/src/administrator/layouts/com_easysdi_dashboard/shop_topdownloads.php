<?php
/**
 * @version     4.5.2
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2019. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
$indicator_name = 'shop_topdownloads';
?>

<div id="<?php echo('div_' . $indicator_name); ?>">

    <?php
    $exportLayout = new JLayoutFile('com_easysdi_dashboard.global_export', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_dashboard'));
    echo $exportLayout->render(array('indicator_name' => $indicator_name));
    ?>

    <div class="module-title nav-header">
        <i class="icon-download" style="text-transform: none;"></i> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_TITLE'); ?>
        <span class="title-total"></span>
    </div> 

    <table class="table table-bordered table-striped table-condensed result-success">
        <thead>
            <tr>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div style="display:none;" class="well waiting-for-result">
        <div class="progress progress-striped active">
            <div class="bar" style="width: 100%;"></div>
        </div>
    </div>
    <div style="display:none;" class="well no-result">
        <span class="no-data"><?php echo JText::_('COM_EASYSDI_DASHBOARD_ERROR_NO_DATA'); ?></span>
    </div>
</div>
<script>
    var vOrganism;
    var vTimestart;
    var vTimeEnd;

    //Retourne les top utilisateurs
    function update_<?php echo($indicator_name); ?>(e) {
        vOrganism = e.organism;
        vTimestart = e.timestart;
        vTimeEnd = e.timeend;
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
                limit: 10
            },
            beforeSend: function () {
                toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'waiting-for-result');
            },
            success: function (json) {
                if (json.data.length > 0) {
                    dashboardFillTable('<?php echo($indicator_name); ?>', json);
                    toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'result-success');
                    jQuery('<?php echo('#div_' . $indicator_name . ' .title-total'); ?>').text(' ( <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_TITLE_TOTAL'); ?>' + json.total + ' )');
                } else {
                    toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'no-result');
                    jQuery('<?php echo('#div_' . $indicator_name . ' .title-total'); ?>').empty();
                }
            },
            error: function (error, ajaxOption, throwError) {
                toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'no-result');
                jQuery('<?php echo('#div_' . $indicator_name . ' .title-total'); ?>').empty();
            }
        });
    }
    jQuery(document).on("dashboardFiltersUpdated", update_<?php echo($indicator_name); ?>);

    // Export download details by diffusion_id
    function dashboardTopDownloadsDetails(vfnID) {
		<?php
		$vUrlInstance = JUri::getInstance();
		$vUrlInstance = substr($vUrlInstance, 0, strrpos($vUrlInstance, "/"));
		?>
        window.open("<?php echo($vUrlInstance); ?>/index.php?option=com_easysdi_dashboard&task=getData&indicator=shop_topdownloadsdetails&organism=" + vOrganism + "&diffusion=" + vfnID + "&timestart=" + vTimestart + "&timeend=" + vTimeEnd + "&dataformat=csv&format=raw");
    }

</script>

