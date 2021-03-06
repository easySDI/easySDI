<?php
/**
 * @version     4.5.2
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2019. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
$indicator_name = 'shop_topusers';
?>

<div id="<?php echo('div_' . $indicator_name); ?>">

    <?php
    $exportLayout = new JLayoutFile('com_easysdi_dashboard.global_export', null, array('debug' => false, 'client' => 1, 'component' => 'com_easysdi_dashboard'));
    echo $exportLayout->render(array('indicator_name' => $indicator_name));
    ?>

    <div class="module-title nav-header">
        <i class="icon-user" style="text-transform: none;"></i> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPUSERS_TITLE'); ?>
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
            beforeSend: function () {
                toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'waiting-for-result');
            },
            success: function (json) {
                if (json.data.length > 0) {
                    dashboardFillTable('<?php echo($indicator_name); ?>', json);
                    toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'result-success');
                } else {
                    toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'no-result');
                }
            },
            error: function (error, ajaxOption, throwError) {
                toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'no-result');
            }
        });
    }
    //add event listener for update
    jQuery(document).on("dashboardFiltersUpdated", update_<?php echo($indicator_name); ?>);
</script>
