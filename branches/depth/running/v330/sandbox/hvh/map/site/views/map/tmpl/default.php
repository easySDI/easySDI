<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_map', JPATH_ADMINISTRATOR);
?>
<?php if( $this->item ) : ?>

    <div class="item_fields">
        
        <ul class="fields_list">

			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_GUID'); ?>:
			<?php echo $this->item->guid; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_ALIAS'); ?>:
			<?php echo $this->item->alias; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_CREATED'); ?>:
			<?php echo $this->item->created; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_MODIFIED_BY'); ?>:
			<?php echo $this->item->modified_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_MODIFIED'); ?>:
			<?php echo $this->item->modified; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_STATE'); ?>:
			<?php echo $this->item->state; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_CHECKED_OUT'); ?>:
			<?php echo $this->item->checked_out; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_CHECKED_OUT_TIME'); ?>:
			<?php echo $this->item->checked_out_time; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_NAME'); ?>:
			<?php echo $this->item->name; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_SRS'); ?>:
			<?php echo $this->item->srs; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_UNIT_ID'); ?>:
			<?php echo $this->item->unit_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_CENTERCOORDINATES'); ?>:
			<?php echo $this->item->centercoordinates; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_MAXRESOLUTION'); ?>:
			<?php echo $this->item->maxresolution; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_MAXEXTENT'); ?>:
			<?php echo $this->item->maxextent; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_ABSTRACT'); ?>:
			<?php echo $this->item->abstract; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_ACCESS'); ?>:
			<?php echo $this->item->access; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_ASSET_ID'); ?>:
			<?php echo $this->item->asset_id; ?></li>


        </ul>
        
    </div>
    <?php if(JFactory::getUser()->authorise('core.edit.own', 'com_easysdi_map')): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=map.edit&id='.$this->item->id); ?>">Edit</a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_map')):
								?>
									<a href="javascript:document.getElementById('form-map-delete-<?php echo $this->item->id ?>').submit()">Delete</a>
									<form id="form-map-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=map.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
										<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
										<input type="hidden" name="jform[guid]" value="<?php echo $this->item->guid; ?>" />
										<input type="hidden" name="jform[alias]" value="<?php echo $this->item->alias; ?>" />
										<input type="hidden" name="jform[created]" value="<?php echo $this->item->created; ?>" />
										<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
										<input type="hidden" name="jform[modified_by]" value="<?php echo $this->item->modified_by; ?>" />
										<input type="hidden" name="jform[modified]" value="<?php echo $this->item->modified; ?>" />
										<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
										<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
										<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
										<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
										<input type="hidden" name="jform[name]" value="<?php echo $this->item->name; ?>" />
										<input type="hidden" name="jform[srs]" value="<?php echo $this->item->srs; ?>" />
										<input type="hidden" name="jform[unit_id]" value="<?php echo $this->item->unit_id; ?>" />
										<input type="hidden" name="jform[centercoordinates]" value="<?php echo $this->item->centercoordinates; ?>" />
										<input type="hidden" name="jform[maxresolution]" value="<?php echo $this->item->maxresolution; ?>" />
										<input type="hidden" name="jform[maxextent]" value="<?php echo $this->item->maxextent; ?>" />
										<input type="hidden" name="jform[abstract]" value="<?php echo $this->item->abstract; ?>" />
										<input type="hidden" name="jform[access]" value="<?php echo $this->item->access; ?>" />
										<input type="hidden" name="jform[asset_id]" value="<?php echo $this->item->asset_id; ?>" />
										<input type="hidden" name="option" value="com_easysdi_map" />
										<input type="hidden" name="task" value="map.remove" />
										<?php echo JHtml::_('form.token'); ?>
									</form>
								<?php
								endif;
							?>
<?php else: ?>
    Could not load the item
<?php endif; ?>
