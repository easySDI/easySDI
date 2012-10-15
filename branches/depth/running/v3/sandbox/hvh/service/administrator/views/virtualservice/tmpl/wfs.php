<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_service/assets/css/easysdi_service.css');

?>
<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&view=virtualservice&id='.JRequest::getVar('id',null)); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		
			 <fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_CONNECTOR_CHOICE' );?></legend>
				<table class="admintable">
					<tr>
						<td>
						<?php 
						echo JHTML::_("select.genericlist",$this->serviceconnectorlist, 'serviceconnector', 'size="1" onChange="document.getElementById(\'layout\').value=document.getElementById(\'serviceconnector\').value;submit()"', 'value', 'value', 'WFS'); ?>
						</td>
					</tr>
				</table>
			</fieldset>
			<?php
			$keywordString = "";
			foreach ($this->config->{"service-metadata"}->{'KeywordList'}->Keyword as $keyword)
			{
				$keywordString .= $keyword .",";
			}
			$keywordString = substr($keywordString, 0, strlen($keywordString)-1) ;
			
			$this->genericServletInformationsHeader ("WFS");
			?>
			<fieldset class="adminform" id="service_metadata" ><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_METADATA'); ?></legend>
				<table class="admintable" >
					<tr>
						<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_TITLE"); ?> : <span class="star">*</span></td>
						<td><input class="inputbox required" name="service_title" id="service_title" type="text" size=100 value="<?php echo $this->config->{"service-metadata"}->{"Title"}; ?>"></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_ABSTRACT"); ?> : </td>
						<td><input name="service_abstract" id="service_abstract" type="text" size=100 value="<?php echo $this->config->{"service-metadata"}->{"Abstract"}; ?>"></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_KEYWORD"); ?> : </td>
						<td><input name="service_keyword" id="service_keyword" type="text" size=100 value="<?php echo $keywordString; ?>"></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_FEES"); ?> : </td>
						<td><input name="service_fees" id="service_fees" type="text" size=100 value="<?php echo $this->config->{"service-metadata"}->{"Fees"}; ?>"></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONSTRAINTS"); ?> : </td>
						<td><input name="service_accessconstraints" id="service_accessconstraints" type="text" size=100 value="<?php echo $this->config->{"service-metadata"}->{"AccessConstraints"}; ?>"></td>
					</tr>
				</table>
			</fieldset>
			<?php
			$this->genericServletInformationsFooter ();
			?>
		
	</div>
	<input type="hidden" name="layout" id="layout" value="" />
	<input type="hidden" name="task" value="<?php echo JRequest::getCmd('task');?>" />
	<input type="hidden" name="previoustask" value="<?php echo JRequest::getCmd('task');?>" />
		
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>

		
    <style type="text/css">
        /* Temporary fix for drifting editor fields */
        .adminformlist li {
            clear: both;
        }
    </style>
</form>