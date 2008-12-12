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
	
	function listOrders($pageNav,$rows,$option,$orderstatus="",$ordertype="",$search="",$orderarchived=""){
	JToolBarHelper::title( JText::_("EASYSDI_LIST_ORDERS"), 'generic.png' );
		$database =& JFactory::getDBO();
	?>	
		
		<form action="index.php" method="GET" id="adminForm" name="adminForm">		
	
		<table width="100%" class="adminlist">
			<tr>
				<td align="left">
					<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" " />			
				</td>
				<td>
				<select name="ordertype" >
					<option value=""><?php echo JText::_("EASYSDI_CMD_FILTER_ALL"); ?></option>
					<option value="D" <?php if($ordertype=="D") echo "selected"; ?>><?php echo JText::_("EASYSDI_CMD_FILTER_D"); ?></option>
					<option value="O" <?php if($ordertype=="O") echo "selected"; ?>><?php echo JText::_("EASYSDI_CMD_FILTER_O"); ?></option>
				</select>
				</td>
				<td>
				<select name="orderstatus" >
					<option value=""><?php echo JText::_("EASYSDI_CMD_STATUS_FILTER_ALL"); ?></option>
					<option value="SAVED" <?php if($orderstatus=="SAVED") echo "selected"; ?>><?php echo JText::_("EASYSDI_CMD_STATUS_FILTER_SAVED"); ?></option>
					<option value="SENT" <?php if($orderstatus=="SENT") echo "selected"; ?>><?php echo JText::_("EASYSDI_CMD_STATUS_FILTER_SENT"); ?></option>
				</select>				
				</td>
				<td>
				<select name="orderarchived" >
					<option value=""><?php echo JText::_("EASYSDI_CMD_ARCHIVED_FILTER_ALL"); ?></option>
					<option value="1" <?php if($orderarchived=="1") echo "selected"; ?>><?php echo JText::_("EASYSDI_CMD_ARCHIVED_FILTER_YES"); ?></option>
					<option value="0" <?php if($orderarchived=="0") echo "selected"; ?>><?php echo JText::_("EASYSDI_CMD_ARCHIVED_FILTER_NO"); ?></option>
				</select>				
				</td>
				</tr>
		</table>
		
		<button type="submit" class="searchButton" > <?php echo JText::_("EASYSDI_SEARCH_BUTTON"); ?></button>
		<br>		
	<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>
	
	<?php JHTML::_("behavior.modal","a.modal",$param); ?>
	<table class="adminlist">
	<thead>
	<tr>
	<th><?php echo JText::_('EASYSDI_ORDER_SHARP'); ?></th>
	<th></th>
	<th><?php echo JText::_('EASYSDI_ORDER_NAME'); ?></th>
	<th><?php echo JText::_('EASYSDI_ORDER_TYPE'); ?></th>
	<th><?php echo JText::_('EASYSDI_ORDER_STATUS'); ?></th>
	<th><?php echo JText::_('EASYSDI_ORDER_USER'); ?></th>
	<th><?php echo JText::_('EASYSDI_ORDER_ARCHIVED'); ?></th>
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
			
			<td><span class="mdtitle" ><a class="modal" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->order_id?>" rel="{handler:'iframe',size:{x:500,y:500}}"> <?php echo $row->name; ?></a></span><br>
			
			<td><?php echo JText::_("EASYSDI_ORDER_TYPE_".$row->type) ;?></td>
			<td><?php echo JText::_("EASYSDI_ORDER_STATUS_".$row->status) ;?></td>
			<?php 
		
		
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND a.user_id=".$row->user_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>		
			
			<td><?php echo JText::_("EASYSDI_ORDER_ARCHIVED_".$row->archived) ;?></td>
			</tr>
				<?php		
		}
		
	?>
	</tbody>
	
	<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
	</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listOrders">
		</form>
		
	<?php	
	}
	
}
?>