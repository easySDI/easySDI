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
			<?php
				$array = array();
				foreach((array)$this->item->group_id as $value): 
					if(!is_array($value)):
						$array[] = $value;
					endif;
				endforeach;
				$data = array();
				foreach($array as $value):
					$db = JFactory::getDbo();
					$query	= $db->getQuery(true);
					$query
						->select('name')
						->from('`#__sdi_layergroup`')
						->where('id = ' .$value);
					$db->setQuery($query);
					$results = $db->loadObjectList();
					$data[] = $results[0]->name;
				endforeach;
				$this->item->group_id = implode(',',$data); ?>
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
    <?php if(JFactory::getUser()->authorise('core.edit.own', 'com_easysdi_map')): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=layer.edit&id='.$this->item->id); ?>">Edit</a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_map')):
								?>
									<a href="javascript:document.getElementById('form-layer-delete-<?php echo $this->item->id ?>').submit()">Delete</a>
									<form id="form-layer-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=layer.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
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
										<input type="hidden" name="jform[group_id]" value="<?php echo $this->item->group_id; ?>" />
										<input type="hidden" name="jform[physicalservice_id]" value="<?php echo $this->item->physicalservice_id; ?>" />
										<input type="hidden" name="jform[virtualservice_id]" value="<?php echo $this->item->virtualservice_id; ?>" />
										<input type="hidden" name="jform[layername]" value="<?php echo $this->item->layername; ?>" />
										<input type="hidden" name="jform[istiled]" value="<?php echo $this->item->istiled; ?>" />
										<input type="hidden" name="jform[isdefaultvisible]" value="<?php echo $this->item->isdefaultvisible; ?>" />
										<input type="hidden" name="jform[opacity]" value="<?php echo $this->item->opacity; ?>" />
										<input type="hidden" name="jform[metadatalink]" value="<?php echo $this->item->metadatalink; ?>" />
										<input type="hidden" name="jform[access]" value="<?php echo $this->item->access; ?>" />
										<input type="hidden" name="jform[asset_id]" value="<?php echo $this->item->asset_id; ?>" />
										<input type="hidden" name="option" value="com_easysdi_map" />
										<input type="hidden" name="task" value="layer.remove" />
										<?php echo JHtml::_('form.token'); ?>
									</form>
								<?php
								endif;
							?>
<?php else: ?>
    Could not load the item
<?php endif; ?>
