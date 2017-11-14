<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
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
					if($item->state == 1 || ($item->state == 0 && JFactory::getUser()->authorise('core.edit.own',' com_easysdi_shop.diffusion.'.$item->id))):
						$show = true;
						?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&view=diffusion&id=' . (int)$item->id); ?>"><?php echo $item->guid; ?></a>
								<?php
									if(JFactory::getUser()->authorise('core.edit.state','com_easysdi_shop.diffusion.'.$item->id)):
									?>
										<a href="javascript:document.getElementById('form-diffusion-state-<?php echo $item->id; ?>').submit()"><?php if($item->state == 1): echo JText::_("COM_EASYSDI_SHOP_UNPUBLISH_ITEM"); else: echo JText::_("COM_EASYSDI_SHOP_PUBLISH_ITEM"); endif; ?></a>
										<form id="form-diffusion-state-<?php echo $item->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[guid]" value="<?php echo $item->guid; ?>" />
											<input type="hidden" name="jform[alias]" value="<?php echo $item->alias; ?>" />
											<input type="hidden" name="jform[created]" value="<?php echo $item->created; ?>" />
											<input type="hidden" name="jform[modified_by]" value="<?php echo $item->modified_by; ?>" />
											<input type="hidden" name="jform[modified]" value="<?php echo $item->modified; ?>" />
											<input type="hidden" name="jform[ordering]" value="<?php echo $item->ordering; ?>" />
											<input type="hidden" name="jform[state]" value="<?php echo (int)!((int)$item->state); ?>" />
											<input type="hidden" name="jform[checked_out]" value="<?php echo $item->checked_out; ?>" />
											<input type="hidden" name="jform[checked_out_time]" value="<?php echo $item->checked_out_time; ?>" />
											<input type="hidden" name="jform[version_id]" value="<?php echo $item->version_id; ?>" />
											<input type="hidden" name="jform[name]" value="<?php echo $item->name; ?>" />
											<input type="hidden" name="jform[description]" value="<?php echo $item->description; ?>" />
											<input type="hidden" name="jform[accessscope_id]" value="<?php echo $item->accessscope_id; ?>" />
											<input type="hidden" name="jform[pricing_id]" value="<?php echo $item->pricing_id; ?>" />
											<input type="hidden" name="jform[deposit]" value="<?php echo $item->deposit; ?>" />
											<input type="hidden" name="jform[productmining_id]" value="<?php echo $item->productmining_id; ?>" />
											<input type="hidden" name="jform[surfacemin]" value="<?php echo $item->surfacemin; ?>" />
											<input type="hidden" name="jform[surfacemax]" value="<?php echo $item->surfacemax; ?>" />
											<input type="hidden" name="jform[productstorage_id]" value="<?php echo $item->productstorage_id; ?>" />
											<input type="hidden" name="jform[file]" value="<?php echo $item->file; ?>" />
											<input type="hidden" name="jform[fileurl]" value="<?php echo $item->fileurl; ?>" />
											<input type="hidden" name="jform[grid_id]" value="<?php echo $item->grid_id; ?>" />
											<input type="hidden" name="jform[access]" value="<?php echo $item->access; ?>" />
											<input type="hidden" name="jform[asset_id]" value="<?php echo $item->asset_id; ?>" />
											<input type="hidden" name="option" value="com_easysdi_shop" />
											<input type="hidden" name="task" value="diffusion.save" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
									if(JFactory::getUser()->authorise('core.delete','com_easysdi_shop.diffusion.'.$item->id)):
									?>
										<a href="javascript:document.getElementById('form-diffusion-delete-<?php echo $item->id; ?>').submit()"><?php echo JText::_("COM_EASYSDI_SHOP_DELETE_ITEM"); ?></a>
										<form id="form-diffusion-delete-<?php echo $item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
											<input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
											<input type="hidden" name="jform[guid]" value="<?php echo $item->guid; ?>" />
											<input type="hidden" name="jform[alias]" value="<?php echo $item->alias; ?>" />
											<input type="hidden" name="jform[created_by]" value="<?php echo $item->created_by; ?>" />
											<input type="hidden" name="jform[created]" value="<?php echo $item->created; ?>" />
											<input type="hidden" name="jform[modified_by]" value="<?php echo $item->modified_by; ?>" />
											<input type="hidden" name="jform[modified]" value="<?php echo $item->modified; ?>" />
											<input type="hidden" name="jform[ordering]" value="<?php echo $item->ordering; ?>" />
											<input type="hidden" name="jform[state]" value="<?php echo $item->state; ?>" />
											<input type="hidden" name="jform[checked_out]" value="<?php echo $item->checked_out; ?>" />
											<input type="hidden" name="jform[checked_out_time]" value="<?php echo $item->checked_out_time; ?>" />
											<input type="hidden" name="jform[version_id]" value="<?php echo $item->version_id; ?>" />
											<input type="hidden" name="jform[name]" value="<?php echo $item->name; ?>" />
											<input type="hidden" name="jform[description]" value="<?php echo $item->description; ?>" />
											<input type="hidden" name="jform[accessscope_id]" value="<?php echo $item->accessscope_id; ?>" />
											<input type="hidden" name="jform[pricing_id]" value="<?php echo $item->pricing_id; ?>" />
											<input type="hidden" name="jform[deposit]" value="<?php echo $item->deposit; ?>" />
											<input type="hidden" name="jform[productmining_id]" value="<?php echo $item->productmining_id; ?>" />
											<input type="hidden" name="jform[surfacemin]" value="<?php echo $item->surfacemin; ?>" />
											<input type="hidden" name="jform[surfacemax]" value="<?php echo $item->surfacemax; ?>" />
											<input type="hidden" name="jform[productstorage_id]" value="<?php echo $item->productstorage_id; ?>" />
											<input type="hidden" name="jform[file]" value="<?php echo $item->file; ?>" />
											<input type="hidden" name="jform[fileurl]" value="<?php echo $item->fileurl; ?>" />
											<input type="hidden" name="jform[grid_id]" value="<?php echo $item->grid_id; ?>" />
											<input type="hidden" name="jform[access]" value="<?php echo $item->access; ?>" />
											<input type="hidden" name="jform[asset_id]" value="<?php echo $item->asset_id; ?>" />
											<input type="hidden" name="option" value="com_easysdi_shop" />
											<input type="hidden" name="task" value="diffusion.remove" />
											<?php echo JHtml::_('form.token'); ?>
										</form>
									<?php
									endif;
								?>
							</li>
						<?php endif; ?>

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


									<?php if(JFactory::getUser()->authorise('core.create','com_easysdi_shop')): ?><a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.edit&id=0'); ?>"><?php echo JText::_("COM_EASYSDI_SHOP_ADD_ITEM"); ?></a>
	<?php endif; ?>