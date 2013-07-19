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

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_easysdi_shop.' . $this->item->id);
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_easysdi_shop' . $this->item->id)) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

    <div class="item_fields">

        <ul class="fields_list">

            			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_GUID'); ?>:
			<?php echo $this->item->guid; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_ALIAS'); ?>:
			<?php echo $this->item->alias; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_CREATED'); ?>:
			<?php echo $this->item->created; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_MODIFIED_BY'); ?>:
			<?php echo $this->item->modified_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_MODIFIED'); ?>:
			<?php echo $this->item->modified; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_STATE'); ?>:
			<?php echo $this->item->state; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_CHECKED_OUT'); ?>:
			<?php echo $this->item->checked_out; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_CHECKED_OUT_TIME'); ?>:
			<?php echo $this->item->checked_out_time; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_VERSION_ID'); ?>:
			<?php echo $this->item->version_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_NAME'); ?>:
			<?php echo $this->item->name; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_DESCRIPTION'); ?>:
			<?php echo $this->item->description; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_ACCESSSCOPE_ID'); ?>:
			<?php echo $this->item->accessscope_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_PRICING_ID'); ?>:
			<?php echo $this->item->pricing_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_DEPOSIT'); ?>:

			<?php 
				$uploadPath = 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysdi_shop' . DIRECTORY_SEPARATOR . '/' . DIRECTORY_SEPARATOR . $this->item->deposit;
			?>
			<a href="<?php echo JRoute::_(JUri::base() . $uploadPath, false); ?>" target="_blank"><?php echo $this->item->deposit; ?></a></li>			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_PRODUCTMINING_ID'); ?>:
			<?php echo $this->item->productmining_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_SURFACEMIN'); ?>:
			<?php echo $this->item->surfacemin; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_SURFACEMAX'); ?>:
			<?php echo $this->item->surfacemax; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_PRODUCTSTORAGE_ID'); ?>:
			<?php echo $this->item->productstorage_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_FILE'); ?>:

			<?php 
				$uploadPath = 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysdi_shop' . DIRECTORY_SEPARATOR . '/' . DIRECTORY_SEPARATOR . $this->item->file;
			?>
			<a href="<?php echo JRoute::_(JUri::base() . $uploadPath, false); ?>" target="_blank"><?php echo $this->item->file; ?></a></li>			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_FILEURL'); ?>:
			<?php echo $this->item->fileurl; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_GRID_ID'); ?>:
			<?php echo $this->item->grid_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_ACCESS'); ?>:
			<?php echo $this->item->access; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_DIFFUSION_ASSET_ID'); ?>:
			<?php echo $this->item->asset_id; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_EASYSDI_SHOP_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_shop.diffusion.'.$this->item->id)):
								?>
									<a href="javascript:document.getElementById('form-diffusion-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_EASYSDI_SHOP_DELETE_ITEM"); ?></a>
									<form id="form-diffusion-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
										<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
										<input type="hidden" name="jform[guid]" value="<?php echo $this->item->guid; ?>" />
										<input type="hidden" name="jform[alias]" value="<?php echo $this->item->alias; ?>" />
										<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
										<input type="hidden" name="jform[created]" value="<?php echo $this->item->created; ?>" />
										<input type="hidden" name="jform[modified_by]" value="<?php echo $this->item->modified_by; ?>" />
										<input type="hidden" name="jform[modified]" value="<?php echo $this->item->modified; ?>" />
										<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
										<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
										<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
										<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
										<input type="hidden" name="jform[version_id]" value="<?php echo $this->item->version_id; ?>" />
										<input type="hidden" name="jform[name]" value="<?php echo $this->item->name; ?>" />
										<input type="hidden" name="jform[description]" value="<?php echo $this->item->description; ?>" />
										<input type="hidden" name="jform[accessscope_id]" value="<?php echo $this->item->accessscope_id; ?>" />
										<input type="hidden" name="jform[pricing_id]" value="<?php echo $this->item->pricing_id; ?>" />
										<input type="hidden" name="jform[deposit]" value="<?php echo $this->item->deposit; ?>" />
										<input type="hidden" name="jform[productmining_id]" value="<?php echo $this->item->productmining_id; ?>" />
										<input type="hidden" name="jform[surfacemin]" value="<?php echo $this->item->surfacemin; ?>" />
										<input type="hidden" name="jform[surfacemax]" value="<?php echo $this->item->surfacemax; ?>" />
										<input type="hidden" name="jform[productstorage_id]" value="<?php echo $this->item->productstorage_id; ?>" />
										<input type="hidden" name="jform[file]" value="<?php echo $this->item->file; ?>" />
										<input type="hidden" name="jform[fileurl]" value="<?php echo $this->item->fileurl; ?>" />
										<input type="hidden" name="jform[grid_id]" value="<?php echo $this->item->grid_id; ?>" />
										<input type="hidden" name="jform[access]" value="<?php echo $this->item->access; ?>" />
										<input type="hidden" name="jform[asset_id]" value="<?php echo $this->item->asset_id; ?>" />
										<input type="hidden" name="option" value="com_easysdi_shop" />
										<input type="hidden" name="task" value="diffusion.remove" />
										<?php echo JHtml::_('form.token'); ?>
									</form>
								<?php
								endif;
							?>
<?php
else:
    echo JText::_('COM_EASYSDI_SHOP_ITEM_NOT_LOADED');
endif;
?>
