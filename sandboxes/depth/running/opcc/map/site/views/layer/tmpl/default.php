<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2012. All rights reserved.
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

			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_GUID'); ?>:
			<?php echo $this->item->guid; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_ALIAS'); ?>:
			<?php echo $this->item->alias; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_CREATED'); ?>:
			<?php echo $this->item->created; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_MODIFIED_BY'); ?>:
			<?php echo $this->item->modified_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_MODIFIED'); ?>:
			<?php echo $this->item->modified; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_STATE'); ?>:
			<?php echo $this->item->state; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_CHECKED_OUT'); ?>:
			<?php echo $this->item->checked_out; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_CHECKED_OUT_TIME'); ?>:
			<?php echo $this->item->checked_out_time; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_NAME'); ?>:
			<?php echo $this->item->name; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_GROUP_ID'); ?>:
			<?php echo $this->item->group_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_PHYSICALSERVICE_ID'); ?>:
			<?php echo $this->item->physicalservice_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_VIRTUALSERVICE_ID'); ?>:
			<?php echo $this->item->virtualservice_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_LAYERNAME'); ?>:
			<?php echo $this->item->layername; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_ISTILED'); ?>:
			<?php echo $this->item->istiled; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_ISDEFAULTVISIBLE'); ?>:
			<?php echo $this->item->isdefaultvisible; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_OPACITY'); ?>:
			<?php echo $this->item->opacity; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_METADATALINK'); ?>:
			<?php echo $this->item->metadatalink; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_ACCESS'); ?>:
			<?php echo $this->item->access; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_LAYER_ASSET_ID'); ?>:
			<?php echo $this->item->asset_id; ?></li>


        </ul>
        
    </div>
    <?php if(JFactory::getUser()->authorise('core.edit', 'com_easysdi_map.layer'.$this->item->id)): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=layer.edit&id='.$this->item->id); ?>">Edit</a>
	<?php endif; ?>
<?php else: ?>
    Could not load the item
<?php endif; ?>
