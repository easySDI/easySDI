<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
$indicator_name = 'shop_extractionstype';
?>

<div id="<?php echo('div_' . $indicator_name); ?>">

    <div class="module-title nav-header">
        <i class="icon-bars" style="text-transform: none;"></i> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_EXTRACTIONSTYPE_TITLE'); ?>
    </div>

    <div class="result-success row-fluid">
        <div class="span3" style="text-align: right">
            <span id="ext-manu-value">0</span> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_EXTRACTIONSTYPE_DIFF_MANUAL'); ?>
        </div>
        <div class="span6">
            <div class="progress">
                <div class="bar bar-1" id="ext-manu-bar" style="width: 0%;">0%</div>
                <div class="bar bar-2" id="ext-auto-bar" style="width: 0%;">0%</div>
            </div>
        </div>
        <div class="span3">
            <span id="ext-auto-value">0</span> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_EXTRACTIONSTYPE_DIFF_AUTO'); ?>
        </div>
    </div>


    <div class="result-success row-fluid">
        <div class="span3" style="text-align: right">
            <span id="ext-fee-value">0</span> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_EXTRACTIONSTYPE_DIFF_FEE'); ?>
        </div>
        <div class="span6">
            <div class="progress">
                <div class="bar bar-1" id="ext-fee-bar" style="width: 0%;">0%</div>
                <div class="bar bar-2" id="ext-free-bar" style="width: 0%;">0%</div>
            </div>
        </div>
        <div class="span3">
            <span id="ext-free-value">0</span> <?php echo JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_EXTRACTIONSTYPE_DIFF_FREE'); ?>
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
            url: '<?php echo JURI::base() ?>index.php',
            dataType: 'json',
            data: {option: "com_easysdi_dashboard",
                task: "getData",
                indicator: "<?php echo($indicator_name); ?>",
                organism: e.organism,
                timestart: e.timestart,
                timeend: e.timeend,
                dataformat: "json",
                format: "raw",
                limit: 0
            },
            beforeSend: function () {
                toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'waiting-for-result');
            },
            success: function (json) {

                if (json.data.total_ext > 0) {
                    toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'result-success');

                    /* values */
                    var pctManu = Math.round(100 * json.data.total_ext_manual / json.data.total_ext);
                    var pctAuto = 100 - pctManu;

                    /* bars */
                    jQuery('#ext-manu-value').html(json.data.total_ext_manual);
                    jQuery('#ext-auto-value').html(json.data.total_ext_auto);
                    jQuery('#ext-manu-bar').html(pctManu + '%');
                    jQuery('#ext-manu-bar').css('width', pctManu + '%');
                    jQuery('#ext-auto-bar').html(pctAuto + '%');
                    jQuery('#ext-auto-bar').css('width', pctAuto + '%');

                    var pctFee = Math.round(100 * json.data.total_ext_fee / json.data.total_ext);
                    var pctFree = 100 - pctFee;
                    jQuery('#ext-fee-value').html(json.data.total_ext_fee);
                    jQuery('#ext-free-value').html(json.data.total_ext_free);
                    jQuery('#ext-fee-bar').html(pctFee + '%');
                    jQuery('#ext-fee-bar').css('width', pctFee + '%');
                    jQuery('#ext-free-bar').html(pctFree + '%');
                    jQuery('#ext-free-bar').css('width', pctFree + '%');

                } else {
                    toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'no-result');
                }


            },
            error: function (error, ajaxOption, throwError) {
                toggleResutlDiv(<?php echo('"#div_' . $indicator_name . '"'); ?>, 'no-result');
            }
        });
    }
    //Add event listener
    jQuery(document).on("dashboardFiltersUpdated", update_<?php echo($indicator_name); ?>);

</script>
