<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
$indicator_name = 'global_dates';
?>

<div id="<?php echo('div_' . $indicator_name); ?>">
    <h2>
        <span class="sdi-dashboard-header-from"><?php echo JText::_('COM_EASYSDI_DASHBOARD_PERIOD_TITLE_FROM'); ?></span><span class="sdi-dashboard-header-from-date"></span><span class="sdi-dashboard-header-between"></span><span class="sdi-dashboard-header-to"><?php echo JText::_('COM_EASYSDI_DASHBOARD_PERIOD_TITLE_TO'); ?></span><span class="sdi-dashboard-header-to-date"></span>
    </h2>
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
            success: function (json) {
                jQuery("#<?php echo('div_' . $indicator_name); ?> .sdi-dashboard-header-from-date").text(json.datefrom);
                jQuery("#<?php echo('div_' . $indicator_name); ?> .sdi-dashboard-header-to-date").text(json.dateto);
            }
        });
    }
    //add event listener for update
    jQuery(document).on("dashboardFiltersUpdated", update_<?php echo($indicator_name); ?>);
</script>
