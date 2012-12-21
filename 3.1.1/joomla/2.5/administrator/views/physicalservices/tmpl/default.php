<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_service/assets/css/easysdi_service.css');

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_easysdi_service');
$saveOrder	= $listOrder == 'a.ordering';

?>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&view=physicalservices'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_connector" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_EASYSDI_SERVICE_SELECT_CONNECTOR');?></option>
				<?php echo JHtml::_('select.options', $this->connectorlist, 'value', 'text', $this->state->get('filter.connector'));?>
			</select>
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>
			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_easysdi_service'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
            <select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>

				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_EASYSDI_SERVICE_SERVICES_ALIAS', 'a.alias', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_EASYSDI_SERVICE_SERVICES_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_EASYSDI_SERVICE_SERVICES_CONNECTOR', 'a.serviceconnector_value', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_EASYSDI_SERVICE_SERVICES_URL', 'a.resourceurl', $listDirn, $listOrder); ?>
				</th>
                <?php if (isset($this->items[0]->state)) { ?>
				<th width="5%">
					<?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
				</th>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				<th width="10%"><?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?> <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'physicalservices.saveorder'); ?>
					<?php endif; ?>
				</th>
				<?php } ?>
                <th width="10%">
					<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
				</th>
                <th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$canDo			= Easysdi_serviceHelper::getActions('physical',null,$item->id);
			$ordering		= ($listOrder == 'a.ordering');
			$canEdit 		= $canDo->get('core.edit');
			$canEditOwn 	= $canDo->get('core.edit.own');
			$canChange 		= $canDo->get('core.edit.state');
			$canCheckin		= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$islocked = ($item->serviceconnector_value == 'Bing' || $item->serviceconnector_value == 'Google' || $item->serviceconnector_value == 'OSM')? true : false;
			
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php // if ($canEdit || $canEditOwn) :  echo JHtml::_('grid.id', $i, $item->id); else : endif; ?>
					<?php if (!$islocked) echo JHtml::_('grid.id', $i, $item->id); else echo '<input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$item->id.'" disabled="false" onclick="Joomla.isChecked(this.checked);" />'  ?>
				</td>

				<td>
				<?php if (isset($item->checked_out) && $item->checked_out) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'physicalservices.', $canCheckin); ?>
				<?php endif; ?>
				<?php if (($canEdit || $canEditOwn) && !$islocked) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_easysdi_service&task=physicalservice.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->alias); ?></a>
				<?php else : ?>
					<?php echo $this->escape($item->alias); ?>
				<?php endif; ?>
				</td>
				<td>
					<?php echo $item->name; ?>
				</td>
				<td>
					<?php echo $item->serviceconnector_value; ?>
				</td>
				<td>
					<?php echo $item->resourceurl; ?>
				</td>
                <?php if (isset($this->items[0]->state)) { ?>
				    <td class="center">
					    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'physicalservices.', $canChange, 'cb'); ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				<td class="order"><?php if ($canChange) : ?> <?php if ($saveOrder) :?>
					<?php if ($listDirn == 'asc') : ?> <span><?php echo $this->pagination->orderUpIcon($i, true, 'physicalservices.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?>
				</span> <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'physicalservices.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?>
				</span> <?php elseif ($listDirn == 'desc') : ?> <span><?php echo $this->pagination->orderUpIcon($i, true, 'physicalservices.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?>
				</span> <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'physicalservices.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?>
				</span> <?php endif; ?> <?php endif; ?> <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5"
					value="<?php echo $item->ordering;?>" <?php echo $disabled ?>
					class="text-area-order" /> <?php else : ?> <?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<?php } ?>
                <td align="center">
					<?php echo $item->category_title; ?>
				</td>
                <td align="center">
					<?php echo $item->access_level; ?>
				</td>
               
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>