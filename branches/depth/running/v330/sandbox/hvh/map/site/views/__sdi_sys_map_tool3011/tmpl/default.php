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

			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL___SDI_SYS_MAP_TOOL3011_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL___SDI_SYS_MAP_TOOL3011_ALIAS'); ?>:
			<?php echo $this->item->alias; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL___SDI_SYS_MAP_TOOL3011_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL___SDI_SYS_MAP_TOOL3011_STATE'); ?>:
			<?php echo $this->item->state; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL___SDI_SYS_MAP_TOOL3011_CHECKED_OUT'); ?>:
			<?php echo $this->item->checked_out; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL___SDI_SYS_MAP_TOOL3011_CHECKED_OUT_TIME'); ?>:
			<?php echo $this->item->checked_out_time; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL___SDI_SYS_MAP_TOOL3011_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_MAP_FORM_LBL___SDI_SYS_MAP_TOOL3011_NAME'); ?>:
			<?php echo $this->item->name; ?></li>


        </ul>
        
    </div>
    <?php if(JFactory::getUser()->authorise('core.edit.own', 'com_easysdi_map')): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=__sdi_sys_map_tool3011.edit&id='.$this->item->id); ?>">Edit</a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_map')):
								?>
									<a href="javascript:document.getElementById('form-__sdi_sys_map_tool3011-delete-<?php echo $this->item->id ?>').submit()">Delete</a>
									<form id="form-__sdi_sys_map_tool3011-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=__sdi_sys_map_tool3011.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
										<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
										<input type="hidden" name="jform[alias]" value="<?php echo $this->item->alias; ?>" />
										<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
										<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
										<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
										<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
										<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
										<input type="hidden" name="jform[name]" value="<?php echo $this->item->name; ?>" />
										<input type="hidden" name="option" value="com_easysdi_map" />
										<input type="hidden" name="task" value="__sdi_sys_map_tool3011.remove" />
										<?php echo JHtml::_('form.token'); ?>
									</form>
								<?php
								endif;
							?>
<?php else: ?>
    Could not load the item
<?php endif; ?>
