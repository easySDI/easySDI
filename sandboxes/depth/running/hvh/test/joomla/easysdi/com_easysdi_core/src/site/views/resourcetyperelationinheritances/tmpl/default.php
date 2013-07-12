<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;
?>

<div class="items">
    <ul class="items_list">
<?php $show = false; ?>
        <?php foreach ($this->items as $item) : ?>

            
				<?php
						$show = true;
						?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&view=resourcetyperelationinheritance&id=' . (int)$item->id); ?>"><?php echo $item->xpath; ?></a>
								<?php
									if(JFactory::getUser()->authorise('core.edit.state','com_easysdi_core.resourcetyperelationinheritance.'.$item->id)):
									?>
										<a href="javascript:document.getElementById('form-resourcetyperelationinheritance-state-<?php echo $item->id; ?>').submit()"><?php if($item->state == 1): echo JText::_("COM_EASYSDI_CORE_UNPUBLISH_ITEM"); else: echo JText::_("COM_EASYSDI_CORE_PUBLISH_ITEM"); endif; ?></a>
										<form id="form-resourcetyperelationinheritance-state-<?php echo $item->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourcetyperelationinheritance.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[resourcetyperelation_id]" value="<?php echo $item->resourcetyperelation_id; ?>" />
											<input type="hidden" name="jform[xpath]" value="<?php echo $item->xpath; ?>" />
											<input type="hidden" name="option" value="com_easysdi_core" />
											<input type="hidden" name="task" value="resourcetyperelationinheritance.save" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
									if(JFactory::getUser()->authorise('core.delete','com_easysdi_core.resourcetyperelationinheritance.'.$item->id)):
									?>
										<a href="javascript:document.getElementById('form-resourcetyperelationinheritance-delete-<?php echo $item->id; ?>').submit()"><?php echo JText::_("COM_EASYSDI_CORE_DELETE_ITEM"); ?></a>
										<form id="form-resourcetyperelationinheritance-delete-<?php echo $item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourcetyperelationinheritance.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[resourcetyperelation_id]" value="<?php echo $item->resourcetyperelation_id; ?>" />
											<input type="hidden" name="jform[xpath]" value="<?php echo $item->xpath; ?>" />
											<input type="hidden" name="option" value="com_easysdi_core" />
											<input type="hidden" name="task" value="resourcetyperelationinheritance.remove" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
								?>
							</li>

<?php endforeach; ?>
        <?php
        if (!$show):
            echo JText::_('COM_EASYSDI_CORE_NO_ITEMS');
        endif;
        ?>
    </ul>
</div>
<?php if ($show): ?>
    <div class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
<?php endif; ?>


									<?php if(JFactory::getUser()->authorise('core.create','com_easysdi_core')): ?><a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourcetyperelationinheritance.edit&id=0'); ?>"><?php echo JText::_("COM_EASYSDI_CORE_ADD_ITEM"); ?></a>
	<?php endif; ?>