<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
$indicator_name = 'shop_global';
?>
<div id="<?php echo('div_' . $indicator_name); ?>">
    <div class="span12 well result-success well-small">
        <div class="row-fluid">
            <div class="span4">
                <span class="nav-header"><span id="total-diff"></span> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_GLOBAL_TOTALDIF'); ?></span> 
                <div  style="min-height: 100px " class="indicator-graph result-success" id="<?php echo 'div_' . $indicator_name . '_graph'; ?>"></div>
            </div>
            <div class="span8">

                <div class="result-success row-fluid" style="text-align: center">
                    <span class="nav-header">
                        <span id="total-diff-ext"></span>
                        <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_GLOBAL_TOTALEXT'); ?>
                    </span> 
                </div>
                <div class="result-success row-fluid">
                    <div class="span3" style="text-align: right">
                        <span id="diff-manu-value">0</span> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_GLOBAL_DIFF_MANUAL'); ?>
                    </div>
                    <div class="span6">
                        <div class="progress">
                            <div class="bar bar-1" id="diff-manu-bar" style="width: 0%;">0%</div>
                            <div class="bar bar-2" id="diff-auto-bar" style="width: 0%;">0%</div>
                        </div>
                    </div>
                    <div class="span3">
                        <span id="diff-auto-value">0</span> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_GLOBAL_DIFF_AUTO'); ?>
                    </div>
                </div>


                <div class="result-success row-fluid" style="text-align: center">
                    <span class="nav-header">
                        <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_GLOBAL_FEES'); ?>
                    </span> 
                </div>
                <div class="result-success row-fluid">
                    <div class="span3" style="text-align: right">
                        <span id="diff-fee-value">0</span> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_GLOBAL_DIFF_FEE'); ?>
                    </div>
                    <div class="span6">
                        <div class="progress">
                            <div class="bar bar-1" id="diff-fee-bar" style="width: 0%;">0%</div>
                            <div class="bar bar-2" id="diff-free-bar" style="width: 0%;">0%</div>
                        </div>
                    </div>
                    <div class="span3">
                        <span id="diff-free-value">0</span> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_GLOBAL_DIFF_FREE'); ?>
                    </div>
                </div>                
            </div>
        </div>
    </div>

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
            beforeSend: function() {
                toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'waiting-for-result');
            },
            success: function(json) {

                toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'result-success');



                /* values */
                var totalWithExtraction = parseInt(json.data.total_diff_hasextraction) + parseInt(json.data.total_diff_hasdownandext);
                var pctManu = Math.round(100 * json.data.total_diff_manual / totalWithExtraction);
                var pctAuto = 100 - pctManu;

                /* title */
                jQuery('#total-diff').html(json.data.total_diff);
                jQuery('#total-diff-ext').html(totalWithExtraction);

                /* bars */
                jQuery('#diff-manu-value').html(json.data.total_diff_manual);
                jQuery('#diff-auto-value').html(json.data.total_diff_auto);
                jQuery('#diff-manu-bar').html(pctManu + '%');
                jQuery('#diff-manu-bar').css('width', pctManu + '%');
                jQuery('#diff-auto-bar').html(pctAuto + '%');
                jQuery('#diff-auto-bar').css('width', pctAuto + '%');

                pctFee = Math.round(100 * json.data.total_diff_fee / json.data.total_diff);
                pctFree = 100 - pctFee;
                jQuery('#diff-fee-value').html(json.data.total_diff_fee);
                jQuery('#diff-free-value').html(json.data.total_diff_free);
                jQuery('#diff-fee-bar').html(pctFee + '%');
                jQuery('#diff-fee-bar').css('width', pctFee + '%');
                jQuery('#diff-free-bar').html(pctFree + '%');
                jQuery('#diff-free-bar').css('width', pctFree + '%');


                /* Extraction and download graph */
                var data = [];
                data.push({label: json.data.total_diff_hasextraction + ' <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_GLOBAL_DIFF_HASEXTRACTION'); ?>', data: json.data.total_diff_hasextraction});
                data.push({label: json.data.total_diff_hasdownload + ' <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_GLOBAL_DIFF_HASDOWNLOAD'); ?>', data: json.data.total_diff_hasdownload});
                data.push({label: json.data.total_diff_hasdownandext + ' <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_GLOBAL_DIFF_HASDOWNLOADANDEXTRACTION'); ?>', data: json.data.total_diff_hasdownandext});

                var plotObj = jQuery.plot(jQuery("#<?php echo 'div_' . $indicator_name . '_graph'; ?>"), data, {
                    series: {
                        pie: {
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
            },
            error: function(error, ajaxOption, throwError) {
                toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'no-result');
            }
        });
    }
    //Add event listener
    jQuery(document).on("dashboardFiltersUpdated", update_<?php echo($indicator_name); ?>);

</script>
