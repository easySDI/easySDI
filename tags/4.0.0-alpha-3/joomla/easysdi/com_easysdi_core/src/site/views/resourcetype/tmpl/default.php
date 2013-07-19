<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_core', JPATH_ADMINISTRATOR);
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_easysdi_core.' . $this->item->id);
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_easysdi_core' . $this->item->id)) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

    <div class="item_fields">

        <ul class="fields_list">

            			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_GUID'); ?>:
			<?php echo $this->item->guid; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_ALIAS'); ?>:
			<?php echo $this->item->alias; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_CREATED'); ?>:
			<?php echo $this->item->created; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_MODIFIED_BY'); ?>:
			<?php echo $this->item->modified_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_MODIFIED'); ?>:
			<?php echo $this->item->modified; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_STATE'); ?>:
			<?php echo $this->item->state; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_CHECKED_OUT'); ?>:
			<?php echo $this->item->checked_out; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_CHECKED_OUT_TIME'); ?>:
			<?php echo $this->item->checked_out_time; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_NAME'); ?>:
			<?php echo $this->item->name; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_DESCRIPTION'); ?>:
			<?php echo $this->item->description; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_LOGO'); ?>:
			<?php echo $this->item->logo; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_METADATA'); ?>:
			<?php echo $this->item->metadata; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_DIFFUSION'); ?>:
			<?php echo $this->item->diffusion; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_VIEW'); ?>:
			<?php echo $this->item->view; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_MONITORING'); ?>:
			<?php echo $this->item->monitoring; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_PREDEFINED'); ?>:
			<?php echo $this->item->predefined; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_VERSIONNING'); ?>:
			<?php echo $this->item->versionning; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPE_ACCESS'); ?>:
			<?php echo $this->item->access; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourcetype.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_EASYSDI_CORE_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_core.resourcetype.'.$this->item->id)):
								?>
									<a href="javascript:document.getElementById('form-resourcetype-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_EASYSDI_CORE_DELETE_ITEM"); ?></a>
									<form id="form-resourcetype-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourcetype.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
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
										<input type="hidden" name="jform[logo]" value="<?php echo $this->item->logo; ?>" />
										<input type="hidden" name="jform[metadata]" value="<?php echo $this->item->metadata; ?>" />
										<input type="hidden" name="jform[diffusion]" value="<?php echo $this->item->diffusion; ?>" />
										<input type="hidden" name="jform[view]" value="<?php echo $this->item->view; ?>" />
										<input type="hidden" name="jform[monitoring]" value="<?php echo $this->item->monitoring; ?>" />
										<input type="hidden" name="jform[predefined]" value="<?php echo $this->item->predefined; ?>" />
										<input type="hidden" name="jform[versionning]" value="<?php echo $this->item->versionning; ?>" />
										<input type="hidden" name="jform[access]" value="<?php echo $this->item->access; ?>" />
										<input type="hidden" name="option" value="com_easysdi_core" />
										<input type="hidden" name="task" value="resourcetype.remove" />
										<?php echo JHtml::_('form.token'); ?>
									</form>
								<?php
								endif;
							?>
<?php
else:
    echo JText::_('COM_EASYSDI_CORE_ITEM_NOT_LOADED');
endif;
?>
