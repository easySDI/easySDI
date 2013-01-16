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


class HTML_context {
function listContext(&$rows, $page, $filter_order_Dir, $filter_order, $option)
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
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderContext' ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_CODE"), 'code', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JText::_("CATALOG_CONTEXT_SEARCHCRITERIA"); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
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
							 <?php echo $page->orderUpIcon($i, true, 'orderupContext', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownContext', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupContext', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownContext', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownContext', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupContext', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownContext', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupContext', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>
				 <?php $link =  "index.php?option=$option&amp;task=editContext&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>
				<td><?php echo $row->code; ?></td>
				<td align="center">
					<a href="<?php echo "index.php?option=$option&amp;task=listSearchCriteria&context_id=$row->id"; ?>" title="<?php echo JText::_( 'CATALOG_CONTEXT_LISTSEARCHCRITERIA' ); ?>">
						<img src="<?php echo JURI::root(true); ?>/includes/js/ThemeOffice/mainmenu.png" border="0" />
					</a>
				</td>
				<td><?php echo $row->description; ?></td>
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
	  	<input type="hidden" name="task" value="listContext" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
	}
	
	function editContext(&$row, $listObjectTypes, $fieldsLength, $languages, $labels, $titles, $sortfields, $objecttypes, $selected_objecttypes, $option)
	{
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 

		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td width=150><?php echo JText::_("CORE_NAME"); ?></td>
					<td colspan="2"><input size="50" type="text" name ="name" value="<?php echo $row->name?>" maxlength="<?php echo $fieldsLength['name'];?>"> </td>							
				</tr>
				<tr>
					<td width=150><?php echo JText::_("CORE_CODE"); ?></td>
					<td colspan="2"><input size="50" type="text" name ="code" value="<?php echo $row->code?>" maxlength="<?php echo $fieldsLength['code'];?>"> </td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?></td>
					<td colspan="2"><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description?></textarea></td>							
				</tr> 
				<tr>
					<td width=150 ><?php echo JText::_("CATALOG_CONTEXT_OBJECTTYPE"); ?></td>
					<td colspan="2">
						<?php
						if (count($objecttypes) > 0)
						{
							foreach($objecttypes as $objecttype)
							{
								?>
									<input size="50" type="checkbox" name ="objecttypes[]" value="<?php echo $objecttype->value?>" <?php echo in_array($objecttype->value, $selected_objecttypes)? 'checked="yes"':'';?>><?php echo $objecttype->text?></input>
								<?php
							} 
						}
						?>
					</td>
				</tr>
				<tr>
					<td width=150><?php echo JText::_("CATALOG_CONTEXT_XSLDIR"); ?></td>
					<td><input size="100" type="text" name ="xsldirectory" value="<?php echo $row->xsldirectory?>" maxlength="<?php echo $fieldsLength['xsldirectory'];?>"> </td>
					<td>
						<div style="font-weight: bold" >
							<img src="<?php echo JURI::root(true);?>/includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> <?php echo JText::_( 'CATALOG_CONTEXT_XSLDIR_TIP' ); ?>
						</div>
					</td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_CONTEXT_RUNINITSEARCH"); ?></td>
					<td><input size="50" type="checkbox" name ="runinitsearch" value="1" <?php echo $row->runinitsearch == "1"? 'checked="yes"':'';?>></input></td>							
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("CATALOG_SEARCHCRITERIA_CSW_FILTER"); ?></td>
					<td><textarea rows="4" cols="50" id="filter" name="filter"><?php  if(isset($row->filter)) echo $row->filter;?></textarea></td>
				</tr>
			</table>
			<table border="0" cellpadding="3" cellspacing="0">
					<tr>
					<td colspan="2">
						<fieldset id="sortfields">
							<legend align="top"><?php echo JText::_("CATALOG_CONTEXT_SORTFIELD"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="sortfield<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($sortfields[$lang->id])?>" maxlength="<?php echo $fieldsLength['sortfield'];?>"></td>							
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
			<table border="0" cellpadding="3" cellspacing="0">
					<tr>
					<td colspan="2">
						<fieldset id="labels">
							<legend align="top"><?php echo JText::_("CATALOG_CONTEXT_TITLE"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="title<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($titles[$lang->id])?>" maxlength="<?php echo $fieldsLength['title'];?>"></td>							
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