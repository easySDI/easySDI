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

            			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_GUID'); ?>:
			<?php echo $this->item->guid; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_ALIAS'); ?>:
			<?php echo $this->item->alias; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_CREATED'); ?>:
			<?php echo $this->item->created; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_MODIFIED_BY'); ?>:
			<?php echo $this->item->modified_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_MODIFIED'); ?>:
			<?php echo $this->item->modified; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_STATE'); ?>:
			<?php echo $this->item->state; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_CHECKED_OUT'); ?>:
			<?php echo $this->item->checked_out; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_CHECKED_OUT_TIME'); ?>:
			<?php echo $this->item->checked_out_time; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_NAME'); ?>:
			<?php echo $this->item->name; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_ACCESS'); ?>:
			<?php echo $this->item->access; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_ASSET_ID'); ?>:
			<?php echo $this->item->asset_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_ORDERTYPE_ID'); ?>:
			<?php echo $this->item->ordertype_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_ORDERSTATE_ID'); ?>:
			<?php echo $this->item->orderstate_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_USER_ID'); ?>:
			<?php echo $this->item->user_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_THIRDPARTY_ID'); ?>:
			<?php echo $this->item->thirdparty_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_BUFFER'); ?>:
			<?php echo $this->item->buffer; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_SURFACE'); ?>:
			<?php echo $this->item->surface; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_REMARK'); ?>:
			<?php echo $this->item->remark; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_SENT'); ?>:
			<?php echo $this->item->sent; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDER_COMPLETED'); ?>:
			<?php echo $this->item->completed; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_EASYSDI_SHOP_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_shop.order.'.$this->item->id)):
								?>
									<a href="javascript:document.getElementById('form-order-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_EASYSDI_SHOP_DELETE_ITEM"); ?></a>
									<form id="form-order-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=order.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
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
										<input type="hidden" name="jform[name]" value="<?php echo $this->item->name; ?>" />
										<input type="hidden" name="jform[access]" value="<?php echo $this->item->access; ?>" />
										<input type="hidden" name="jform[asset_id]" value="<?php echo $this->item->asset_id; ?>" />
										<input type="hidden" name="jform[ordertype_id]" value="<?php echo $this->item->ordertype_id; ?>" />
										<input type="hidden" name="jform[orderstate_id]" value="<?php echo $this->item->orderstate_id; ?>" />
										<input type="hidden" name="jform[user_id]" value="<?php echo $this->item->user_id; ?>" />
										<input type="hidden" name="jform[thirdparty_id]" value="<?php echo $this->item->thirdparty_id; ?>" />
										<input type="hidden" name="jform[buffer]" value="<?php echo $this->item->buffer; ?>" />
										<input type="hidden" name="jform[surface]" value="<?php echo $this->item->surface; ?>" />
										<input type="hidden" name="jform[remark]" value="<?php echo $this->item->remark; ?>" />
										<input type="hidden" name="jform[sent]" value="<?php echo $this->item->sent; ?>" />
										<input type="hidden" name="jform[completed]" value="<?php echo $this->item->completed; ?>" />
										<input type="hidden" name="option" value="com_easysdi_shop" />
										<input type="hidden" name="task" value="order.remove" />
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
