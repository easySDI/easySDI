<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */

defined('_JEXEC') or die('Restricted access');


class HTML_model {
function listModel(&$rows, &$pageNav, $option,  $filter_order_Dir, $filter_order, $use_pagination)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_MODEL"));
		$database =& JFactory::getDBO();
		$ordering = ($filter_order == 'ordering');
		
?>
	<form action="index.php" method="GET" name="adminForm">
		<table class="adminlist">
		<thead>
			<tr>
				<th class='title' width="10px"><?php echo JText::_("EASYSDI_SHARP"); ?></th>
				<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveModel' ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_NAME"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROFILE"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
			</tr>
		</thead>
		<tbody>		
<?php
		$i=0;
		foreach ($rows as $row)
		{		
?>
			<tr>
				<td align="center" width="10px"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>												
				<td width="30px" align="center"><?php echo $row->id; ?></td>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupModel', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownModel', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupModel', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownModel', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownModel', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupModel', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownModel', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupModel', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>
	            <?php $link =  "index.php?option=$option&amp;task=editModel&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>
				<td><?php echo $row->profile_id; ?></td>
			</tr>
<?php
			$i ++;
		}
		
			?></tbody>
			
		<?php			
		
		if ($use_pagination)
		{?>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
		<?php
		}
?>
	  	</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listModel" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
	}
	
	function editModel(&$row, $profiles, $option)
	{
		global  $mainframe;
		
		JToolBarHelper::title(JText::_("EASYSDI_EDIT_MODEL"));
		
		$database =& JFactory::getDBO(); 

		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td><?php echo JText::_("EASYSDI_NAME"); ?></td>
					<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>"> </td>							
				</tr>
				<tr>
					<td><?php echo JText::_("EASYSDI_CODE"); ?></td>
					<td><input size="50" type="text" name ="code" value="<?php echo $row->code?>"> </td>							
				</tr>
				<tr>
					<td><?php echo JText::_("EASYSDI_DESCRIPTION"); ?></td>
					<td><input size="50" type="text" name ="description" value="<?php echo $row->description?>"> </td>							
				</tr>
				<tr>
					<td><?php echo JText::_("EASYSDI_LABEL"); ?></td>
					<td><input size="50" type="text" name ="label" value="<?php echo $row->label?>"> </td>							
				</tr>		
				<tr>
					<td><?php echo JText::_("EASYSDI_ISSYSTEM"); ?></td>
					<td><input size="50" type="checkbox" name ="issystem" value="<?php echo $row->issystem?>"> </td>							
				</tr>
				<tr>
					<td><?php echo JText::_("EASYSDI_ISEXTENSIBLE"); ?></td>
					<td><input size="50" type="checkbox" name ="isextensible" value="<?php echo $row->isextensible?>"> </td>							
				</tr>		
				<tr>
					<td><?php echo JText::_("EASYSDI_PROFILE"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$profiles, 'profile_id', 'size="1" class="inputbox"', 'value', JText::_('label'), $row->profile_id ); ?></td>							
				</tr>				
			</table>
			 
			 
			 
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="" />
		</form>
			<?php 	
	}
}
?>