<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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
								<a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=orderdiffusion&id=' . (int)$item->id); ?>"><?php echo $item->remark; ?></a>
								<?php
									if(JFactory::getUser()->authorise('core.delete','com_easysdi_shop')):
									?>
										<a href="javascript:document.getElementById('form-orderdiffusion-delete-<?php echo $item->id; ?>').submit()"><?php echo JText::_("COM_EASYSDI_SHOP_DELETE_ITEM"); ?></a>
										<form id="form-orderdiffusion-delete-<?php echo $item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=orderdiffusion.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[order_id]" value="<?php echo $item->order_id; ?>" />
											<input type="hidden" name="jform[diffusion_id]" value="<?php echo $item->diffusion_id; ?>" />
											<input type="hidden" name="jform[productstate_id]" value="<?php echo $item->productstate_id; ?>" />
											<input type="hidden" name="jform[remark]" value="<?php echo $item->remark; ?>" />
											<input type="hidden" name="jform[fee]" value="<?php echo $item->fee; ?>" />
											<input type="hidden" name="jform[completed]" value="<?php echo $item->completed; ?>" />
											<input type="hidden" name="jform[file]" value="<?php echo $item->file; ?>" />
											<input type="hidden" name="jform[size]" value="<?php echo $item->size; ?>" />
											<input type="hidden" name="jform[created_by]" value="<?php echo $item->created_by; ?>" />
											<input type="hidden" name="option" value="com_easysdi_shop" />
											<input type="hidden" name="task" value="orderdiffusion.remove" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
								?>
							</li>

<?php endforeach; ?>
        <?php
        if (!$show):
            echo JText::_('COM_EASYSDI_SHOP_NO_ITEMS');
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


									<?php if(JFactory::getUser()->authorise('core.create','com_easysdi_shop')): ?><a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=orderdiffusion.edit&id=0'); ?>"><?php echo JText::_("COM_EASYSDI_SHOP_ADD_ITEM"); ?></a>
	<?php endif; ?>