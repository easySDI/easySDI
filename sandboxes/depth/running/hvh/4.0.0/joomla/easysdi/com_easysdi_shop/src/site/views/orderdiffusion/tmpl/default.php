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
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_easysdi_shop');
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_easysdi_shop')) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

    <div class="item_fields">

        <ul class="fields_list">

            			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_ORDER_ID'); ?>:
			<?php echo $this->item->order_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_DIFFUSION_ID'); ?>:
			<?php echo $this->item->diffusion_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_PRODUCTSTATE_ID'); ?>:
			<?php echo $this->item->productstate_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_REMARK'); ?>:
			<?php echo $this->item->remark; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_FEE'); ?>:
			<?php echo $this->item->fee; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_COMPLETED'); ?>:
			<?php echo $this->item->completed; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_FILE'); ?>:
			<?php echo $this->item->file; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_SIZE'); ?>:
			<?php echo $this->item->size; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERDIFFUSION_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=orderdiffusion.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_EASYSDI_SHOP_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_shop')):
								?>
									<a href="javascript:document.getElementById('form-orderdiffusion-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_EASYSDI_SHOP_DELETE_ITEM"); ?></a>
									<form id="form-orderdiffusion-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=orderdiffusion.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
										<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
										<input type="hidden" name="jform[order_id]" value="<?php echo $this->item->order_id; ?>" />
										<input type="hidden" name="jform[diffusion_id]" value="<?php echo $this->item->diffusion_id; ?>" />
										<input type="hidden" name="jform[productstate_id]" value="<?php echo $this->item->productstate_id; ?>" />
										<input type="hidden" name="jform[remark]" value="<?php echo $this->item->remark; ?>" />
										<input type="hidden" name="jform[fee]" value="<?php echo $this->item->fee; ?>" />
										<input type="hidden" name="jform[completed]" value="<?php echo $this->item->completed; ?>" />
										<input type="hidden" name="jform[file]" value="<?php echo $this->item->file; ?>" />
										<input type="hidden" name="jform[size]" value="<?php echo $this->item->size; ?>" />
										<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
										<input type="hidden" name="option" value="com_easysdi_shop" />
										<input type="hidden" name="task" value="orderdiffusion.remove" />
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
