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
JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
class HTML_package {
function listPackage(&$rows, &$pageNav, $option,  $filter_order_Dir, $filter_order, $use_pagination)
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
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderPackage' ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?>
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
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupPackage', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownPackage', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupPackage', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownPackage', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownPackage', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupPackage', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownPackage', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupPackage', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>
	            <?php $link =  "index.php?option=$option&amp;task=editPackage&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>
				<td><?php echo $row->description; ?></td>
				<td width="100px"><?php if ($row->updated and $row->updated<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td>
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
	  	<input type="hidden" name="task" value="listPackage" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
	}
	
	function editPackage(&$row, $profiles, $classes, $option, $rootId, $childClasses, $rootClasses, $fieldsLength, $languages, $labels)
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		global  $mainframe;
		
		$database =& JFactory::getDBO();
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td><?php echo JText::_("CORE_NAME"); ?></td>
					<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>" maxlength="<?php echo $fieldsLength['name'];?>"> </td>							
				</tr>
				<!-- 
				<tr>
					<td><?php echo JText::_("CORE_CODE"); ?></td>
					<td><input size="50" type="text" name ="code" value="<?php echo $row->code?>" maxlength="<?php echo $fieldsLength['code'];?>"> </td>							
				</tr>
				 -->
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?></td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description?></textarea></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_CSSLAYOUT"); ?></td>
					<td><input size="50" type="text" name ="csslayout" value="<?php echo $row->csslayout?>"> </td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_PROFILE"); ?></td>
					<script>
						var rootClassesValues = <?php echo HTML_metadata::array2json($rootClasses) ?>;
						var childClassesValues = <?php echo HTML_metadata::array2json($childClasses) ?>;
					</script>
					<td><?php echo JHTML::_("select.genericlist",$profiles, 'profile_id', 'size="1" class="inputbox" onchange="javascript:updateSelect2(childClassesValues,rootClassesValues,this.value);"', 'value', 'text', $row->profile_id) ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_CLASS"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$classes, 'class_id', 'size="1" class="inputbox"', 'value', 'text', $row->class_id ); ?></td>							
				</tr>
				
				<tr>
					<td colspan="2">
						<fieldset>
							<legend align="top"><?php echo JText::_("CORE_LABEL"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td><?php echo JText::_($lang->code); ?></td>
					<td><input size="50" type="text" name ="label<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($labels[$lang->id])?>" maxlength="<?php echo $fieldsLength['label'];?>"></td>							
					</tr>
<?php
}
?>
							</table>
						</fieldset>
					</td>
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
if ($row->updated)
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
			 
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="" />
		</form>
			<?php 	
	}
}
?>