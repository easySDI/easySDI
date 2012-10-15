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


class HTML_attribute {
function listAttribute(&$rows, $lists, $page, $option,  $filter_order_Dir, $filter_order)
	{
		$database =& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$ordering = ($filter_order == 'ordering');
?>
	<form action="index.php" method="POST" name="adminForm">
		<table>
			<tr>
				<td width="100%">
					<?php echo JText::_( 'Filter' ); ?>:
					<input type="text" name="searchAttribute" id="searchAttribute" value="<?php echo $lists['searchAttribute'];?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button onclick="document.getElementById('searchAttribute').value='';this.form.getElementById('filter_attributetype_id').value='-1';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
				</td>
				<td nowrap="nowrap">
					<?php
					echo $lists['attributetype_id'];
					?>
				</td>
			</tr>
		</table>
		
		
		<table class="adminlist">
		<thead>
			<tr>
				<th class='title' width="10px"><?php echo JText::_("CORE_SHARP"); ?></th>
				<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
				<!-- <th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderAttribute' ); ?></th> -->
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_ATTRIBUTE_ATTRIBUTETYPE"), 'attributetype_id', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_EDITLIST"), 'attributetype_id', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_ISOCODE"), 'attribute_isocode', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$i=0;
		foreach ($rows as $row)
		{
			$checked 	= JHTML::_('grid.checkedout',   $row, $i );		
?>
			<tr>
				<td align="center" width="10px"><?php echo $page->getRowOffset( $i );//echo $i+$page->limitstart+1;?></td>
				<!-- <td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php //echo $row->id; ?>" onclick="isChecked(this.checked);" /></td> -->
				<td align="center">
					<?php echo $checked; ?>
				</td>												
				<td width="30px" align="center"><?php echo $row->id; ?></td>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<!-- <td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupAttribute', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownAttribute', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupAttribute', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownAttribute', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownAttribute', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupAttribute', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownAttribute', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupAttribute', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>
	             -->
	            <?php $link =  "index.php?option=$option&amp;task=editAttribute&cid[]=$row->id";?>
				<td>
				<?php 
				if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
				{
					echo $row->name;
				} 
				else 
				{
					?>
					<a href="<?php echo $link;?>"><?php echo $row->name; ?></a>
					<?php
				}
				?>
				</td>
				<td><?php echo $row->attributetype_name; ?></td>
				<td align="center">
					<?php $link =  "index.php?option=$option&amp;task=listCodeValue&attribute_id=$row->id";?>
					<?php 
					if ($row->attributetype_code == 'list' or $row->attributetype_code == 'textchoice' or $row->attributetype_code == 'localechoice'){
					?>
						<?php 
						if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
						{
							?>
							<img src="<?php echo JURI::root(true); ?>/includes/js/ThemeOffice/mainmenu.png" border="0" />
							<?php
						} 
						else 
						{
							?>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_( 'CATALOG_ATTRIBUTE_EDIT_LIST_ITEMS' ); ?>">
								<img src="<?php echo JURI::root(true); ?>/includes/js/ThemeOffice/mainmenu.png" border="0" />
							</a>
							<?php
						}
						?>
					<?php 
					}
					?>
				</td>
				<td><?php echo htmlspecialchars($row->attribute_isocode); ?></td>
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
				<td colspan="12">
					<?php echo $page->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	  	</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listAttribute" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
	}
	
	//function editAttribute(&$row, $attributetypelist, $fieldsLength, $languages, $style, $defaultStyle, $defaultStyle_Radio, $defaultStyle_Date, $defaultStyle_Locale, $codevalues ,$option)
	function editAttribute(&$row, $attributetypelist, $fieldsLength, $style, $styleAttributes, $languages, $informations, $regexmsgs, $namespacelist, $option)
	{
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 

		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td WIDTH=150><?php echo JText::_("CORE_NAME"); ?></td>
					<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>" maxlength="<?php echo $fieldsLength['name'];?>"> </td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?></td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description?></textarea></td>
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_ISOCODE");?></td>
					<td>
						<?php echo JHTML::_("select.genericlist",$namespacelist, 'namespace_id', 'size="1" class="inputbox"', 'value', 'text', $row->namespace_id ); ?>
						<input size="50" type="text" name ="isocode" value="<?php echo $row->isocode?>" maxlength="<?php echo $fieldsLength['isocode'];?>"> 
					</td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_ATTRIBUTE_STEREOTYPE"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$attributetypelist, 'attributetype_id', 'size="1" class="inputbox" onchange="javascript:changeAttributeListVisibility(this.value);"', 'value', 'text', $row->attributetype_id ); ?></td>							
				</tr>
			</table>
			<div id = "div_isocodeType" style="<?php echo $style; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td WIDTH=150><?php echo JText::_("CATALOG_TYPE_ISOCODE"); ?></td>
					<td>
						<?php echo JHTML::_("select.genericlist",$namespacelist, 'listnamespace_id', 'size="1" class="inputbox"', 'value', 'text', $row->listnamespace_id ); ?>
						<input size="50" type="text" name ="type_isocode" value="<?php echo $row->type_isocode?>" maxlength="<?php echo $fieldsLength['type_isocode'];?>"> 
					</td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_CODELIST"); ?></td>
					<td><input size="50" type="text" name ="codeList" value="<?php echo $row->codeList?>" maxlength="<?php echo $fieldsLength['codeList'];?>"> </td>							
				</tr>		
			</table>
			</div>
			<div id = "div_attributes" style="<?php echo $styleAttributes; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td WIDTH=150><?php echo JText::_("CATALOG_LENGTH"); ?></td>
					<td><input size="50" type="text" name ="length" value="<?php echo $row->length?>" > </td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_PATTERN"); ?></td>
					<td><textarea cols="50" rows="5" name ="pattern" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['pattern'];?>);"><?php echo $row->pattern?></textarea></td>							
				</tr>
				<tr>
					<td colspan="2">
						<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_REGEX_MSG"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td width=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="regexmsg<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($regexmsgs[$lang->id])?>" maxlength="<?php echo $fieldsLength['regexmsg'];?>"></td>							
					</tr>
<?php
}
?>
							</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<table border="0" cellpadding="3" cellspacing="0">	
				
				<tr>
					<td colspan="2">
						<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_INFORMATION"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td width=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="information<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($informations[$lang->id])?>" maxlength="<?php echo $fieldsLength['information'];?>"></td>							
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
			<input type="hidden" name="ordering" value="<?php echo $row->ordering;?>" />
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