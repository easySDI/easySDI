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

            			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATIONINHERITANCE_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATIONINHERITANCE_RESOURCETYPERELATION_ID'); ?>:
			<?php
				if($this->item->resourcetyperelation_id != ''):
					$array = array();
					foreach((array)$this->item->resourcetyperelation_id as $value): 
						if(!is_array($value)):
							$array[] = $value;
						endif;
					endforeach;
					$data = array();
					foreach($array as $value):
						$db = JFactory::getDbo();
						$query	= $db->getQuery(true);
						$query
							->select('alias')
							->from('`#__sdi_resourcetypelink`')
							->where('id = ' .$value);
						$db->setQuery($query);
						$results = $db->loadObjectList();
						if($results){
							$data[] = $results[0]->alias;
						}
					endforeach;
					$this->item->resourcetyperelation_id = implode(',',$data);
				endif; ?>			<?php echo $this->item->resourcetyperelation_id; ?></li>
			<li><?php echo JText::_('COM_EASYSDI_CORE_FORM_LBL_RESOURCETYPERELATIONINHERITANCE_XPATH'); ?>:
			<?php echo $this->item->xpath; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourcetyperelationinheritance.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_EASYSDI_CORE_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_easysdi_core.resourcetyperelationinheritance.'.$this->item->id)):
								?>
									<a href="javascript:document.getElementById('form-resourcetyperelationinheritance-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_EASYSDI_CORE_DELETE_ITEM"); ?></a>
									<form id="form-resourcetyperelationinheritance-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_easysdi_core&task=resourcetyperelationinheritance.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
										<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
										<input type="hidden" name="jform[resourcetyperelation_id]" value="<?php echo $this->item->resourcetyperelation_id; ?>" />
										<input type="hidden" name="jform[xpath]" value="<?php echo $this->item->xpath; ?>" />
										<input type="hidden" name="option" value="com_easysdi_core" />
										<input type="hidden" name="task" value="resourcetyperelationinheritance.remove" />
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
