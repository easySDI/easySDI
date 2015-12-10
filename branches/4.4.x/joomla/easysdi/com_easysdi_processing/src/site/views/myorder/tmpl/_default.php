<?php
/**
 * @version     4.3.2
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_processing/assets/css/easysdi_processing.css');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if(task == 'order.cancel')
            location.href='./index.php?option=com_easysdi_processing&view=orders';
    }
</script>

<?php
    $item= $this->item;
?>
<h1><?php echo $item->name; ?></h1>
<div class='row'>
    <div class="span6 offset3 well form-horizontal">

        <div class="control-group" >
            <div class="control-label"><?php echo JText::_('COM_EASYSDI_PROCESSING_FORM_LBL_ORDER_NAME'); ?>&nbsp;</div>
            <div class="controls"><?php echo $item->name; ?></div>
        </div>

        <div class="control-group" >
            <div class="control-label"><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_FORM_LBL_PROCESSING'); ?>&nbsp;</div>
            <div class="controls"><?php echo JText::_($item->processing_label);?></div>
        </div>

        <div class="control-group" >
            <div class="control-label"><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_FORM_LBL_STATUS'); ?>&nbsp;</div>
            <div class="controls"><?php echo Easysdi_processingStatusHelper::status($item->status) ;?> </div>
        </div>

        <div class="control-group" >
            <div class="control-label"><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_FORM_LBL_USER'); ?>&nbsp;</div>
            <div class="controls"><?php echo $item->user_label ;?> </div>
        </div>

        <div class="control-group" >
            <div class="control-label"><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_FORM_LBL_CREATED'); ?>&nbsp;</div>
            <div class="controls"><?php echo $item->created ;?> </div>
        </div>

        <div class="control-group" >
            <div class="control-label" ><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_FORM_LBL_COMPLETED'); ?>&nbsp;</div>
            <div class="controls"><?php echo $item->completed; ?></div>
        </div>

        <div class="control-group" >
            <div class="control-label"><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_FORM_LBL_PARAMS'); ?>&nbsp;</div>
            <div class="controls"><?php echo Easysdi_processingParamsHelper::table($item->processing_parameters,$item->parameters) ?></div>
        </div>

        <div class="control-group" >
            <div class="control-label"><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_FORM_LBL_INPUT'); ?>&nbsp;</div>
            <div class="controls"><?php /*var_dump($item;)*/ ?> </div>
        </div>

        <div class="control-group" >
            <div class="control-label"><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_FORM_LBL_COMMANDLINE'); ?>&nbsp;</div>
            <div class="controls"><?php echo $item->output ;?> </div>
        </div>

        <div class="control-group" >
            <div class="control-label"><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDER_FORM_LBL_OUTPUT'); ?>&nbsp;</div>
            <div class="controls"><?php echo $item->output ;?> </div>
        </div>
    </div>
</div>


