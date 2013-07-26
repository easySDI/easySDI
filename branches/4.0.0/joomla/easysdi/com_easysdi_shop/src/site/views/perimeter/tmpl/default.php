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

            			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_GUID'); ?>:
			<?php echo $this->item->guid; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_ALIAS'); ?>:
			<?php echo $this->item->alias; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_CREATED'); ?>:
			<?php echo $this->item->created; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_MODIFIED_BY'); ?>:
			<?php echo $this->item->modified_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_MODIFIED'); ?>:
			<?php echo $this->item->modified; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_STATE'); ?>:
			<?php echo $this->item->state; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_CHECKED_OUT'); ?>:
			<?php echo $this->item->checked_out; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_CHECKED_OUT_TIME'); ?>:
			<?php echo $this->item->checked_out_time; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_NAME'); ?>:
			<?php echo $this->item->name; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_DESCRIPTION'); ?>:
			<?php echo $this->item->description; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_ACCESSSCOPE_ID'); ?>:
			<?php echo $this->item->accessscope_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_WFSSERVICE_ID'); ?>:
			<?php echo $this->item->wfsservice_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_FEATURETYPEID'); ?>:
			<?php echo $this->item->featuretypeid; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_FEATURETYPENAME'); ?>:
			<?php echo $this->item->featuretypename; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_FEATURETYPEFIELDSURFACE'); ?>:
			<?php echo $this->item->featuretypefieldsurface; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_FEATURETYPEFIELDDISPLAY'); ?>:
			<?php echo $this->item->featuretypefielddisplay; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_FEATURETYPEFIELDSEARCH'); ?>:
			<?php echo $this->item->featuretypefieldsearch; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_WMSSERVICE_ID'); ?>:
			<?php echo $this->item->wmsservice_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_LAYERNAME'); ?>:
			<?php echo $this->item->layername; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_MINSCALE'); ?>:
			<?php echo $this->item->minscale; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_MAXSCALE'); ?>:
			<?php echo $this->item->maxscale; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_ACCESS'); ?>:
			<?php echo $this->item->access; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_SHOP_FORM_LBL_PERIMETER_ASSET_ID'); ?>:
			<?php echo $this->item->asset_id; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=perimeter.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_EASYSDI_SHOP_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_shop.perimeter.'.$this->item->id)):
								?>
									<a href="javascript:document.getElementById('form-perimeter-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_EASYSDI_SHOP_DELETE_ITEM"); ?></a>
									<form id="form-perimeter-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_shop&task=perimeter.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
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
										<input type="hidden" name="jform[accessscope_id]" value="<?php echo $this->item->accessscope_id; ?>" />
										<input type="hidden" name="jform[wfsservice_id]" value="<?php echo $this->item->wfsservice_id; ?>" />
										<input type="hidden" name="jform[featuretypeid]" value="<?php echo $this->item->featuretypeid; ?>" />
										<input type="hidden" name="jform[featuretypename]" value="<?php echo $this->item->featuretypename; ?>" />
										<input type="hidden" name="jform[featuretypefieldsurface]" value="<?php echo $this->item->featuretypefieldsurface; ?>" />
										<input type="hidden" name="jform[featuretypefielddisplay]" value="<?php echo $this->item->featuretypefielddisplay; ?>" />
										<input type="hidden" name="jform[featuretypefieldsearch]" value="<?php echo $this->item->featuretypefieldsearch; ?>" />
										<input type="hidden" name="jform[wmsservice_id]" value="<?php echo $this->item->wmsservice_id; ?>" />
										<input type="hidden" name="jform[layername]" value="<?php echo $this->item->layername; ?>" />
										<input type="hidden" name="jform[minscale]" value="<?php echo $this->item->minscale; ?>" />
										<input type="hidden" name="jform[maxscale]" value="<?php echo $this->item->maxscale; ?>" />
										<input type="hidden" name="jform[access]" value="<?php echo $this->item->access; ?>" />
										<input type="hidden" name="jform[asset_id]" value="<?php echo $this->item->asset_id; ?>" />
										<input type="hidden" name="option" value="com_easysdi_shop" />
										<input type="hidden" name="task" value="perimeter.remove" />
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
