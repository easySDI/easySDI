<?php
/**
 * @version     4.3.2
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
$indicator_name = 'shop_responsetimeproduct';
?>
<span class="nav-header"><i class="icon-clock" style="text-transform: none;"></i> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_RESPONSETIMEPRODUCTS_TITLE'); ?></span> 
<div id="<?php echo('div_' . $indicator_name); ?>">
    <div style="display:none;" class="well waiting-for-result">
        <div class="progress progress-info progress-striped active">
            <div class="bar" style="width: 100%;"></div>
        </div>
    </div>
    <div style="display:none;" class="well no-result">
        <span class="no-data"><?php echo JText::_('COM_EASYSDI_DASHBOARD_ERROR_NO_DATA'); ?></span>
    </div>
    <div class="indicator-graph result-success" id="<?php echo 'div_' . $indicator_name . '_graph'; ?>"></div>
</div>

<script>
    //Retourne les top utilisateurs
    function update_<?php echo($indicator_name); ?>(e) {
        jQuery.ajax({
            url: 'index.php',
            dataType: 'json',
            data: {option: "com_easysdi_dashboard",
                task: "getData",
                indicator: "<?php echo $indicator_name; ?> ",
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

                var data = [];
                var total = 0;
                jQuery.each(json.data, function(key, value) {
                    total += value[1];
                    data.push({label: value[0], data: value[1]});
                });

                if (total == 0) {
                    toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'no-result');
                }
                else {
                    toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'result-success');

                    var plotObj = jQuery.plot(jQuery("#<?php echo 'div_' . $indicator_name . '_graph'; ?>"), data, {
                        series: {
                            pie: {
                                innerRadius: 0.5,
                                show: true
                            }
                        },
                        grid: {
                            hoverable: true
                        },
                        tooltip: true,
                        tooltipOpts: {
                            content: "%s \: %p.0%", // show percentages, rounding to 2 decimal places
                            shifts: {
                                x: 20,
                                y: 0
                            },
                            defaultTheme: true
                        },
                        colors: com_easysdi_dahboard_graphcolours
                    });
                }
            },
            error: function(error, ajaxOption, throwError) {
                toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'no-result');
            }
        });

    }
    jQuery(document).on("dashboardFiltersUpdated", update_<?php echo($indicator_name); ?>);

</script>
