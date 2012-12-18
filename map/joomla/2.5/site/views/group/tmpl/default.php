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

			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_GUID'); ?>:
			<?php echo $this->item->guid; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_ALIAS'); ?>:
			<?php echo $this->item->alias; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_CREATED'); ?>:
			<?php echo $this->item->created; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_MODIFIED_BY'); ?>:
			<?php echo $this->item->modified_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_MODIFIED'); ?>:
			<?php echo $this->item->modified; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_STATE'); ?>:
			<?php echo $this->item->state; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_CHECKED_OUT'); ?>:
			<?php echo $this->item->checked_out; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_CHECKED_OUT_TIME'); ?>:
			<?php echo $this->item->checked_out_time; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_NAME'); ?>:
			<?php echo $this->item->name; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_ISDEFAULTOPEN'); ?>:
			<?php echo $this->item->isdefaultopen; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_ACCESS'); ?>:
			<?php echo $this->item->access; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL_GROUP_ASSET_ID'); ?>:
			<?php echo $this->item->asset_id; ?></li>


        </ul>
        
    </div>
    <?php if(JFactory::getUser()->authorise('core.edit', 'com_easysdi_map.group'.$this->item->id)): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=group.edit&id='.$this->item->id); ?>">Edit</a>
	<?php endif; ?>
<?php else: ?>
    Could not load the item
<?php endif; ?>
