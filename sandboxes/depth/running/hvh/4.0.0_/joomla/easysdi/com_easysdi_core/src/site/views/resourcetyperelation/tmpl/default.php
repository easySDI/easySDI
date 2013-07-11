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

            			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_GUID'); ?>:
			<?php echo $this->item->guid; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_ALIAS'); ?>:
			<?php echo $this->item->alias; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_CREATED_BY'); ?>:
			<?php echo $this->item->created_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_CREATED'); ?>:
			<?php echo $this->item->created; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_MODIFIED_BY'); ?>:
			<?php echo $this->item->modified_by; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_MODIFIED'); ?>:
			<?php echo $this->item->modified; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_ORDERING'); ?>:
			<?php echo $this->item->ordering; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_STATE'); ?>:
			<?php echo $this->item->state; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_CHECKED_OUT'); ?>:
			<?php echo $this->item->checked_out; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_CHECKED_OUT_TIME'); ?>:
			<?php echo $this->item->checked_out_time; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_PARENT_ID'); ?>:
			<?php
				if($this->item->parent_id != ''):
					$array = array();
					foreach((array)$this->item->parent_id as $value): 
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
							->from('`#__sdi_resource`')
							->where('id = ' .$value);
						$db->setQuery($query);
						$results = $db->loadObjectList();
						if($results){
							$data[] = $results[0]->name;
						}
					endforeach;
					$this->item->parent_id = implode(',',$data);
				endif; ?>			<?php echo $this->item->parent_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_CHILD_ID'); ?>:
			<?php
				if($this->item->child_id != ''):
					$array = array();
					foreach((array)$this->item->child_id as $value): 
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
							->from('`#__sdi_resource`')
							->where('id = ' .$value);
						$db->setQuery($query);
						$results = $db->loadObjectList();
						if($results){
							$data[] = $results[0]->name;
						}
					endforeach;
					$this->item->child_id = implode(',',$data);
				endif; ?>			<?php echo $this->item->child_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_PARENTBOUNDLOWER'); ?>:
			<?php echo $this->item->parentboundlower; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_PARENTBOUNDUPPER'); ?>:
			<?php echo $this->item->parentboundupper; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_CHILDBOUNDLOWER'); ?>:
			<?php echo $this->item->childboundlower; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_CHILDBOUNDUPPER'); ?>:
			<?php echo $this->item->childboundupper; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_CLASS_ID'); ?>:
			<?php echo $this->item->class_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_ATTRIBUTE_ID'); ?>:
			<?php echo $this->item->attribute_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_VIRALVERSIONING'); ?>:
			<?php echo $this->item->viralversioning; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATION_INHERITANCE'); ?>:
			<?php echo $this->item->inheritance; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourcetyperelation.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_EASYSDI_CORE_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_core.resourcetyperelation.'.$this->item->id)):
								?>
									<a href="javascript:document.getElementById('form-resourcetyperelation-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_EASYSDI_CORE_DELETE_ITEM"); ?></a>
									<form id="form-resourcetyperelation-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourcetyperelation.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
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
										<input type="hidden" name="jform[parent_id]" value="<?php echo $this->item->parent_id; ?>" />
										<input type="hidden" name="jform[child_id]" value="<?php echo $this->item->child_id; ?>" />
										<input type="hidden" name="jform[parentboundlower]" value="<?php echo $this->item->parentboundlower; ?>" />
										<input type="hidden" name="jform[parentboundupper]" value="<?php echo $this->item->parentboundupper; ?>" />
										<input type="hidden" name="jform[childboundlower]" value="<?php echo $this->item->childboundlower; ?>" />
										<input type="hidden" name="jform[childboundupper]" value="<?php echo $this->item->childboundupper; ?>" />
										<input type="hidden" name="jform[class_id]" value="<?php echo $this->item->class_id; ?>" />
										<input type="hidden" name="jform[attribute_id]" value="<?php echo $this->item->attribute_id; ?>" />
										<input type="hidden" name="jform[viralversioning]" value="<?php echo $this->item->viralversioning; ?>" />
										<input type="hidden" name="jform[inheritance]" value="<?php echo $this->item->inheritance; ?>" />
										<input type="hidden" name="option" value="com_easysdi_core" />
										<input type="hidden" name="task" value="resourcetyperelation.remove" />
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
