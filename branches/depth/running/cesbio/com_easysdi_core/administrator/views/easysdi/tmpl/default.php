<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document = &JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_core/assets/css/easysdi_core.css');

?>
<form action="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=easysdi'); ?>" method="post" name="adminForm" id="adminForm">
	
	<?php 
	foreach($this->form->getFieldsets() as $fieldset)
	{
		?>
		<fieldset class="adminsdiform">
		<legend class="adminsdilegend adminsdi<?php echo $fieldset->name; ?>legend"><?php echo JText::_($fieldset->text); ?></legend>
			<div>
				<ul class="adminformlist">
				<?php foreach($this->form->getFieldset($fieldset->name) as $field): ?>
					<li><?php echo $field->input;?></li>
				<?php endforeach; ?>
				</ul>
			</div>
		</fieldset>
		<?php 
	}
	?>
</form>