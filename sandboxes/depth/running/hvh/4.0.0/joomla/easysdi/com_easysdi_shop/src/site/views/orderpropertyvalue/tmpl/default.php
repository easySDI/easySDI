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

            			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERPROPERTYVALUE_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERPROPERTYVALUE_ORDERDIFFUSION_ID'); ?>:
			<?php echo $this->item->orderdiffusion_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERPROPERTYVALUE_PROPERTY_ID'); ?>:
			<?php echo $this->item->property_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERPROPERTYVALUE_PROPERTYVALUE_ID'); ?>:
			<?php echo $this->item->propertyvalue_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERPROPERTYVALUE_PROPERTYVALUE'); ?>:
			<?php echo $this->item->propertyvalue; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_ORDERPROPERTYVALUE_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=orderpropertyvalue.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_EASYSDI_SHOP_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_shop')):
								?>
									<a href="javascript:document.getElementById('form-orderpropertyvalue-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_EASYSDI_SHOP_DELETE_ITEM"); ?></a>
									<form id="form-orderpropertyvalue-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=orderpropertyvalue.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
										<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
										<input type="hidden" name="jform[orderdiffusion_id]" value="<?php echo $this->item->orderdiffusion_id; ?>" />
										<input type="hidden" name="jform[property_id]" value="<?php echo $this->item->property_id; ?>" />
										<input type="hidden" name="jform[propertyvalue_id]" value="<?php echo $this->item->propertyvalue_id; ?>" />
										<input type="hidden" name="jform[propertyvalue]" value="<?php echo $this->item->propertyvalue; ?>" />
										<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
										<input type="hidden" name="option" value="com_easysdi_shop" />
										<input type="hidden" name="task" value="orderpropertyvalue.remove" />
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
