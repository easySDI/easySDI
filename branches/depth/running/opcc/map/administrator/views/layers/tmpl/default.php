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

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_map/assets/css/easysdi_map.css');

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_easysdi_map');
$saveOrder	= $listOrder == 'a.ordering';
?>

<form
	action="<?php echo JRoute::_('index.php?option=com_easysdi_map&view=layers'); ?>"
	method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
			</label> <input type="text" name="filter_search" id="filter_search"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				title="<?php echo JText::_('Search'); ?>" />
			<button type="submit">
				<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
			</button>
			<button type="button"
				onclick="document.id('filter_search').value='';this.form.submit();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>


		<div class='filter-select fltrt'>
			<select name="filter_published" class="inputbox"
				onchange="this.form.submit()">
				<option value="">
					<?php echo JText::_('JOPTION_SELECT_PUBLISHED');?>
				</option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true);?>
			</select> <select name="filter_access" class="inputbox"
				onchange="this.form.submit()">
				<option value="">
					<?php echo JText::_('JOPTION_SELECT_ACCESS');?>
				</option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>
		</div>


	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%"><input type="checkbox" name="checkall-toggle"
					value="" onclick="checkAll(this)" />
				</th>

				<th class='left'><?php echo JHtml::_('grid.sort',  'COM_EASYSDI_MAP_LAYERS_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class='left'><?php echo JHtml::_('grid.sort',  'COM_EASYSDI_MAP_LAYERS_SERVICE_NAME', 'a.physicalservice_id', $listDirn, $listOrder); ?>
				</th>
				<th class='left'><?php echo JHtml::_('grid.sort',  'COM_EASYSDI_MAP_LAYERS_LAYERNAME', 'a.layername', $listDirn, $listOrder); ?>
				</th>
				<th class='left'><?php echo JHtml::_('grid.sort',  'COM_EASYSDI_MAP_LAYERS_GROUP_ID', 'a.group_id', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<?php if (isset($this->items[0]->state)) { ?>
				<th width="5%"><?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<?php } ?>
				<?php if (isset($this->items[0]->ordering)) { ?>
				<th width="10%"><?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?> <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'layers.saveorder'); ?>
					<?php endif; ?>
				</th>
				<?php } ?>
				<?php if (isset($this->items[0]->id)) { ?>
				<th width="1%" class="nowrap"><?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
				<?php } ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10"><?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'a.ordering');
			$canCreate	= $user->authorise('core.create',		'com_easysdi_map');
			$canEdit	= $user->authorise('core.edit',			'com_easysdi_map');
			$canCheckin	= $user->authorise('core.manage',		'com_easysdi_map');
			$canChange	= $user->authorise('core.edit.state',	'com_easysdi_map');
			
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>

				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'group.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit ) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_easysdi_map&task=layer.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->name); ?></a>
					<?php else : ?>
							<?php echo $this->escape($item->name); ?>
					<?php endif; ?>
					<p class="smallsub">
						<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
				</td>

				
				<td><?php if( $item->physicalservice_name ) echo $item->physicalservice_name; else echo $item->virtualservice_name; ?>
				</td>
				<td><?php echo $item->layername; ?>
				</td>
				<td><?php echo $item->groups_name_277305; ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<?php if (isset($this->items[0]->state)) { ?>
				<td class="center"><?php echo JHtml::_('jgrid.published', $item->state, $i, 'layers.', $canChange, 'cb'); ?>
				</td>
				<?php } ?>
				<?php if (isset($this->items[0]->ordering)) { ?>
				<td class="order"><?php if ($canChange) : ?> <?php if ($saveOrder) :?>
					<?php if ($listDirn == 'asc') : ?> <span><?php echo $this->pagination->orderUpIcon($i, true, 'layers.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?>
				</span> <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'layers.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?>
				</span> <?php elseif ($listDirn == 'desc') : ?> <span><?php echo $this->pagination->orderUpIcon($i, true, 'layers.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?>
				</span> <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'layers.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?>
				</span> <?php endif; ?> <?php endif; ?> <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5"
					value="<?php echo $item->ordering;?>" <?php echo $disabled ?>
					class="text-area-order" /> <?php else : ?> <?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<?php } ?>
				<?php if (isset($this->items[0]->id)) { ?>
				<td class="center"><?php echo (int) $item->id; ?>
				</td>
				<?php } ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" /> <input type="hidden"
			name="boxchecked" value="0" /> <input type="hidden"
			name="filter_order" value="<?php echo $listOrder; ?>" /> <input
			type="hidden" name="filter_order_Dir"
			value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
