<?php
/**
 * @version     4.3.2
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('JPATH_BASE') or die;
JText::script('COM_EASYSDI_DASHBOARD_ERROR_DATES_PROBLEM');
?>
<div class="modal hide" id="chooseTimeReporting">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3><?php echo JText::_('COM_EASYSDI_DASHBOARD_REPORTING_TITLE'); ?></h3>
    </div>
    <div class="modal-body" style="min-height: 210px">
        <form id="hiddenReportForm" name="hiddenReportForm" action="index.php" method="GET" target="_blank">
            <input type="hidden" name="option"     value="com_easysdi_dashboard" />
            <input type="hidden" name="indicator"  value=""      id="report-indicator"/> 
            <input type="hidden" name="task"       value="getData" /> 
            <input type="hidden" name="organism"   value=""      id="report-organism"/> 
            <input type="hidden" name="timestart"  value=""      id="report-timestart"/> 
            <input type="hidden" name="timeend"    value=""      id="report-timeend"/> 
            <input type="hidden" name="dataformat" value=""      id="report-dataformat"/> 
            <input type="hidden" name="format"     value="raw" /> 
            <input type="hidden" name="limit"      value=""      id="report-limit"/> 
        </form>
        <form id="reportConfigForm" name="reportConfigForm" class="form-horizontal">
          

            <div class="control-group">
                <label class="control-label" for="reporting-date"><?php echo JText::_('COM_EASYSDI_DASHBOARD_REPORTING_PERIOD'); ?></label>
                <div class="controls">
                    <?php
                    $dateFilters = Easysdi_dashboardHelper::getDateFilterList();
                    array_push($dateFilters, (object) array('value' => '-1', 'text' => JText::_('COM_EASYSDI_DASHBOARD_REPORTING_TIME_CUSTOM')));
                    ?>
                    <?php echo JHTML::_('select.genericlist', $dateFilters, 'reporting-date', ' onchange="updateReportingDates();"'); ?><br/>
                </div>
            </div>
            <div class="control-group advanced-time-reporting"  style="display: none;">
                <label class="control-label" for="reporting_date_from"><?php echo JText::_('COM_EASYSDI_DASHBOARD_REPORTING_FROM'); ?></label>
                <div class="controls">
                    <?php echo JHTML::calendar(date("Y-m-d", time()), 'reporting_date_from', 'reporting_date_from', '%Y-%m-%d', array( 'onchange' => 'checkCustomReportTime()' )); ?>
                </div>
            </div>
            <div class="control-group advanced-time-reporting"  style="display: none;">
                <label class="control-label" for="reporting_date_to"><?php echo JText::_('COM_EASYSDI_DASHBOARD_REPORTING_TO'); ?></label>
                <div class="controls">
                    <?php echo JHTML::calendar(date("Y-m-d", time()), 'reporting_date_to', 'reporting_date_to', '%Y-%m-%d', array( 'onchange' => 'checkCustomReportTime()' )); ?>
                </div>
            </div>    
            <div class="control-group">
                <label class="control-label" for="reporting-limit"><?php echo JText::_('COM_EASYSDI_DASHBOARD_REPORTING_LIMIT'); ?></label>
                <div class="controls">
                    <?php echo JHTML::_('select.genericlist', Easysdi_dashboardHelper::getReportLimitList(), 'reporting-limit'); ?>
                </div>
            </div>                  
            <div class="control-group">
                <label class="control-label" for="reporting-format"><?php echo JText::_('COM_EASYSDI_DASHBOARD_REPORTING_FORMAT'); ?></label>
                <div class="controls">
                    <?php echo JHTML::_('select.genericlist', Easysdi_dashboardHelper::getReportFormatList(), 'reporting-format'); ?>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('COM_EASYSDI_DASHBOARD_REPORTING_CLOSE'); ?></a>
        <a href="#" id="generateReport" class="btn btn-primary" onClick="generateReport();"><?php echo JText::_('COM_EASYSDI_DASHBOARD_REPORTING_DOWNLOAD'); ?></a>
    </div>
</div>
