<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_catalog', JPATH_ADMINISTRATOR);
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_easysdi_catalog.' . $this->item->id);
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_easysdi_catalog' . $this->item->id)) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

    <div class="item_fields">

        <ul class="fields_list">

            			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_GUID'); ?>:
			<?php echo $this->item->guid; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_ALIAS'); ?>:
			<?php echo $this->item->alias; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_CREATED'); ?>:
			<?php echo $this->item->created; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_MODIFIED_BY'); ?>:
			<?php echo $this->item->modified_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_MODIFIED'); ?>:
			<?php echo $this->item->modified; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_STATE'); ?>:
			<?php echo $this->item->state; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_CHECKED_OUT'); ?>:
			<?php echo $this->item->checked_out; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_CHECKED_OUT_TIME'); ?>:
			<?php echo $this->item->checked_out_time; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_NAME'); ?>:
			<?php echo $this->item->name; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_DESCRIPTION'); ?>:
			<?php echo $this->item->description; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_XSLDIRECTORY'); ?>:
			<?php echo $this->item->xsldirectory; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_ONINITRUNSEARCH'); ?>:
			<?php echo $this->item->oninitrunsearch; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_CATALOG_CSWFILTER'); ?>:
			<?php echo $this->item->cswfilter; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=catalog.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_EASYSDI_CATALOG_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_catalog.catalog.'.$this->item->id)):
								?>
									<a href="javascript:document.getElementById('form-catalog-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_EASYSDI_CATALOG_DELETE_ITEM"); ?></a>
									<form id="form-catalog-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=catalog.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
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
										<input type="hidden" name="jform[xsldirectory]" value="<?php echo $this->item->xsldirectory; ?>" />
										<input type="hidden" name="jform[oninitrunsearch]" value="<?php echo $this->item->oninitrunsearch; ?>" />
										<input type="hidden" name="jform[cswfilter]" value="<?php echo $this->item->cswfilter; ?>" />
										<input type="hidden" name="option" value="com_easysdi_catalog" />
										<input type="hidden" name="task" value="catalog.remove" />
										<?php echo JHtml::_('form.token'); ?>
									</form>
								<?php
								endif;
							?>
<?php
else:
    echo JText::_('COM_EASYSDI_CATALOG_ITEM_NOT_LOADED');
endif;
?>
