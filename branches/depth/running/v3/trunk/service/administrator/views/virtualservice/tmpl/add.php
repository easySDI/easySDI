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

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="width-60 fltlft">
		
		   
		   <fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_CONNECTOR_CHOICE' );?></legend>
				<table class="admintable">
					<tr>
						<td>
						<?php 
						echo JHTML::_("select.genericlist",$this->serviceconnectorlist, 'serviceconnector', 'size="1" onChange="submit()"', 'value', 'value', null); ?>
						</td>
					</tr>
				</table>
			</fieldset>
		
	</div>
	
		<input type="hidden" name="task" value="virtualservice.add" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>

	
		
    <style type="text/css">
        /* Temporary fix for drifting editor fields */
        .adminformlist li {
            clear: both;
        }
    </style>
</form>