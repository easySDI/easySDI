<?php
/**
 *  EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
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

class HTML_simplesearch 
{
	function listSimpleSearch( $rows, $pageNav,$search, $filter_order_Dir, $filter_order, $option)
	{
		JToolBarHelper::title(JText::_("MAP_LIST_SIMPLE_SEARCH"), 'map.png');
		?>
		<form action="index.php" method="GET" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchSimpleSearch" id="searchSimpleSearch" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchSimpleSearch').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("MAP_SIMPLE_SEARCH_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_SIMPLE_SEARCH_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_SIMPLE_SEARCH_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
		</thead>
		<tbody>		
		<?php
		$k = 0;
		$i=0;
		foreach ($rows as $row)
		{		
		?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editSimpleSearch')"><?php echo $row->name; ?></a></td>		
				<td><?php echo $row->description; ?></td>			
			</tr>
		<?php
			$k = 1 - $k;
			$i++;
		}
		?>
		</tbody>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
	  	</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="simpleSearch" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  	</form>
		<?php		
	}
	
	function editSimpleSearch ($simpleSearch, $rowsFilters,$rowsSelectedFilter,$rowsResultGrid,$rowsSelectedGrid,$createUser, $updateUser,$fieldsLength, $option)
	{
		if ($simpleSearch->id != 0)
		{
			JToolBarHelper::title( JText::_("MAP_EDIT_SIMPLE_SEARCH").': <small><small>['. JText::_("CORE_EDIT").']</small></small>', 'addedit.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("MAP_EDIT_SIMPLE_SEARCH").': <small><small>['. JText::_("CORE_NEW").']</small></small>', 'addedit.png' );
		}
		?>			
		<script>	
		function submitbutton(pressbutton)
		{
			if(pressbutton == "saveSimpleSearch")
			{
				if (document.getElementById('code').value == "")
				{	
					alert ('<?php echo  JText::_( 'MAP_SIMPLESEARCH_CODE_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('dropdownfeaturetype').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_SIMPLESEARCH_FT_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('dropdowndisplayattr').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_SIMPLESEARCH_ATTR_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('dropdownidattr').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_SIMPLESEARCH_ID_ATTR_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('searchattribute').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_SIMPLESEARCH_SEARCH_ATTR_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('operator').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_SIMPLESEARCH_OPERATOR_VALIDATION_ERROR');?>');	
					return;
				}
				else
				{	
					submitform(pressbutton);
				}
			}
			else
			{
				submitform(pressbutton);
			}
		}
		</script>	
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<fieldset>						
							<table class="admintable">
								<tr>
									<td class="key" width="100p"><?php echo JText::_("MAP_SIMPLE_SEARCH_NAME"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['name'];?>" name="name" id="name" value="<?php echo $simpleSearch->name; ?>" /></td>								
								</tr>
								<tr>
									<td class="key" width="100p"><?php echo JText::_("MAP_SIMPLE_SEARCH_DESCRIPTION"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['description'];?>" name="description" id="description" value="<?php echo $simpleSearch->description; ?>" /></td>								
								</tr>
								<tr>
									<td class="key" width="100p"><?php echo JText::_("MAP_SIMPLE_SEARCH_CODE"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['code'];?>" name="code" id="code" value="<?php echo $simpleSearch->code; ?>" /></td>								
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("MAP_SIMPLE_SEARCH_DROPDOWN_FT"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['dropdownfeaturetype'];?>" name="dropdownfeaturetype" id="dropdownfeaturetype" value="<?php echo $simpleSearch->dropdownfeaturetype; ?>" /></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("MAP_SIMPLE_SEARCH_DROPDOWN_ATTR"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['dropdowndisplayattr'];?>" name="dropdowndisplayattr" id="dropdowndisplayattr" value="<?php echo $simpleSearch->dropdowndisplayattr; ?>" /></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("MAP_SIMPLE_SEARCH_DROPDOWN_ID_ATTR"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['dropdownidattr'];?>" name="dropdownidattr" id="dropdownidattr" value="<?php echo $simpleSearch->dropdownidattr; ?>" /></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("MAP_SIMPLE_SEARCH_SEARCH_ATTR"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['searchattribute'];?>" name="searchattribute" id="searchattribute" value="<?php echo $simpleSearch->searchattribute; ?>" /></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("MAP_SIMPLE_SEARCH_OPERATOR"); ?></td>
									<td><input class="inputbox" type="text" size="5" maxlength="<?php echo $fieldsLength['operator'];?>" name="operator" id="operator" value="<?php echo $simpleSearch->operator; ?>" /></td>
								</tr>							
							</table>
						</fieldset>
						<fieldset>						
							<table class="admintable">
								<tr>
									<td class="key"><?php echo JText::_("MAP_SIMPLE_SEARCH_ADD_FILTERS"); ?> : </td>
									<td><?php echo JHTML::_("select.genericlist",$rowsFilters, 'filter_id[]', 'size="5" multiple="true" class="selectbox"', 'value', 'text', $rowsSelectedFilter ); ?></td>
									<td><a href="./index.php?option=com_easysdi_map&task=newAdditionalFilter" > 
										<img class="helpTemplate" 
											 src="../templates/easysdi/icons/silk/add.png" 
											 alt="<?php echo JText::_("MAP_SIMPLE_SEARCH_NEW_ADD_FILTERS") ?>" 
											 />
									</a></td>
								</tr>
							</table>
						</fieldset>
						<fieldset>						
							<table class="admintable">
								<tr>
									<td class="key"><?php echo JText::_("MAP_SIMPLE_SEARCH_RESULT_GRID"); ?> : </td>
									<td><?php echo JHTML::_("select.genericlist",$rowsResultGrid, 'grid_id[]', 'size="5" multiple="true" class="selectbox"', 'value', 'text', $rowsSelectedGrid ); ?></td>
									<td><a href="./index.php?option=com_easysdi_map&task=newResultGrid" > 
										<img class="helpTemplate" 
											 src="../templates/easysdi/icons/silk/add.png" 
											 alt="<?php echo JText::_("MAP_SIMPLE_SEARCH_NEW_RESULT_GRID") ?>" 
											 />
									</a></td>
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>
			</table>
			<br></br>
			<table border="0" cellpadding="3" cellspacing="0">
			<?php
			if ($simpleSearch->created)
			{ 
			?>
				<tr>
					<td><?php echo JText::_("CORE_CREATED"); ?> : </td>
					<td><?php if ($simpleSearch->created) {echo date('d.m.Y h:i:s',strtotime($simpleSearch->created));} ?></td>
					<td>, </td>
					<td><?php echo $createUser; ?></td>
				</tr>
			<?php
			}
			if ($simpleSearch->updated and $simpleSearch->updated<> '0000-00-00 00:00:00')
			{ 
			?>
				<tr>
					<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
					<td><?php if ($simpleSearch->updated and $simpleSearch->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($simpleSearch->updated));} ?></td>
					<td>, </td>
					<td><?php echo $updateUser; ?></td>
				</tr>
			<?php
			}
			?>		
		</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" value="<?php echo $simpleSearch->id; ?>" />
			<input type="hidden" name="guid" value="<?php echo $simpleSearch->guid?>" />
			<input type="hidden" name="ordering" value="<?php echo $simpleSearch->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo $simpleSearch->created;?>" />
			<input type="hidden" name="createdby" value="<?php echo $simpleSearch->createdby; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo $simpleSearch->created; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo $simpleSearch->createdby; ?>" /> 
		</form>
		<?php
	}
	
	
	function listAdditionalFilter($rows, $pageNav,$search, $filter_order_Dir, $filter_order, $option)
	{
		JToolBarHelper::title(JText::_("MAP_LIST_ADD_FILTER"));
		?>
		<form action="index.php" method="GET" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchAdditionalFilter" id="searchAdditionalFilter" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchAdditionalFilter').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("MAP_ADD_FILTER_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_ADD_FILTER_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_ADD_FILTER_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>				
			</tr>
		</thead>
		<tbody>		
		<?php
		$k = 0;
		$i=0;
		foreach ($rows as $row)
		{		
		?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editAdditionalFilter')"><?php echo $row->name; ?></a></td>			
				<td><?php echo $row->description; ?></td>
			</tr>
		<?php
			$k = 1 - $k;
			$i++;
		}
		?>
		</tbody>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
	  	</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="additionalFilter" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  	</form>
		<?php		
	}
	
	function editAdditionalFilter ($additionalFilter,$createUser, $updateUser,$fieldsLength, $option)
	{
		if ($additionalFilter->id != 0)
		{
			JToolBarHelper::title( JText::_("MAP_EDIT_ADD_FILTER").': <small><small>['. JText::_("CORE_EDIT").']</small></small>', 'addedit.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("MAP_EDIT_ADD_FILTER").': <small><small>['. JText::_("CORE_NEW").']</small></small>', 'addedit.png' );
		}
		?>				
		<script>	
		function submitbutton(pressbutton)
		{
			if(pressbutton == "saveAdditionalFilter")
			{
				if (document.getElementById('attribute').value == "")
				{	
					alert ('<?php echo  JText::_( 'MAP_ADDFILTER_ATTRIBUTE_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('value').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_ADDFILTER_VALUE_VALIDATION_ERROR');?>');	
					return;
				}			
				else
				{	
					submitform(pressbutton);
				}
			}
			else
			{
				submitform(pressbutton);
			}
		}
		</script>	
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<fieldset>						
							<table class="admintable">
								<tr>
									<td class="key" width="100p"><?php echo JText::_("MAP_ADD_FILTER_NAME"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['name'];?>" name="name" id="name" value="<?php echo $additionalFilter->name; ?>" /></td>								
								</tr>
								<tr>
									<td class="key" width="100p"><?php echo JText::_("MAP_ADD_FILTER_DESCRIPTION"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['description'];?>" name="description" id="description" value="<?php echo $additionalFilter->description; ?>" /></td>								
								</tr>
								<tr>
									<td class="key" width="100p"><?php echo JText::_("MAP_ADD_FILTER_ATTRIBUTE"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['attribute'];?>" name="attribute" id="attribute" value="<?php echo $additionalFilter->attribute; ?>" /></td>								
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("MAP_ADD_FILTER_VALUE"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['value'];?>" name="value" id="value" value="<?php echo $additionalFilter->value; ?>" /></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("MAP_ADD_FILTER_OPERATOR"); ?></td>
									<td><input class="inputbox" type="text" size="5" maxlength="<?php echo $fieldsLength['operator'];?>" name="operator" id="operator" value="<?php echo $additionalFilter->operator; ?>" /></td>
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>
			</table>
			<br></br>
			<table border="0" cellpadding="3" cellspacing="0">
			<?php
			if ($additionalFilter->created)
			{ 
			?>
				<tr>
					<td><?php echo JText::_("CORE_CREATED"); ?> : </td>
					<td><?php if ($additionalFilter->created) {echo date('d.m.Y h:i:s',strtotime($additionalFilter->created));} ?></td>
					<td>, </td>
					<td><?php echo $createUser; ?></td>
				</tr>
			<?php
			}
			if ($additionalFilter->updated and $additionalFilter->updated<> '0000-00-00 00:00:00')
			{ 
			?>
				<tr>
					<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
					<td><?php if ($additionalFilter->updated and $additionalFilter->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($additionalFilter->updated));} ?></td>
					<td>, </td>
					<td><?php echo $updateUser; ?></td>
				</tr>
			<?php
			}
			?>		
			</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" value="<?php echo $additionalFilter->id; ?>" />
			<input type="hidden" name="guid" value="<?php echo $additionalFilter->guid?>" />
			<input type="hidden" name="ordering" value="<?php echo $additionalFilter->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo $additionalFilter->created;?>" />
			<input type="hidden" name="createdby" value="<?php echo $additionalFilter->createdby; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo $additionalFilter->created; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo $additionalFilter->createdby; ?>" /> 
		</form>
		<?php
	}
}
?>