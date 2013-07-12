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

            			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_GUID'); ?>:
			<?php echo $this->item->guid; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_ALIAS'); ?>:
			<?php echo $this->item->alias; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_CREATED'); ?>:
			<?php echo $this->item->created; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_MODIFIED_BY'); ?>:
			<?php echo $this->item->modified_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_MODIFIED'); ?>:
			<?php echo $this->item->modified; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_METADATASTATE_ID'); ?>:
			<?php echo $this->item->metadatastate_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_CHECKED_OUT'); ?>:
			<?php echo $this->item->checked_out; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_CHECKED_OUT_TIME'); ?>:
			<?php echo $this->item->checked_out_time; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_ACCESSSCOPE_ID'); ?>:
			<?php echo $this->item->accessscope_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_NAME'); ?>:
			<?php echo $this->item->name; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_PUBLISHED'); ?>:
			<?php echo $this->item->published; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_ARCHIVED'); ?>:
			<?php echo $this->item->archived; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_LASTSYNCHRONIZATION'); ?>:
			<?php echo $this->item->lastsynchronization; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_SYNCHRONIZED_BY'); ?>:
			<?php echo $this->item->synchronized_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_NOTIFICATION'); ?>:
			<?php echo $this->item->notification; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CATALOG_FORM_LBL_METADATA_VERSION_ID'); ?>:
			<?php echo $this->item->version_id; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_EASYSDI_CATALOG_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_catalog.metadata.'.$this->item->id)):
								?>
									<a href="javascript:document.getElementById('form-metadata-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_EASYSDI_CATALOG_DELETE_ITEM"); ?></a>
									<form id="form-metadata-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
										<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
										<input type="hidden" name="jform[guid]" value="<?php echo $this->item->guid; ?>" />
										<input type="hidden" name="jform[alias]" value="<?php echo $this->item->alias; ?>" />
										<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
										<input type="hidden" name="jform[created]" value="<?php echo $this->item->created; ?>" />
										<input type="hidden" name="jform[modified_by]" value="<?php echo $this->item->modified_by; ?>" />
										<input type="hidden" name="jform[modified]" value="<?php echo $this->item->modified; ?>" />
										<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
										<input type="hidden" name="jform[metadatastate_id]" value="<?php echo $this->item->metadatastate_id; ?>" />
										<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
										<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
										<input type="hidden" name="jform[accessscope_id]" value="<?php echo $this->item->accessscope_id; ?>" />
										<input type="hidden" name="jform[name]" value="<?php echo $this->item->name; ?>" />
										<input type="hidden" name="jform[published]" value="<?php echo $this->item->published; ?>" />
										<input type="hidden" name="jform[archived]" value="<?php echo $this->item->archived; ?>" />
										<input type="hidden" name="jform[lastsynchronization]" value="<?php echo $this->item->lastsynchronization; ?>" />
										<input type="hidden" name="jform[synchronized_by]" value="<?php echo $this->item->synchronized_by; ?>" />
										<input type="hidden" name="jform[notification]" value="<?php echo $this->item->notification; ?>" />
										<input type="hidden" name="jform[version_id]" value="<?php echo $this->item->version_id; ?>" />
										<input type="hidden" name="option" value="com_easysdi_catalog" />
										<input type="hidden" name="task" value="metadata.remove" />
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
