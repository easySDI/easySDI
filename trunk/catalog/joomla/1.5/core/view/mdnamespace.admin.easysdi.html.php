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


class HTML_mdnamespace {
function listMDNamespace(&$rows, $page, $option,  $filter_order_Dir, $filter_order)
	{
		$database =& JFactory::getDBO();
		
		$ordering = ($filter_order == 'ordering');
?>
	<form action="index.php" method="POST" name="adminForm">
		<table class="adminlist">
		<thead>
			<tr>
				<th class='title' width="10px"><?php echo JText::_("CORE_SHARP"); ?></th>
				<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_NAMESPACE_PREFIX"), 'prefix', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_NAMESPACE_URI"), 'uri', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_ISSYSTEM"), 'system', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$i=0;
		foreach ($rows as $row)
		{		
?>
			<tr>
				<td align="center" width="10px"><?php echo $page->getRowOffset( $i );//echo $i+$page->limitstart+1;?></td>
				<td width="10px"><?php if(!$row->system){?><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /><?php }?></td>												
				<td width="30px" align="center"><?php echo $row->id; ?></td>
				<?php $link =  "index.php?option=$option&amp;task=editMDNamespace&cid[]=$row->id"; ?>
				<td><?php if(!$row->system){?> <a href="<?php echo $link;?>"> <?php }?><?php echo $row->prefix; ?><?php if(!$row->system){?></a><?php }?></td>
				<td><?php echo $row->uri; ?></td>
				<td width="100px" align="center">
					<?php 
						$imgY = 'tick.png';
						$imgX = 'publish_x.png';
						$img 	= $row->system ? $imgY : $imgX;
						$prefix = "namespace_issystem_";
						$task 	= $row->system ? 'unpublish' : 'publish';
						$alt = $row->system ? JText::_( 'Yes' ) : JText::_( 'No' );		
					?>
<!--					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $prefix.$task;?>');">
						<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt;?>" />
 					</a> -->
					<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt;?>" />
				</td>
				<td width="100px"><?php if ($row->modified and $row->modified<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->modified));} ?></td>
			</tr>
<?php
			$i ++;
		}
		
			?>
		</tbody>
		<tfoot>
		<tr>	
		<td colspan="10"><?php echo $page->getListFooter(); ?></td>
		</tr>
		</tfoot>
		</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listMDNamespace" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
	}
	
	function editMDNamespace(&$row, $fieldsLength, $option)
	{
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 

		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table class="admintable" border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td class="key"><?php echo JText::_("CATALOG_NAMESPACE_PREFIX"); ?></td>
					<td><input size="50" type="text" name ="prefix" value="<?php echo $row->prefix?>" maxlength="<?php echo $fieldsLength['prefix'];?>"> </td>							
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("CATALOG_NAMESPACE_URI"); ?></td>
					<td><input size="50" type="text" name ="uri" value="<?php echo $row->uri?>" maxlength="<?php echo $fieldsLength['uri'];?>"> </td>							
				</tr>
			</table>
			<br></br>
			<table border="0" cellpadding="3" cellspacing="0">
<?php
$user =& JFactory::getUser();
if ($row->created)
{ 
?>
				<tr>
					<td><?php echo JText::_("CORE_CREATED"); ?> : </td>
					<td><?php if ($row->created) {echo date('d.m.Y h:i:s',strtotime($row->created));} ?></td>
					<td>, </td>
					<?php
						if ($row->created_by and $row->created_by<> 0)
						{
							$query = "SELECT name FROM #__users WHERE id=".$row->created_by ;
							$database->setQuery($query);
							$createUser = $database->loadResult();
						}
						else
							$createUser = "";
					?>
					<td><?php echo $createUser; ?></td>
				</tr>
<?php
}
if ($row->modified and $row->modified<> '0000-00-00 00:00:00')
{ 
?>
				<tr>
					<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
					<td><?php if ($row->modified and $row->modified<> 0) {echo date('d.m.Y h:i:s',strtotime($row->modified));} ?></td>
					<td>, </td>
					<?php
						if ($row->modified_by and $row->modified_by<> 0)
						{
							$query = "SELECT name FROM #__users WHERE id=".$row->modified_by ;
							$database->setQuery($query);
							$updateUser = $database->loadResult();
						}
						else
							$updateUser = "";
					?>
					<td><?php echo $updateUser; ?></td>
				</tr>
<?php
}
?>
			</table> 
			 
			<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
			<input type="hidden" name="guid" value="<?php echo $row->guid?>" />
			<input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo ($row->created_by)? $row->created_by : $user->id; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo ($row->created_by)? $user->id : ''; ?>" /> 
			<input type="hidden" name="system" value="<?php echo $row->system; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="" />
		</form>
			<?php 	
	}
}
?>