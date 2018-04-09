<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
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
                
                <li><a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=user&id=' . (int)$item->id); ?>"><?php echo $item->guid; ?></a></li>

    <?php endforeach; ?>
            
            </ul>

    </div>

     <div class="pagination">
        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
            <p class="counter">
                <?php echo $this->pagination->getPagesCounter(); ?>
            </p>
        <?php endif; ?>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>


<?php endif; ?>