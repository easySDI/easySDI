<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;
?>

<?php if($this->items) : ?>

    <div class="items">

            <ul class="items_list">
                
    <?php foreach ($this->items as $item) :?>
                
                <li><a href="<?php echo JRoute::_('index.php?option=com_easysdi_service&view=service&id=' . (int)$item->id); ?>"><?php echo $item->id; ?></a></li>

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