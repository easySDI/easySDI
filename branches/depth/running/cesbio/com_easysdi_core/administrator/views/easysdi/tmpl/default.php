<?php
/**
 * @version     3.0.0
  * @package     com_easysdi_user
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document = &JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_core/assets/css/easysdi_user.css');

?>
<form action="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=easysdi'); ?>" method="post" name="adminForm" id="adminForm">
	
	<?php if($this->user){?>
	<fieldset class="adminsdiform">
	<legend class="adminsdilegend adminsdicorelegend"><?php echo JText::_('COM_EASYSDI_CORE_LEGEND_EASYSDI'); ?></legend>
		<div>
			<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('user') as $field): ?>
				<li><?php echo $field->input;?></li>
			<?php endforeach; ?>
			</ul>
		</div>
	</fieldset>
	<?php }?>
	<?php if($this->service){?>
	<fieldset class="adminsdiform">
	<legend class="adminsdilegend adminsdiservicelegend"><?php echo JText::_('COM_EASYSDI_SERVICE_LEGEND_EASYSDI'); ?></legend>
		<div>
			<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('service') as $field): ?>
				<li><?php echo $field->input;?></li>
			<?php endforeach; ?>
			</ul>
		</div>
	</fieldset>
	<?php }?>	
</form>