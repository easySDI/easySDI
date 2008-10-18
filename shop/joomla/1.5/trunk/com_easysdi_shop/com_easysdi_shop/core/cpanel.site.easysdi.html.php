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

class HTML_cpanel {
	
	function listOrders($pageNav,$rows,$option){
	
	?>	
		<div class="contentin">
		<form action="index.php" method="GET" id="ordersListForm" name="ordersListForm">
		<h2 class="contentheading"><?php echo JText::_("LIST_ORDERS"); ?></h2>
	
		<h3> <?php echo JText::_("SEARCH_CRITERIA_TITLE"); ?></h3>
	
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("FILTER");?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" " />			
				</td>
			</tr>
		</table>
		
		<button type="submit" class="searchButton" > <?php echo JText::_("SEARCH_BUTTON"); ?></button>
		<br>		
		<table width="100%">
			<tr>																																						
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td><td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("SEARCH_RESULTS_TITLE"); ?></h3>
	
	
	<table>
	<thead>
	<tr>
	<th><?php echo JText::_('ORDER_SHARP'); ?></th>
	<th></th>
	<th><?php echo JText::_('ORDER_NAME'); ?></th>
	<th><?php echo JText::_('ORDER_TYPE'); ?></th>
	<th><?php echo JText::_('ORDER_STATUS'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
		$i=0;
		foreach ($rows as $row)
		{	$i++;
			?>		
			<tr>
			<td><?php echo $i; ?></td>
			<td><input type="radio" name="order_id" value="<?php echo $row->order_id ;?>"></td>
			<td><?php echo $row->name ;?></td>
			<td><?php echo JText::_("ORDER_TYPE_".$row->type) ;?></td>
			<td><?php echo JText::_("ORDER_STATUS_".$row->status) ;?></td>
			</tr>
			
				<?php		
		}
		
	?>
	</tbody>
	</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listOrders">
			<button type="button" onClick="document.getElementById('task<?php echo $option; ?>').value='archiveOrder';document.getElementById('ordersListForm').submit();" ><?php echo JText::_("ARCHIVE_ORDER"); ?></button>
		</form>
		</div>
	<?php	
	}
	
}
?>