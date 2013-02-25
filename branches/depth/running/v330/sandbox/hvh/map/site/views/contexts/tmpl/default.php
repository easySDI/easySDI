<?php
/**
 \* @version     3.3.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;
?>

<?php if($this->items) : ?>

    <div class="items">

        <ul class="items_list">

            <?php foreach ($this->items as $item) :?>

                
				<li><a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&view=context&id=' . (int)$item->id); ?>" target="_blank"><?php echo $item->name; ?></a></li>

            <?php endforeach; ?>

        </ul>

    </div>

     <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
    <?php if(JFactory::getUser()->authorise('core.create', 'com_easysdi_map.context')): ?>
		<!-- <a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=context.edit&id=0'); ?>">Add</a> -->
	<?php endif; ?>
<?php else: ?>
    
    There are no items in the list

<?php endif; ?>