<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/

// no direct access
defined('_JEXEC') or die;

$user=sdiFactory::getSdiUser();
if(!$user->isEasySDI) {
    return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();

$user	= JFactory::getUser();
$userId	= $user->get('id');
//$listOrder	= $this->state->get('list.ordering');
//$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_easysdi_processing');
$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_easysdi_processing&task=orders.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'orderList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar)) {
    $this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_easysdi_processing&view=orders'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>


		<div class="clearfix"> </div>
		<table class="table table-striped" id="orderList">
			<thead>
				<tr>
                <?php if (isset($this->items[0]->ordering)): ?>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
                <?php endif; ?>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
                <?php /*if (isset($this->items[0]->state)): ?>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
                <?php endif;*/?>

                <th class='left'><?php echo JHtml::_('grid.sort',  'COM_EASYSDI_PROCESSING_ORDERS_NAME', 'a.name', $listDirn, $listOrder); ?></th>
                <th class='left'><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDERS_PROCESSING'); ?></th>
                <th class='left'><?php echo JText::_('COM_EASYSDI_PROCESSING_ORDERS_STATUS'); ?></th>
                <th class='left'><?php echo JHtml::_('grid.sort',  'COM_EASYSDI_PROCESSING_ORDERS_USER', 'user', $listDirn, $listOrder); ?></th>
                <th class='left'><?php echo JHtml::_('grid.sort',  'COM_EASYSDI_PROCESSING_ORDERS_CREATED', 'a.created', $listDirn, $listOrder); ?></th>
                <th class='left'><?php echo JHtml::_('grid.sort',  'COM_EASYSDI_PROCESSING_ORDERS_COMPLETED', 'a.completed', $listDirn, $listOrder); ?></th>



                <?php
                /*?>

                <?php if (isset($this->items[0]->id)): ?>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
                <?php endif; ?>
				*/ ?>
				</tr>
			</thead>
			<tfoot>
                <?php
                if(isset($this->items[0])){
                    $colspan = count(get_object_vars($this->items[0]));
                }
                else{
                    $colspan = 10;
                }
            ?>

			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				$ordering   = ($listOrder == 'a.ordering');
                                $canCreate	= $user->authorise('core.create', 'com_easysdi_shop');
                                $canEdit	= $user->authorise('core.edit',	'com_easysdi_shop');
                                $canCheckin	= $user->authorise('core.manage', 'com_easysdi_shop');
                                $canChange	= $user->authorise('core.edit.state', 'com_easysdi_shop');
				?>
				<tr class="row<?php echo $i % 2; ?>">

                <?php if (isset($this->items[0]->ordering)): ?>
					<td class="order nowrap center hidden-phone">
					<?php if ($canChange) :
						$disableClassName = '';
						$disabledLabel	  = '';
						if (!$saveOrder) :
							$disabledLabel    = JText::_('JORDERINGDISABLED');
							$disableClassName = 'inactive tip-top';
						endif; ?>
						<span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
							<i class="icon-menu"></i>
						</span>
						<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
					<?php else : ?>
						<span class="sortable-handler inactive" >
							<i class="icon-menu"></i>
						</span>
					<?php endif; ?>
					</td>
                <?php endif; ?>
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
                <?php /* if (isset($this->items[0]->state)): ?>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', $item->state, $i, 'orders.', $canChange, 'cb'); ?>
					</td>
                <?php endif; */ ?>


                    <td>
                    <?php if (isset($item->checked_out) && $item->checked_out) : ?>
                    	<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'orders.', $canCheckin); ?>
                    <?php endif; ?>

                    <?php if ($canEdit) : ?>
                    	<a href="<?php echo JRoute::_('index.php?option=com_easysdi_processing&task=order.edit&id='.(int) $item->id); ?>">
                    	<?php echo $this->escape($item->name); ?></a>
                    <?php else : ?>
                    	<?php echo $this->escape($item->name); ?>
                    <?php endif; ?>
                    </td>
                    <td><?php
    	                echo JText::_($item->processing);
                    ?></td>
                    <td><?php
                    	echo Easysdi_processingStatusHelper::status($item->status)
                     ?></td>
                    <td><?php echo $item->user; ?></td>
                    <td><?php echo $item->created; ?></td>
                    <td><?php
                    	if ('0000-00-00 00:00:00'!=$item->completed)
                    		echo $item->completed;
                    	?></td>
                    <td><?php echo $item->products; ?></td>

               	</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php
 /*
    // DEBUG
    echo "<div style='position:absolute; left:5px; top:1500px'>";
    var_dump($this->items);
    echo "</div>";
*/
?>

</div>

