<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

$indicator_name = $displayData['indicator_name'];
?>
<div class="pull-right hasTip sdi-dashboard-export-buttons" title="<?php echo JText::_('COM_EASYSDI_DASHBOARD_EXPORT_TOOLTIP'); ?>">
    <a target="_blank" data-sdi-report-format="csv" data-sdi-report-indicator="<?php echo($indicator_name); ?>" href="#" class="sdi-dashboard-report-link small ">
        <i class="icon-file"></i> <?php echo JText::_('COM_EASYSDI_DASHBOARD_EXPORT_CSV'); ?>
    </a> <a target="_blank" data-sdi-report-format="pdf" data-sdi-report-indicator="<?php echo($indicator_name); ?>" href="#" class="sdi-dashboard-report-link small ">
        <i class="icon-file-2"></i> <?php echo JText::_('COM_EASYSDI_DASHBOARD_EXPORT_PDF'); ?>
    </a>
</div>