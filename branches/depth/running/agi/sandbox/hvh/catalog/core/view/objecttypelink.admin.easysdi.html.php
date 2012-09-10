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


class HTML_objecttypelink {
function listObjectTypeLink(&$rows, $page, $option,  $filter_order_Dir, $filter_order)
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
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderObjectTypeLink' ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_OBJECTTYPELINK_PARENT"), 'parent_name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_OBJECTTYPELINK_CHILD"), 'child_name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_OBJECTTYPELINK_FLOWDOWNVERSIONING"), 'xslfile', @$filter_order_Dir, @$filter_order); ?></th>
				<!-- <th class='title'><?php //echo JHTML::_('grid.sort',   JText::_("CATALOG_OBJECTTYPELINK_ESCALATEVERSIONINGUPDATE"), 'url', @$filter_order_Dir, @$filter_order); ?></th> -->
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
				<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>												
				<td width="30px" align="center"><?php echo $row->id; ?></td>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupObjectTypeLink', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownObjectTypeLink', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupObjectTypeLink', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownObjectTypeLink', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownObjectTypeLink', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupObjectTypeLink', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownObjectTypeLink', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupObjectTypeLink', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>
				 <?php $link =  "index.php?option=$option&amp;task=editObjectTypeLink&cid[]=$row->id";?>
				<td><?php echo $row->parent_name; ?></td>
				<td><?php echo $row->child_name; ?></td>
				<td width="100px" align="center">
					<?php 
						$imgY = 'tick.png';
						$imgX = 'publish_x.png';
						$img 	= $row->flowdown_versioning ? $imgY : $imgX;
						$prefix = "objecttypelink_flowdown_versioning_";
						$task 	= $row->flowdown_versioning ? 'unpublish' : 'publish';
						$alt = $row->flowdown_versioning ? JText::_( 'Yes' ) : JText::_( 'No' );		
					?>
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $prefix.$task;?>');">
						<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt;?>" />
					</a>
				</td>
				<!-- <td width="100px" align="center">
					<?php 
						/*$imgY = 'tick.png';
						$imgX = 'publish_x.png';
						$img 	= $row->escalate_versioning_update ? $imgY : $imgX;
						$prefix = "objecttypelink_escalate_versioning_update_";
						$task 	= $row->escalate_versioning_update ? 'unpublish' : 'publish';
						$alt = $row->escalate_versioning_update ? JText::_( 'Yes' ) : JText::_( 'No' );*/		
					?>
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php //echo $i;?>','<?php //echo $prefix.$task;?>');">
						<img src="images/<?php //echo $img;?>" width="16" height="16" border="0" alt="<?php //echo $alt;?>" />
					</a>
				</td>
				 -->
				<td width="100px"><?php if ($row->updated and $row->updated<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td>
			</tr>
<?php
			$i ++;
		}
		
			?>
		</tbody>
		<tfoot>
		<tr>	
		<td colspan="9"><?php echo $page->getListFooter(); ?></td>
		</tr>
		</tfoot>
		</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listObjectTypeLink" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
	}
	
	function editObjectTypeLink(&$row, $fieldsLength, $objecttypes, $classes, $attributes, $style, $pageReloaded, $option)
	{
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 

		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
		<script>
		function addXPath()
		{
			var tr = document.createElement('tr');	
			var tdParam = document.createElement('td');	
			var inputParam = document.createElement('input');
			inputParam.size=250;
			inputParam.type="text";
			inputParam.name="xpath_"+document.getElementById('nbxpath').value;
			tdParam.appendChild(inputParam);
			tr.appendChild(tdParam);
			document.getElementById('inheritancexpathtable').appendChild(tr);
			document.getElementById('nbxpath').value = parseInt(document.getElementById('nbxpath').value) + 1 ;
		}
		function enableXPath(value)
		{
			if(value == 0){
				document.getElementById('inheritance').style.display = 'none';
			}
			else{
				document.getElementById('inheritance').style.display = 'block';
			}
		}
		</script>
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td width=150><?php echo JText::_("CATALOG_OBJECTTYPELINK_PARENT"); ?></td>
					<?php if ($pageReloaded) $parent_objecttype = $_POST['parent_id']; else $parent_objecttype = $row->parent_id; ?>
					<td><?php echo JHTML::_("select.genericlist",$objecttypes, 'parent_id', 'size="1" class="inputbox"', 'value', 'text', $parent_objecttype); ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_OBJECTTYPELINK_CHILD"); ?></td>
					<?php if ($pageReloaded) $child_objecttype = $_POST['child_id']; else $child_objecttype = $row->child_id; ?>
					<td><?php echo JHTML::_("select.genericlist",$objecttypes, 'child_id', 'size="1" class="inputbox"', 'value', 'text', $child_objecttype); ?></td>							
				</tr>
			</table>
			<div id = "div_xpathParentId" style="<?php echo $style; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td width=150><?php echo JText::_("CATALOG_OBJECTTYPELINK_PARENTMETADATA_ROOTCLASS"); ?></td>
					<?php if ($pageReloaded) $classid = $_POST['class_id']; else $classid = $row->class_id; ?>
					<td><?php echo JHTML::_("select.genericlist",$classes, 'class_id', 'size="1" class="inputbox" onchange="javascript:submitform(\'editObjectTypeLink\');"', 'value', 'text', $classid ); ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_OBJECTTYPELINK_PARENTMETADATA_GUID"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$attributes, 'attribute_id', 'size="1" class="inputbox"', 'value', 'text', $row->attribute_id ); ?></td>							
				</tr>
			</table>
			</div>
			<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td width=150><?php echo JText::_("CATALOG_OBJECTTYPELINK_FLOWDOWNVERSIONING_LABEL"); ?></td>
					<?php if ($pageReloaded) $flowdown_versioning = $_POST['flowdown_versioning']; else $flowdown_versioning = $row->flowdown_versioning; ?>
					<td><?php echo JHTML::_('select.booleanlist', 'flowdown_versioning', '', $flowdown_versioning); ?> </td>							
				</tr>
				<tr>
					<td colspan="2">
						<fieldset id="parent_bounds">
							<legend align="top"><?php echo JText::_("CATALOG_OBJECTTYPELINK_PARENTBOUNDS"); ?></legend>
							<table>
								<tr>
									<td width=140><?php echo JText::_("CATALOG_OBJECTTYPELINK_LOWER"); ?> : </td>
									<td><input size="5" type="text" name ="parentbound_lower" value="<?php if ($pageReloaded) echo $_POST['parentbound_lower']; else echo $row->parentbound_lower;?>"></td>							
								</tr>
								<tr>
									<td width=140><?php echo JText::_("CATALOG_OBJECTTYPELINK_UPPER"); ?> : </td>
									<td><input size="5" type="text" name ="parentbound_upper" value="<?php if ($pageReloaded) echo $_POST['parentbound_upper']; else echo $row->parentbound_upper;?>" onChange="javascript:changexpathParentIdVisibility(this.value);"></td>							
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<fieldset id="child_bounds">
							<legend align="top"><?php echo JText::_("CATALOG_OBJECTTYPELINK_CHILDBOUNDS"); ?></legend>
							<table>
								<tr>
									<td width=140><?php echo JText::_("CATALOG_OBJECTTYPELINK_LOWER"); ?> : </td>
									<td><input size="5" type="text" name ="childbound_lower" value="<?php if ($pageReloaded) echo $_POST['childbound_lower']; else echo $row->childbound_lower;?>"></td>							
								</tr>
								<tr>
									<td width=140><?php echo JText::_("CATALOG_OBJECTTYPELINK_UPPER"); ?> : </td>
									<td><input size="5" type="text" name ="childbound_upper" value="<?php if ($pageReloaded) echo $_POST['childbound_upper']; else echo $row->childbound_upper;?>"></td>							
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>	
			</table>
			<fieldset>
					<legend align="top"><?php echo JText::_("CATALOG_OBJECTTYPELINK_INHERITANCE"); ?>
						
					</legend>
				<table>
					<tr>
						<td width=150><?php echo JText::_("CATALOG_OBJECTTYPELINK_INHERITANCE_LABEL"); ?></td>
						<?php if ($pageReloaded) $inheritance = $_POST['inheritance']; else $inheritance = $row->inheritance; ?>
						<td><?php echo JHTML::_('select.booleanlist', 'inheritance', 'onclick="enableXPath(this.value)"', $inheritance); ?> </td>							
					</tr>
					
				</table>
				<div id="inheritance" style="<?php if($inheritance == 0) echo 'display:none'; else echo 'display:block';?>">
					<table  class="admintable">
						<thead>
							<tr>
								<th>
								<table>
								<tr><td>
								<b><?php echo JText::_( 'CATALOG_OBJECTTYPELINK_INHERITANCE_XPATH'); ?></b>
								</td>								
								<td><div title="<?php echo JText::_( 'CATALOG_OBJECTTYPELINK_INHERITANCE_ADD_XPATH');?>" class="fieldset-add-icon" onClick="addXPath();"></div>
								</td></tr>
								</table>
								</th>		
							</tr>
						</thead>
						<tbody id="inheritancexpathtable">
							<?php 
								$i = 0;
								$xpathlist = $row->getXPath();
								foreach ($xpathlist as $xpath)
								{			
									?>
									<tr>
										<td><input name="xpath_<?php echo $i;?>" type="text" class="text_area" size="250" value='<?php echo $xpath->xpath; ?>'></td>
									</tr>
									<?php 
									$i  ++;
								} 
							?>
							
						</tbody>
					</table>
				</div>
			</fieldset>
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
						if ($row->createdby and $row->createdby<> 0)
						{
							$query = "SELECT name FROM #__users WHERE id=".$row->createdby ;
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
if ($row->updated and $row->updated<> '0000-00-00 00:00:00')
{ 
?>
				<tr>
					<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
					<td><?php if ($row->updated and $row->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td>
					<td>, </td>
					<?php
						if ($row->updatedby and $row->updatedby<> 0)
						{
							$query = "SELECT name FROM #__users WHERE id=".$row->updatedby ;
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
			<input type="hidden" name="createdby" value="<?php echo ($row->createdby)? $row->createdby : $user->id; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo ($row->createdby)? $user->id : ''; ?>" /> 
			<input type="hidden" name="nbxpath" id="nbxpath" value="<?php echo $i;?>">
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="" />
		</form>
			<?php 	
	}
}
?>