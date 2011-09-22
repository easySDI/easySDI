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


class HTML_searchcriteria {
	function listSearchCriteria(&$rows, $page, $filter_order_Dir, $filter_order, $context_id, $option)
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
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderSearchCriteria' ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<!-- <th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_SEARCHCRITERIA_OGCSEARCHFILTER"), 'ogcsearchfilter', @$filter_order_Dir, @$filter_order); ?></th> -->
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_SEARCHCRITERIA_CRITERIATYPE"), 'criteriatype_label', @$filter_order_Dir, @$filter_order); ?></th>
				<!-- <th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_SEARCHCRITERIA_SIMPLETAB"), 'simpletab', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_SEARCHCRITERIA_ADVANCEDTAB"), 'advancedtab', @$filter_order_Dir, @$filter_order); ?></th> -->
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_SEARCHCRITERIA_TAB"), 'tab_label', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$i=0;
		foreach ($rows as $row)
		{	
			// Gestion du nom
			/*if ($row->criteriatype_name=="system")
				$name = $row->name;
			else if ($row->criteriatype_name=="csw")
				$name = JText::_($row->csw_label);
			else	*/
				$name = $row->name;
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
							 <?php echo $page->orderUpIcon($i, true, 'orderupSearchCriteria', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownSearchCriteria', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupSearchCriteria', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownSearchCriteria', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupSearchCriteria', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownSearchCriteria', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupSearchCriteria', 'Move Up', isset($rows[$i-1]) ); ?>
		 		             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownSearchCriteria', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->cc_ordering;?>" class="text_area" style="text-align: center" />
	            </td>
<?php
if ($row->criteriatype_name <> "relation")
{ 
?>
				<?php $link =  "index.php?option=$option&amp;task=editSearchCriteria&context_id=$context_id&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $name; ?></a></td>
<?php 
}
else
{
?>
				<td><?php echo $name; ?></td>
<?php 
}?>
				<!-- <td><?php //echo $row->ogcsearchfilter; ?></td> -->
				<td align="center"><?php echo JText::_($row->criteriatype_label);?></td>
				<!-- <td width="100px" align="center">
					<?php 
						$imgY = 'tick.png';
						$imgX = 'publish_x.png';
						$img 	= $row->simpletab ? $imgY : $imgX;
						$prefix = "searchcriteria_simpletab_";
						$task 	= $row->simpletab ? 'unpublish' : 'publish';
						$alt = $row->simpletab ? JText::_( 'Yes' ) : JText::_( 'No' );		
					?>
					
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $prefix.$task;?>');">
						<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt;?>" />
					</a>
				</td>
				<td width="100px" align="center">
					<?php 
						$imgY = 'tick.png';
						$imgX = 'publish_x.png';
						$img 	= $row->advancedtab ? $imgY : $imgX;
						$prefix = "searchcriteria_advancedtab_";
						$task 	= $row->advancedtab ? 'unpublish' : 'publish';
						$alt = $row->advancedtab ? JText::_( 'Yes' ) : JText::_( 'No' );		
					?>
					
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $prefix.$task;?>');">
						<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt;?>" />
					</a>
				</td>-->
				<?php $tab 	= ADMIN_searchcriteria::tab($row, $i);?>
				<td width="100px" align="center">
					<?php echo $tab;?>
				</td>
				<td width="100px"><?php if ($row->updated and $row->updated<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td>
			</tr>
<?php
			$i ++;
		}
		
			?>
		</tbody>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $page->getListFooter(); ?></td>
		</tr>
		</tfoot>
		</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listSearchCriteria" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="context_id" value="<?php echo $context_id; ?>" />
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
	}
	
	function editSystemSearchCriteria($row, $tab, $selectedTab, $fieldsLength, $languages, $labels, $context_id, $tabList, $tab_id, $option)
	{
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 

		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td width=150><?php echo JText::_("CORE_NAME"); ?></td>
					<td width=150><?php echo $row->name; ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?></td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description?></textarea></td>							
				</tr>
				<!-- <tr>
					<td><?php echo JText::_("CATALOG_SEARCHCRITERIA_TAB"); ?></td>
					<td><?php echo JHTML::_('select.radiolist', $tab, 'tab', 'class="radio"', 'value', 'text', $selectedTab);?></td>							
				</tr>
				 -->
				<tr>
					<td><?php echo JText::_("CATALOG_SEARCHCRITERIA_TAB"); ?></td>
					<td><?php echo JHTML::_('select.genericlist', $tabList, 'tabList', 'class="list"', 'value', 'text', $tab_id);?></td>							
				</tr>
			</table>
			<table border="0" cellpadding="3" cellspacing="0">
					<tr>
					<td colspan="2">
						<fieldset id="labels">
							<legend align="top"><?php echo JText::_("CORE_LABEL"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
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
			<input type="hidden" name="context_id" value="<?php echo $context_id?>" />
			<input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo ($row->createdby)? $row->createdby : $user->id; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo ($row->createdby)? $user->id : ''; ?>" /> 
			<input type="hidden" name="name" value="<?php echo $row->name?>" />
			<input type="hidden" name="code" value="<?php echo $row->code?>" />
			<input type="hidden" name="criteriatype_id" value="<?php echo $row->criteriatype_id?>" />
			<input type="hidden" name="label" value="<?php echo $row->label; ?>" />
			
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="" />
		</form>
			<?php 	
	}		

	
	function editOGCSearchCriteria($row, $tab, $selectedTab, $fieldsLength, $languages, $labels, $filterfields, $context_id, $tabList, $tab_id, $rendertypes, $option)
	{
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 

		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td width=150><?php echo JText::_("CORE_NAME"); ?></td>
					<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>" maxlength="<?php echo $fieldsLength['name'];?>"> </td>							
				</tr>
				<!-- <tr>
					<td><?php //echo JText::_("CATALOG_SEARCHCRITERIA_OGCSEARCHFILTER"); ?></td>
					<td><input size="<?php //echo $fieldsLength['ogcsearchfilter'];?>" type="text" name ="ogcsearchfilter" value="<?php //echo $row->ogcsearchfilter?>" maxlength="<?php //echo $fieldsLength['ogcsearchfilter'];?>"> </td>							
				</tr>
				 -->
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?></td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description?></textarea></td>							
				</tr>
				<!-- <tr>
					<td><?php echo JText::_("CATALOG_SEARCHCRITERIA_TAB"); ?></td>
					<td><?php echo JHTML::_('select.radiolist', $tab, 'tab', 'class="radio"', 'value', 'text', $selectedTab);?></td>							
				</tr>
				-->
				<tr>
					<td><?php echo JText::_("CATALOG_SEARCHCRITERIA_TAB"); ?></td>
					<td><?php echo JHTML::_('select.genericlist', $tabList, 'tabList', 'class="list"', 'value', 'text', $tab_id);?></td>							
				</tr>
				<tr>
					<td width=150 ><?php echo JText::_("CATALOG_RENDERTYPE"); ?></td>
					<?php 
						$selectedRendertype = $row->rendertype_id;
					?>
					<td><?php echo JHTML::_("select.genericlist",$rendertypes, 'rendertype_id', 'size="1" class="inputbox" onchange="javascript:changeDefaultField(this.value);"', 'value', 'text', $selectedRendertype ); ?></td>							
				</tr>
			</table>
			<table border="0" cellpadding="3" cellspacing="0">
					<tr>
					<td colspan="2">
						<fieldset id="filterfields">
							<legend align="top"><?php echo JText::_("CATALOG_CONTEXT_FILTERFIELD"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="filterfield<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($filterfields[$lang->id])?>" maxlength="<?php echo $fieldsLength['ogcsearchfilter'];?>"></td>							
					</tr>
<?php
}
?>
							</table>
						</fieldset>
					</td>
				</tr>
			</table>
			<table border="0" cellpadding="3" cellspacing="0">
					<tr>
					<td colspan="2">
						<fieldset id="labels">
							<legend align="top"><?php echo JText::_("CORE_LABEL"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
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
			<input type="hidden" name="context_id" value="<?php echo $context_id?>" />
			<input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo ($row->createdby)? $row->createdby : $user->id; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo ($row->createdby)? $user->id : ''; ?>" /> 
			<input type="hidden" name="criteriatype_id" value="<?php echo $row->criteriatype_id?>" />
			<input type="hidden" name="label" value="<?php echo $row->label; ?>" />
			
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="" />
		</form>
			<?php 	
	}
}
?>