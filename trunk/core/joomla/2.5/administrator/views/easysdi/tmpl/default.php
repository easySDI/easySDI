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
$document->addStyleSheet('components/com_easysdi_core/assets/css/easysdi_core.css');

?>
<form action="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=easysdi'); ?>" method="post" name="adminForm" id="adminForm">

	<?php
	$html = JHtml::_('icons.buttons', $this->buttons);
	if (!empty($html)): ?>
		<div class="cpanel">
			<?php echo $html;?>
		</div>
	<?php endif;?>
	
</form>