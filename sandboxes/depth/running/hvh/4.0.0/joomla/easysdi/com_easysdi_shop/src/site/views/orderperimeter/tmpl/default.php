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

            			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERPERIMETER_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERPERIMETER_ORDER_ID'); ?>:
			<?php echo $this->item->order_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERPERIMETER_PERIMETER_ID'); ?>:
			<?php echo $this->item->perimeter_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERPERIMETER_VALUE'); ?>:
			<?php echo $this->item->value; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERPERIMETER_TEXT'); ?>:
			<?php echo $this->item->text; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERPERIMETER_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=orderperimeter.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_EASYSDI_SHOP_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_shop')):
								?>
									<a href="javascript:document.getElementById('form-orderperimeter-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_EASYSDI_SHOP_DELETE_ITEM"); ?></a>
									<form id="form-orderperimeter-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=orderperimeter.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
										<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
										<input type="hidden" name="jform[order_id]" value="<?php echo $this->item->order_id; ?>" />
										<input type="hidden" name="jform[perimeter_id]" value="<?php echo $this->item->perimeter_id; ?>" />
										<input type="hidden" name="jform[value]" value="<?php echo $this->item->value; ?>" />
										<input type="hidden" name="jform[text]" value="<?php echo $this->item->text; ?>" />
										<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
										<input type="hidden" name="option" value="com_easysdi_shop" />
										<input type="hidden" name="task" value="orderperimeter.remove" />
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
