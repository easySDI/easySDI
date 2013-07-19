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

            			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_GUID'); ?>:
			<?php echo $this->item->guid; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_ALIAS'); ?>:
			<?php echo $this->item->alias; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_CREATED'); ?>:
			<?php echo $this->item->created; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_MODIFIED_BY'); ?>:
			<?php echo $this->item->modified_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_MODIFIED'); ?>:
			<?php echo $this->item->modified; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_STATE'); ?>:
			<?php echo $this->item->state; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_CHECKED_OUT'); ?>:
			<?php echo $this->item->checked_out; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_CHECKED_OUT_TIME'); ?>:
			<?php echo $this->item->checked_out_time; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_NAME'); ?>:
			<?php echo $this->item->name; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_DESCRIPTION'); ?>:
			<?php echo $this->item->description; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_ACCESS'); ?>:
			<?php echo $this->item->access; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_ASSET_ID'); ?>:
			<?php echo $this->item->asset_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_MAP_ID'); ?>:
			<?php echo $this->item->map_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_WFSSERVICE_ID'); ?>:
			<?php echo $this->item->wfsservice_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_FEATURETYPE'); ?>:
			<?php echo $this->item->featuretype; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_PREFIX'); ?>:
			<?php echo $this->item->prefix; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_NAMESPACE'); ?>:
			<?php echo $this->item->namespace; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_FEATURETYPEFIELDNAME'); ?>:
			<?php echo $this->item->featuretypefieldname; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_FEATURETYPEFIELDDESCRIPTION'); ?>:
			<?php echo $this->item->featuretypefielddescription; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_FEATURETYPEFIELDGEOMETRY'); ?>:
			<?php echo $this->item->featuretypefieldgeometry; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_FEATURETYPEFIELDRESOURCE'); ?>:
			<?php echo $this->item->featuretypefieldresource; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_GRID_TOOLTIP'); ?>:
			<?php echo $this->item->tooltip; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=grid.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_EASYSDI_SHOP_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_shop.grid.'.$this->item->id)):
								?>
									<a href="javascript:document.getElementById('form-grid-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_EASYSDI_SHOP_DELETE_ITEM"); ?></a>
									<form id="form-grid-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=grid.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
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
										<input type="hidden" name="jform[description]" value="<?php echo $this->item->description; ?>" />
										<input type="hidden" name="jform[access]" value="<?php echo $this->item->access; ?>" />
										<input type="hidden" name="jform[asset_id]" value="<?php echo $this->item->asset_id; ?>" />
										<input type="hidden" name="jform[map_id]" value="<?php echo $this->item->map_id; ?>" />
										<input type="hidden" name="jform[wfsservice_id]" value="<?php echo $this->item->wfsservice_id; ?>" />
										<input type="hidden" name="jform[featuretype]" value="<?php echo $this->item->featuretype; ?>" />
										<input type="hidden" name="jform[prefix]" value="<?php echo $this->item->prefix; ?>" />
										<input type="hidden" name="jform[namespace]" value="<?php echo $this->item->namespace; ?>" />
										<input type="hidden" name="jform[featuretypefieldname]" value="<?php echo $this->item->featuretypefieldname; ?>" />
										<input type="hidden" name="jform[featuretypefielddescription]" value="<?php echo $this->item->featuretypefielddescription; ?>" />
										<input type="hidden" name="jform[featuretypefieldgeometry]" value="<?php echo $this->item->featuretypefieldgeometry; ?>" />
										<input type="hidden" name="jform[featuretypefieldresource]" value="<?php echo $this->item->featuretypefieldresource; ?>" />
										<input type="hidden" name="jform[tooltip]" value="<?php echo $this->item->tooltip; ?>" />
										<input type="hidden" name="option" value="com_easysdi_shop" />
										<input type="hidden" name="task" value="grid.remove" />
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
