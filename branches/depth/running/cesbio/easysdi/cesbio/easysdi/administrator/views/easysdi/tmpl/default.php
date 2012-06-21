<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=easysdi'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
	<legend><?php echo JText::_('COM_EASYSDI_CORE_LEGEND_EASYSDI'); ?></legend>
		<?php foreach($this->form->getFieldset('ctrlpanel') as $field): ?>
			<li><?php echo $field->input;?></li>
		<?php endforeach; ?>
	</fieldset>
</form>